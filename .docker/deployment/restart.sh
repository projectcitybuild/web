#!/usr/bin/env bash
set -Eeuo pipefail

environment=${1:?Environment must be staging or production}
image_tag=${2:?Image tag is required}
image_repo=${3:?Image repository is required}
deploy_path=${4:?Deploy path is required}

case "$environment" in
    staging|production) ;;
    *) echo "Unsupported environment: $environment" >&2; exit 2 ;;
esac
case "$image_tag" in
    *[!a-f0-9]*) echo "Image tag must be a lowercase hexadecimal SHA" >&2; exit 2 ;;
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
if [[ ${release_root##*/} != "$environment" || $release_tag != "$image_tag" ]]; then
    echo "Deploy path must be DEPLOY_PATH/$environment/$image_tag" >&2
    exit 2
fi

app_env_file="/etc/pcb-web/${environment}.env"
compose_base="$deploy_path/compose.yaml"
compose_server="$deploy_path/compose.server.yaml"
route_file="$deploy_root/proxy/sites/${environment}.caddy"
infra_project="pcb-${environment}-infra"

for file in "$app_env_file" "$compose_base" "$compose_server" "$route_file"; do
    if [[ ! -f $file ]]; then
        echo "Missing required restart file: $file" >&2
        exit 1
    fi
done

exec 9>"$deploy_root/.deploy.lock"
flock -x 9

blue_route=0
green_route=0
grep -Fq "reverse_proxy ${environment}-blue:80" "$route_file" && blue_route=1 || true
grep -Fq "reverse_proxy ${environment}-green:80" "$route_file" && green_route=1 || true
if [[ $blue_route -eq $green_route ]]; then
    echo "Could not identify exactly one active slot from $route_file" >&2
    exit 1
fi
if [[ $blue_route -eq 1 ]]; then
    active_slot=blue
else
    active_slot=green
fi

project="pcb-${environment}-${active_slot}"
network="pcb-${environment}"
proxy_network=pcb-proxy

export ENVIRONMENT="$environment"
export DEPLOY_SLOT="$active_slot"
export APP_ENV_FILE="$app_env_file"
export IMAGE_REPO="$image_repo"
export IMAGE_TAG="$image_tag"

compose() {
    local project_name=$1
    shift
    docker compose --env-file "$app_env_file" \
        --project-name "$project_name" \
        -f "$compose_base" -f "$compose_server" "$@"
}

on_exit() {
    local status=$?
    if [[ $status -ne 0 ]]; then
        echo "Restart failed for ${environment} (${active_slot}); collecting diagnostics" >&2
        compose "$infra_project" ps >&2 || true
        compose "$infra_project" logs --no-color --tail=100 mariadb redis >&2 || true
        compose "$project" ps >&2 || true
        compose "$project" logs --no-color --tail=100 >&2 || true
    fi
    exit "$status"
}
trap on_exit EXIT

for required_network in "$network" "$proxy_network"; do
    if ! docker network inspect "$required_network" >/dev/null 2>&1; then
        echo "Required Docker network does not exist: $required_network" >&2
        exit 1
    fi
done
for required_volume in \
    "pcb-${environment}-database" \
    "pcb-${environment}-storage" \
    "pcb-${environment}-redis"; do
    if ! docker volume inspect "$required_volume" >/dev/null 2>&1; then
        echo "Required Docker volume does not exist: $required_volume" >&2
        exit 1
    fi
done

echo "Recreating MariaDB and Redis for ${environment}"
compose "$infra_project" up -d --force-recreate --wait --wait-timeout 120 mariadb redis

echo "Recreating ${active_slot} application services for ${environment}"
compose "$project" up -d --no-deps --force-recreate --wait --wait-timeout 120 \
    fpm nginx queue scheduler

echo "Restarted ${environment} (${active_slot}) without changing the proxy route"
