<?php

namespace Deployer;

require 'recipe/laravel.php';
require 'contrib/discord.php';

function configure(): void
{
    set('repository', 'https://github.com/projectcitybuild/web.git');

    add('shared_files', []);
    add('shared_dirs', []);
    add('writable_dirs', []);

    host(getenv('DEPLOY_HOST'))
        ->set('remote_user', getenv('DEPLOY_USER'))
        ->set('deploy_path', getenv('DEPLOY_PATH'))
        ->set('branch', getenv('DEPLOY_BRANCH'))
        ->set('discord_channel', getenv('DEPLOY_DISCORD_CHANNEL_ID'))
        ->set('discord_token', getenv('DEPLOY_DISCORD_CHANNEL_TOKEN'));

    after('deploy:failed', 'deploy:unlock');
}

function setupDiscordTasks(): void
{
    before('deploy', 'discord:notify');
    after('deploy:success', 'discord:notify:success');
    after('deploy:failed', 'discord:notify:failure');
}

function setupFrontendTasks(): void
{
    // The Laravel recipe doesn't build any frontend assets, so we need
    // to run our frontend tasks after dependencies are installed
    after('deploy:vendors', 'deploy:frontend');

    // NVM doesn't play well with deployer
    // https://github.com/deployphp/deployer/issues/2535
    //
    // Every command related tp npm must call `use_nvm` so that it's
    // sourced in the run command's shell
    set('use_nvm', 'source $HOME/.nvm/nvm.sh');

    task('deploy:frontend', function () {
        run('{{use_nvm}} && cd {{release_path}} && npm ci');
        run('{{use_nvm}} && cd {{release_path}} && npm run build', timeout: 600);
    })->desc('Build frontend assets');
}

function setupApiTasks(): void
{
    after('deploy:vendors', 'deploy:openapi');

    task('deploy:openapi', function () {
        run('php artisan scramble:export --path=public/api.json');
    })->desc('Generate api.json specification');
}

function setupSiteMap(): void
{
    run('php artisan sitemap:generate');
}

configure();
setupDiscordTasks();
setupFrontendTasks();
setupApiTasks();
setupSiteMap();
