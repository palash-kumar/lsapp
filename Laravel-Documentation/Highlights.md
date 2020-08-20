To import resources without managing it manually we will use composer to do it for us. To use UI in this project such as .css or .scss or saas we require to import Laravel's UI library:
```shell
composer require laravel/ui
```
After successfully installing the package, we install Bootstrap 4 in our application using the following command:
```shell
php artisan ui bootstrap
```
Finally, you need to install the bootstrap package and the related frontend dependencies such as jquery from npm using the following command and then run dev command to compile the new resources:
```shell
npm install

npm run dev
```
Now bootstrap is integrated to our project.

For authentication we need a user to login. And the Users are registered or stored in a table **USERS** in our DB. Now Laravel creates a Table or model named **users** with basic fields id, name, email, password and required timestap along with migration when we cerate a new project, and than when we use the migrate command it migrates the *users* model to our DB. No for now we have to enable authentication for our application. To enable authentication we will use the following command:
```shell
php artisan make:auth
```
The above command works only for Laravel version 5.x and below. So for the updated versions we will be using the following commands:
```shell
composer require laravel/ui --dev

php artisan ui vue --auth
```
Once it's done than we will have to run the following command as instructed while installing the *auth* using the previous command.
```shell
npm install

npm run dev
```