#!/bin/sh
set -eu

environment=${1:?Environment must be staging or production}
image_tag=${2:?Image tag is required}
domain=${3:?Application domain is required}
image_repo=${4:?Image repository is required}
deploy_path=${5:?Deploy path is required}

case "$environment" in
    staging|production) ;;
    *) echo "Unsupported environment: $environment" >&2; exit 2 ;;
esac
case "$image_tag" in
    *[!a-f0-9]*) echo "Image tag must be a lowercase hexadecimal SHA" >&2; exit 2 ;;
esac
case "$domain" in
    *[!A-Za-z0-9.-]*|.*|*.) echo "Invalid application domain" >&2; exit 2 ;;
esac
case "$image_repo" in
    ghcr.io/*) ;;
    *) echo "Image repository must be hosted on ghcr.io" >&2; exit 2 ;;
esac
case "$deploy_path" in
    /*) ;;
    *) echo "Deploy path must be absolute" >&2; exit 2 ;;
esac

release_root=$(dirname "$deploy_path")
deploy_root=$(dirname "$release_root")
release_tag=${deploy_path##*/}
if [ "${release_root##*/}" != "$environment" ] || [ "$release_tag" != "$image_tag" ]; then
    echo "Deploy path must be DEPLOY_PATH/$environment/$image_tag" >&2
    exit 2
fi
proxy_root="$deploy_root/proxy"
app_env_file="/etc/pcb-web/${environment}.env"
proxy_env_file=/etc/pcb-web/proxy.env
compose_base="$deploy_path/compose.yaml"
compose_server="$deploy_path/compose.server.yaml"
proxy_compose="$deploy_path/proxy-compose.yaml"
proxy_caddyfile="$deploy_path/Caddyfile"
route_file="$proxy_root/sites/${environment}.caddy"
default_route="$proxy_root/sites/00-default.caddy"
network="pcb-${environment}"
proxy_network=pcb-proxy
new_slot=green
switched=0
scheduler_switched=0
rollback_failed=0

if [ ! -f "$app_env_file" ] || [ ! -f "$proxy_env_file" ]; then
    echo "Missing host environment file(s) under /etc/pcb-web" >&2
    exit 1
fi
for file in "$compose_base" "$compose_server" "$proxy_compose" "$proxy_caddyfile"; do
    if [ ! -f "$file" ]; then
        echo "Missing deployment file: $file" >&2
        exit 1
    fi
done

mkdir -p "$deploy_root" "$release_root" "$proxy_root/sites"
exec 9>"$deploy_root/.deploy.lock"
flock -x 9

if [ -f "$route_file" ]; then
    if grep -q "reverse_proxy ${environment}-green:80" "$route_file"; then
        new_slot=blue
    fi
    old_route=$(cat "$route_file")
    had_old_route=1
else
    old_route=
    had_old_route=0
fi
new_alias="$environment-$new_slot"
project="pcb-${environment}-${new_slot}"
old_project="pcb-${environment}-$(if [ "$new_slot" = blue ]; then echo green; else echo blue; fi)"

export ENVIRONMENT="$environment"
export DEPLOY_SLOT="$new_slot"
export APP_ENV_FILE="$app_env_file"
export IMAGE_REPO="$image_repo"
export IMAGE_TAG="$image_tag"

compose() {
    project_name=$1
    shift
    docker compose --env-file "$app_env_file" \
        --project-name "$project_name" \
        -f "$compose_base" -f "$compose_server" "$@"
}

restore_route() {
    if [ "$had_old_route" -eq 1 ]; then
        printf '%s\n' "$old_route" > "$route_file"
    else
        rm -f "$route_file"
    fi
    if ! DEPLOY_ROOT="$deploy_root" docker compose --env-file "$proxy_env_file" \
        --project-name pcb-proxy -f "$proxy_compose" \
        exec -T caddy caddy reload --config /etc/caddy/Caddyfile >/dev/null 2>&1; then
        echo "Failed to reload the restored proxy route; preserving candidate services to avoid interrupting traffic" >&2
        rollback_failed=1
    fi
}

cleanup() {
    status=$?
    if [ "$status" -ne 0 ]; then
        echo "Deployment failed for ${environment} (${new_slot}); collecting candidate diagnostics" >&2
        compose "$project" ps >&2 || true
        compose "$project" logs --no-color --tail=100 >&2 || true
        if [ "$switched" -eq 1 ]; then
            restore_route
        fi
        if [ "$scheduler_switched" -eq 1 ] && [ "$rollback_failed" -eq 0 ]; then
            compose "$project" stop --timeout 30 scheduler >/dev/null 2>&1 || true
            if [ "$had_old_route" -eq 1 ]; then
                compose "$old_project" up -d --no-deps queue scheduler >/dev/null 2>&1 || true
            fi
        fi
        if [ "$rollback_failed" -eq 0 ]; then
            compose "$project" down --remove-orphans >/dev/null 2>&1 || true
        else
            echo "Candidate project ${project} was left running for traffic continuity; inspect proxy state before cleanup" >&2
        fi
    fi
    docker logout ghcr.io >/dev/null 2>&1 || true
    exit "$status"
}
trap cleanup EXIT HUP INT TERM
trap 'exit 129' HUP
trap 'exit 130' INT
trap 'exit 143' TERM

