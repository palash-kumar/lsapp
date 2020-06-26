### Project and Laravel Setup
#### Install PHP
To install PHP run:

```shell
sudo pacman -S php php-apache
```

After PHP is installed, we need to configure Apache PHP module.
To do so, edit /etc/httpd/conf/httpd.conf file,

```shell
sudo nano /etc/httpd/conf/httpd.conf
```

Find the following line and comment it out:
```shell
[...]
#LoadModule mpm_event_module modules/mod_mpm_event.so
[...]
```

Uncomment or add the line:

> LoadModule mpm_prefork_module modules/mod_mpm_prefork.so
> Include conf/extra/httpd-vhosts.conf


Then, add the following lines at the bottom:

> LoadModule php7_module modules/libphp7.so
> AddHandler php7-script php
> Include conf/extra/php7_module.conf

Save and close the file.

Test PHP
Now create a test.php file in the Apache root directory.
```shell
sudo nano /srv/http/test.php
```
Add the following lines:
```php
<?php
phpinfo();
```
Restart httpd service.
```shell
sudo systemctl restart httpd
```
Open up your web browser and navigate to http://ip-address/test.php. You should the screen like
below.
**fig: php-test.png**

#### Installing Composer in Manjaro
To install composer we need to modify some codes in *php.ini*

Open php.ini for editing from the command line:
```shell
sudo gedit /etc/php/php.ini
```
Locate and uncomment the following lines:
```shell
extension=openssl.so
extension=phar.so
```
Search *(ctrl + f)* for the open_basedir and add the following to the end of the line:
```shell
:/usr/local/bin/:/root/
```
Save and close the php.ini file.

Now Downloading and installing composer:

```shell
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

Restart the httpd server:
```shell
sudo systemctl restart httpd
```
Update composer:
```shell
sudo composer self-update
```

### MODIFY httpd.conf to configure root directory
Normally in manjaro to run *.php* files as *localhost* we have to run it from **/srv/http/**, and it cannot be accessed without **root** permission. Therefore, we have to modify the code to run our projects from other directory so that we can access it locally without root permission and run it from localhost.

To modify here are the steps:

Open *httpd.conf*
```shell
sudo gedit /etc/httpd/conf/httpd.conf
```
Search for *ServerName* than add **ServerName localhost** if it is commented out

Search for the following lines:
```shell
DocumentRoot "/srv/http"
<Directory "/srv/http">
```

and change it to you project directory path:
```shell
DocumentRoot "/mnt/Projects/PHP-Projects"
<Directory "/mnt/Projects/PHP-Projects">
```

Now all the configurations are done and all set to start our project.

#### Creating Laravel project
run the command:
```shell
composer create-project laravel/laravel lsapp
```

It will create laravel project with the lame **lsapp**

To check that if we can access our project from browser type:
```html
http://localhost/lsapp/
```
it will show the project directories.
**fig: folder_structure.png**

Now try the following
```html
http://localhost/lsapp/public/
```
it will display the *Laravel* page
**fig: laravel.png**

On the conrary if it does not display the page and displays a error something similar to this:

```html
UnexpectedValueExceptionThe stream or file "/mnt/Projects/PHP-Projects/lsapp/storage/logs/laravel.log" could not be opened: failed to open stream: Permission denied
```
than change the project directory permission:
```shell
chmod -R 775 lsapp/
# or
chmod -R 777 lsapp/
```

But we cannot allow the user to view the directories of the project, and using /public is not a standered way to access the site. as it a serious security issue. Therefore to resolve this security issue we will have to configure a *vhost* also known as I*virtual host* which will point to the location */lsapp/public/*. To do the configuration we will have to modify *httpd-vhosts.conf* as follows:

The file usually located at **/etc/httpd/conf/extra/** and add the following lines to the *httpd-vhosts.conf*

```xml
<!-- nomally does not require -->
<VirtualHost *:80>
    DocumentRoot "/mnt/Projects/PHP-Projects"
    ServerName localhost
</VirtualHost>

<VirtualHost *:80>
    DocumentRoot "/mnt/Projects/PHP-Projects/lsapp/public/"
    ServerName lsapp-dev
</VirtualHost>
```

Than open **sudo gedit /etc/hosts** and add
```
127.0.0.1	lsapp-dev
```
next open **sudo nano /etc/hostname** and add the new servername 
```
lsapp-dev
```

now restart *httpd* & *apachectl*
```shell
sudo systemctl restart httpd
sudo apachectl restart
```

Now in the browser URL type: **http://lsapp-dev/** as configured previously, if everything works fine you will see the laravel page:
**fig: vhost-laravel.png**

#### Laravel folder structure
* **models** are contained inside the **app** directory; you can also create a sub-folder named *models* and than placce the models inside
* **Controller** are located under *app -> Http -> Controllers*. By default it contains a file *Controller.php* which extends the *BaseController*
* **views** are located under *resources -> views*
* **routes** are used to navigate or view the *views* to the client end
* DB Credentials are contained in **.env**
* **app.php** contains project related providers or packages.
* **css** and **js** files are located in **public** directory

By this point our environment and our laravel project is ready to start to work on it.