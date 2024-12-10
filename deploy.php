<?php

namespace Deployer;

require 'recipe/laravel.php';

// Config

set('repository', 'git@github.com:projectcitybuild/web.git');

add('shared_files', []);
add('shared_dirs', []);
add('writable_dirs', []);

// Hosts

host(getenv('DEPLOY_HOST'))
    ->set('remote_user', fn () => getenv('DEPLOY_USER'))
    ->set('deploy_path', fn () => getenv('DEPLOY_PATH'));

// Hooks

after('deploy:failed', 'deploy:unlock');