docker network inspect "$network" >/dev/null 2>&1 || docker network create "$network" >/dev/null
docker network inspect "$proxy_network" >/dev/null 2>&1 || docker network create "$proxy_network" >/dev/null
docker volume create "pcb-${environment}-database" >/dev/null
docker volume create "pcb-${environment}-storage" >/dev/null
docker volume create "pcb-${environment}-redis" >/dev/null

mkdir -p "$proxy_root/sites"
cp "$proxy_caddyfile" "$proxy_root/Caddyfile.new"
mv "$proxy_root/Caddyfile.new" "$proxy_root/Caddyfile"
if [ ! -f "$default_route" ]; then
    printf ':8080 {\n    respond "Not found" 404\n}\n' > "$default_route"
fi
DEPLOY_ROOT="$deploy_root" docker compose --env-file "$proxy_env_file" \
    --project-name pcb-proxy -f "$proxy_compose" up -d --wait --wait-timeout 90 caddy

echo "Waiting for MariaDB and Redis to become healthy for ${environment}"
if ! compose "pcb-${environment}-infra" up -d --wait --wait-timeout 120 mariadb redis; then
    echo "MariaDB or Redis did not become healthy within 120 seconds; aborting deployment" >&2
    compose "pcb-${environment}-infra" ps >&2 || true
    compose "pcb-${environment}-infra" logs --no-color --tail=100 mariadb redis >&2 || true
    exit 1
fi
echo "MariaDB and Redis are healthy"

echo "Pulling candidate images for ${new_slot}"
compose "$project" pull fpm nginx queue scheduler
echo "Starting candidate PHP-FPM and nginx services"
compose "$project" up -d --no-deps --wait --wait-timeout 120 fpm nginx
compose "$project" exec -T --user root fpm chown -R www-data:www-data storage
echo "Running database migrations against healthy MariaDB"
compose "$project" exec -T --user www-data fpm php artisan migrate --force
echo "Checking candidate HTTP health endpoint"
if ! compose "$project" exec -T nginx wget -q -O /dev/null http://127.0.0.1/up; then
    echo "Candidate ${new_alias} failed its HTTP health check; traffic remains on the existing route" >&2
    exit 1
fi

if [ "$had_old_route" -eq 1 ]; then
    scheduler_switched=1
    compose "$old_project" stop --timeout 30 scheduler
fi
scheduler_switched=1
compose "$project" up -d --no-deps queue scheduler
scheduler_id=$(compose "$project" ps -q scheduler)
if [ -z "$scheduler_id" ] || [ "$(docker inspect --format '{{.State.Running}}' "$scheduler_id")" != true ]; then
    echo "New scheduler did not start" >&2
    exit 1
fi

{
    printf '%s {\n    reverse_proxy %s:80\n}\n' "$domain" "$new_alias"

    if [ "$environment" = production ] && [ "$domain" = projectcitybuild.com ]; then
        printf 'api.%s {\n    reverse_proxy %s:80\n}\n' "$domain" "$new_alias"
    fi
} > "$route_file.new"

switched=1
mv "$route_file.new" "$route_file"
DEPLOY_ROOT="$deploy_root" docker compose --env-file "$proxy_env_file" \
    --project-name pcb-proxy -f "$proxy_compose" \
    exec -T caddy caddy reload --config /etc/caddy/Caddyfile

if docker ps -aq --filter "label=com.docker.compose.project=${old_project}" | grep -q .; then
    compose "$old_project" stop --timeout 90
fi

switched=0
scheduler_switched=0
echo "Deployed ${image_tag} to ${environment} (${new_slot})"

for old_release in "$release_root"/*; do
    [ -d "$old_release" ] || continue
    old_release_tag=${old_release##*/}
    case "$old_release_tag" in
        *[!a-f0-9]*|'') continue ;;
    esac
    [ "$old_release_tag" = "$image_tag" ] && continue
    if ! rm -rf "$old_release"; then
        echo "Warning: failed to remove old deployment directory: $old_release" >&2
    fi
done
rm -f "$release_root/.current" "$release_root/.current.new" || true

if ! docker image prune --all --force; then
    echo "Warning: failed to prune unused Docker images" >&2
fi
