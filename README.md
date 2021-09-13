The Nginx configuration can be found in `config/nginx/`.<br>

X10<br>
installed<br>
<ul>
    <li>swagger</li>
    <li>passport</li>
    <li>cors</li>
    <li>Intervention\Image</li>
    <li>doctrine/dbal</li>
    <li>socialite</li>
    <li>laravel/ui</li>
    <li>jeroennoten/laravel-adminlte</li>
    <li>user crud</li>
    <li>barryvdh/laravel-ide-helper</li>
</ul>
<br>
<br> remove Docker proxy
added the following in /etc/docker/daemon.json and restarted docker:
{
    "userland-proxy": false
}


<br>
upload laravel <br> 
up docker's items - docker-compose build<br><br>
need rights to write to public folder
docker container exec -it laravel-php bash //enter to php container
<br>
composer install<br>
php artisan migrate <br>
php artisan db:seed   //create admin user  test@test.com  123456<br>
php artisan passport:install  //Personal access client not found. Please create one.<br>
php artisan key:generate // No Application Encryption Key Has Been Specified  - <br>
<br><br>
AuthServiceProvider  passport settings
<br>


php myadmin http://server:8080/index.php<br>
server   laravel-mariadb<br>
user  laravel<br>
password laravel<br><br>


socials views  https://domen/socials<br>
socials settings in config/servises.php<br>

<h1>Deployer</h1> <br>
Links: <br>
- https://deployer.org/download/ <br>
- https://deployer.org/docs/how-to-deploy-laravel.html

Steps:
- install deployer with 
    - `curl -LO https://deployer.org/deployer.phar`
    - `mv deployer.phar /usr/local/bin/dep`
    - `chmod +x /usr/local/bin/dep`
- Place deploy.php ( or deploy-docker.php ( **needs to be renamed to deploy.php** ) if you're using docker) to your project root folder
- Set up deploy.php parameters, for example [check this documentation page](https://deployer.org/docs/how-to-deploy-laravel.html)
- Run `dep deploy` to deploy your project
- Configure you server to serve files from current. For example if you are using nginx next: 
```
server {
  listen 80;
  server_name domain.org;

  root /var/www/html/current/public;

  location / {
    try_files $uri /index.php$is_args$args;
  }
}
```

