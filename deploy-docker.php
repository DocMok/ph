<?php
namespace Deployer;


require 'recipe/laravel.php';

// Project name
set('application', 'your application name here');

set('ssh-type','native');
// Project repository
set('repository', 'your git here');

// [Optional] Allocate tty for git clone. Default value is false.
set('git_tty', true);

set('writable_mod','chmod');

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

host('your ip or domain here')
    ->user('root')
    ->set('deploy_path', '/var/www/laravel');

// Tasks


task('execphp', function () {
    run('cd {{deploy_path}}/release/laravel && ls && docker exec laravel-php composer install --verbose --prefer-dist --no-progress --no-interaction --no-dev --optimize-autoloader --no-suggest;');
});

task('upload:env', function () {
    upload('.env.production', '{{deploy_path}}/shared/.env');
})->desc('Environment setup');


task('artisan:migrate',function (){
    run('docker exec laravel-php php artisan migrate');
});

task('artisan:storage:link',function (){
    run('docker exec laravel-php php artisan storage:link ');
});

task('artisan:view:clear',function (){
    run('docker exec laravel-php php artisan view:clear ');
});
task('artisan:cache:clear',function (){
    run('docker exec laravel-php php artisan cache:clear ');
});

task('artisan:config:clear',function (){
    run('docker exec laravel-php php artisan config:clear ');
});

task('artisan:config:cache',function (){
    run('docker exec laravel-php php artisan config:cache ');
});

task('deploy', [
    'deploy:prepare',
    'deploy:lock',
    'deploy:release',
    'deploy:update_code',
    'upload:env',
    'deploy:shared',
    'execphp',
    'deploy:writable',
    'artisan:storage:link',
    'artisan:view:clear',
    'artisan:cache:clear',
    'artisan:config:clear',
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

