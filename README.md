COVID-19 - DSP declarations management
======================================

[![Conventional Commits][conventional-commits-image]][conventional-commits-url]

This project is about developing a web interface for management of COVID-19 private declarations.

**Project stack**  
* PHP 7.2
* MySQL 5.7
* Laravel 7.3.0
* Composer

## Install and run on local machine

### Prerequisites
Before start install, user will need:
* One [Bionic Beaver][1], and a non-root user with `sudo` privileges.
* [Composer][2]
* [Docker][3]
* [Docker Compose][4]

### Install local
* Clone repository
```shell script
git clone git@github.com:citizennext/covid-19-entry-dsp.git covid-19-entry-dsp
```
* Move into the `covid-19-entry-dsp` directory
```shell script
cd ~/covid-19-entry-dsp
```
* Mount the directories that you will need for your Laravel project and avoid the overhead of installing Composer
 globally
 ```shell script
docker run --rm -v $(pwd):/app composer install
```
* Set permissions on the project directory so that it is owned by your non-root user
```shell script
sudo chown -R $USER:$USER ~/covid-19-entry-dsp
```
* Make a copy of the `.env.example` file that Laravel includes by default and name the copy `.env`
```shell script
cp .env.example .env
```
* Open the file using nano or your text editor of choice
```shell script
nano .env
```
* Modify, add and remove to reflect the specifics of your setup
```shell script
APP_NAME="COVID-19 - DSP declarations management"

DB_HOST=covid19-dsp-db
DB_DATABASE=laravel
DB_USERNAME=laravel
DB_PASSWORD=laravel
DB_ROOT_PASSWORD=toor

MAIL_MAILER=smtp
MAIL_HOST=0.0.0.0
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
#MAIL_FROM_ADDRESS=null
#MAIL_FROM_NAME="${APP_NAME}"
```

### Run local
* Run the `sh` script which deploy Docker environment. Use option `-h` - help in order to check all parameters
```shell script
./run.sh -m -t
```
* Check if all Docker containers are running
```shell script
docker container ls
```
```shell script
b874899e1356        phpmyadmin/phpmyadmin   "/docker-entrypoint.…"   13 hours ago        Up 2 minutes        0.0.0.0:8081->80/tcp                             covid19-dsp-phpmyadmin
926590434ca2        mysql:5.7.22            "docker-entrypoint.s…"   13 hours ago        Up 2 minutes        0.0.0.0:3306->3306/tcp                           covid19-dsp-db
cbf25f7688cf        nginx:alpine            "nginx -g 'daemon of…"   13 hours ago        Up 2 minutes        0.0.0.0:80->80/tcp, 0.0.0.0:443->443/tcp         covid19-dsp-webserver
f13affc27734        adminer                 "entrypoint.sh docke…"   13 hours ago        Up 2 minutes        0.0.0.0:8080->8080/tcp                           covid19-dsp-adminer
90dd4b6b8dc3        redis:5.0.0-alpine      "docker-entrypoint.s…"   13 hours ago        Up 2 minutes        0.0.0.0:6379->6379/tcp                           covid19-dsp-redis
4a33b9341bfe        covid19_dsp/php         "docker-php-entrypoi…"   13 hours ago        Up 2 minutes        9000/tcp                                         covid19-dsp-app
b00db15a073c        mailhog/mailhog         "MailHog"                13 hours ago        Up 2 minutes        0.0.0.0:1025->1025/tcp, 0.0.0.0:8025->8025/tcp   covid19-dsp-mailhog
```
* Run in `covid19-dsp-app` container `composer install`
```shell script
docker container exec -it covid19-dsp-app composer install
```
* Generate a key and copy it to your `.env` file, ensuring that your user sessions and encrypted data remain secure
```shell script
docker container exec -it covid19-dsp-app php artisan key:generate
```
* Cache these settings into a file
```shell script
docker container exec -it covid19-dsp-app php artisan config:cache
```
* Now user will be able to check it in browser
```shell script
http://localhost/         - main app
http://localhost:8080/    - adminer (use for Server - covid19-dsp-db, User - root)
http://localhost:8081/    - phpmyadmin
http://localhost:8025/    - MailHog 
```
in Docker running terminal user could check Redis
```shell script
...
covid19-dsp-redis         | 1:M 29 Mar 2020 07:31:34.362 * DB loaded from disk: 0.000 seconds
covid19-dsp-redis         | 1:M 29 Mar 2020 07:31:34.362 * Ready to accept connections
...
```
* Open the `covid19-dsp-db` container and log into the MySQL root administrative account
```shell script
docker container exec -it covid19-dsp-db mysql -u root -p
```
* Create the user account that will be allowed to access this database and flush the privileges to notify the MySQL
 server of the changes
 ```mysql
mysql> GRANT ALL ON laravel.* TO 'laravel'@'%' IDENTIFIED BY 'laravel';
mysql> FLUSH PRIVILEGES;
mysql> EXIT;
```
* Migrate the data and check it with `tinker`
```shell script
docker container exec -it covid19-dsp-app php artisan migrate

Migration table created successfully.
Migrating: 2014_10_12_000000_create_users_table
Migrated:  2014_10_12_000000_create_users_table (0.1 seconds)
Migrating: 2019_08_19_000000_create_failed_jobs_table
Migrated:  2019_08_19_000000_create_failed_jobs_table (0.05 seconds)
```
```shell script
docker container exec -it covid19-dsp-app php artisan tinker
Psy Shell v0.10.2 (PHP 7.2.29 — cli) by Justin Hileman
>>> \DB::table('migrations')->get();
=> Illuminate\Support\Collection {#3012
     all: [
       {#3010
         +"id": 1,
         +"migration": "2014_10_12_000000_create_users_table",
         +"batch": 1,
       },
       {#3019
         +"id": 2,
         +"migration": "2019_08_19_000000_create_failed_jobs_table",
         +"batch": 1,
       },
     ],
   }
>>> 
```
* Enjoy!


[conventional-commits-image]: https://img.shields.io/badge/Conventional%20Commits-1.0.0-yellow.svg
[conventional-commits-url]: https://conventionalcommits.org/
[1]: http://releases.ubuntu.com/18.04.4/
[2]: https://www.digitalocean.com/community/tutorials/how-to-install-and-use-composer-on-ubuntu-18-04
[3]: https://www.digitalocean.com/community/tutorials/how-to-install-and-use-docker-on-ubuntu-18-04
[4]: https://www.digitalocean.com/community/tutorials/how-to-install-docker-compose-on-ubuntu-18-04
