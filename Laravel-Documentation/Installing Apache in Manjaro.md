### Installing Apache in Manjaro
[Manjaro forum](https://forum.manjaro.org/t/install-apache-mariadb-php-lamp-2016/1243)
1. Update your system
Run the following command as root user to update your Manjaro Linux:

```shell
sudo pacman -Syyu
```

2. Install Apache
After updating the system, install Apache web server using command:

```shell
sudo pacman -S apache
```

Edit */etc/httpd/conf/httpd.conf* file,

```shell
sudo nano /etc/httpd/conf/httpd.conf
```

Search and comment out the following line if it is not already:

```shell
[...]
# LoadModule unique_id_module modules/mod_unique_id.so
[...]
```

Save and close the file.

#### Enable Apache service to start at boot and restart Apache service using commands:

```shell
sudo systemctl enable httpd
sudo systemctl restart httpd
```

You can verify whether Apache is running or not with command:

sudo systemctl status httpd

Sample output:

```shell
httpd.service - Apache Web Server
Loaded: loaded (/usr/lib/systemd/system/httpd.service; disabled; vendor
preset: disabled)
Active: active (running) since Tue 2016-02-16 13:00:18 IST; 7s ago
Main PID: 1067 (httpd)
Tasks: 82 (limit: 512)
CGroup: /system.slice/httpd.service
├─1067 /usr/bin/httpd -k start -DFOREGROUND
├─1070 /usr/bin/httpd -k start -DFOREGROUND
├─1071 /usr/bin/httpd -k start -DFOREGROUND
└─1072 /usr/bin/httpd -k start -DFOREGROUND
Feb 16 13:00:18 server systemd[1]: `Started Apache Web Server.`
Feb 16 13:00:18 server httpd[1067]: AH00558: httpd: Could not reliably
dete...ge
```

>Hint: Some lines were ellipsized, use -l to show in full.

Apache server is ready to use.

#### Test Apache
Let us create a sample page in the Apache root directory , i.e /srv/http.
```shell
sudo nano /srv/http/index.html
```
Add the following lines:

```html
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta http-equiv="X-UA-Compatible" content="ie=edge" />
  <title>Welcome</title>
</head>
<body>
  <h2>Welcome to my Web Server test page</h2>
</body>
</html>
```

Now, open your web browser and navigate to *http://localhost or http://IP-address*. You will be pleased with Apache server Test page.
**Fig: apache-test-page.png**

