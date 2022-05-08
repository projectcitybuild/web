@servers(['localhost' => '127.0.0.1'])

@setup
    require __DIR__.'/vendor/autoload.php';
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load(__DIR__, '.env');

    $branch = env('DEPLOY_BRANCH', 'master');
@endsetup

@task('deploy')
    php artisan down

    git pull --force origin {{ $branch }}

    composer install --no-interaction --quiet --no-dev --prefer-dist --optimize-autoloader
    php artisan migrate --force

    npm install --no-audit --no-fund --no-optional
    npm run production

    php artisan cache:clear
    php artisan config:clear
    php artisan view:clear

    php artisan queue:restart
    php artisan up
@endtask
