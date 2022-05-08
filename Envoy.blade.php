@servers(['localhost' => '127.0.0.1'])

@setup
    require __DIR__.'/vendor/autoload.php';
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load(__DIR__, '.env');

    $branch = env('DEPLOY_BRANCH', 'master');
@endsetup

@task('deploy')
    php artisan down

    echo "=> Pulling from ".$branch;
    git pull origin {{ $branch }}

    composer install --no-interaction --prefer-dist --optimize-autoloader

    npm install --no-audit --no-fund --no-optional
    npm run production

    php artisan migrate --force

    php artisan cache:clear
    php artisan config:clear
    php artisan view:clear

{{--    php artisan config:cache--}}
{{--    php artisan route:cache--}}

    php artisan queue:restart

    php artisan up
@endtask

@error
    @discord(env('DEPLOY_DISCORD_WEBHOOK_URL'), 'Deployment failed')
@enderror

@before
    @discord(env('DEPLOY_DISCORD_WEBHOOK_URL'), 'Deployment starting...')
@endbefore

@success
    @discord(env('DEPLOY_DISCORD_WEBHOOK_URL'), 'Deployment succeeded')
@endsuccess
