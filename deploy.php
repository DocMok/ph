<?php
namespace Deployer;

require 'recipe/laravel.php';

// Project name
set('application', 'your app name here');

set('ssh-type','native');
// Project repository
set('repository', 'your git repository here');

// [Optional] Allocate tty for git clone. Default value is false.
set('git_tty', true);

set('writable_mod','chmod 765');

// Shared files/dirs between deploys 
add('shared_files', ['.env']);
add('shared_dirs', ['storage']);

// Writable dirs by web server 
add('writable_dirs', [
    'bootstrap/cache',
    'storage',
    'storage/app',
    'storage/app/public',
    'storage/framework',
    'storage/framework/cache',
    'storage/framework/sessions',
    'storage/framework/views',
    'storage/logs',
]);


set('keep_releases',3);



// Hosts

host('your domain or ip here')
    ->user('root')
    ->set('deploy_path', '/var/www/');
    
// Tasks

task('upload:env', function () {
    upload('.env', '{{deploy_path}}/shared/.env');
})->desc('Environment setup');


task('deploy', [
    'deploy:prepare',
    'deploy:lock',
    'deploy:release',
    'deploy:update_code',
    'upload:env',
    'deploy:shared',
    'deploy:vendors',
    'deploy:writable',
    'artisan:storage:link',
    'artisan:view:clear',
    'artisan:cache:clear',
    'artisan:config:cache',
    'artisan:migrate',
    'deploy:symlink',
    'deploy:unlock',
    'cleanup',
]);

// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');

// Migrate database before symlink new release.

before('deploy:symlink', 'artisan:migrate');

