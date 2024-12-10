<?php

namespace Deployer;

require 'recipe/laravel.php';

// Config

set('repository', 'https://github.com/projectcitybuild/web.git');

add('shared_files', []);
add('shared_dirs', []);
add('writable_dirs', []);

// Hosts

host(getenv('DEPLOY_HOST'))
    ->set('remote_user', getenv('DEPLOY_USER'))
    ->set('deploy_path', getenv('DEPLOY_PATH'))
    ->set('branch', getenv('DEPLOY_BRANCH'));

// Hooks

after('deploy:failed', 'deploy:unlock');

// Additional tasks

// The Laravel recipe doesn't build any frontend assets, so we'll
// need to do it ourselves here
task('deploy:frontend', function () {
    run('npm install');
    run('npm run build');
})->desc('Build frontend assets');

after('deploy:vendors', 'deploy:frontend');
