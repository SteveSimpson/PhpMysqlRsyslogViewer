# PhpMysqlRsyslogViewer
A simple PHP page to search / view rsyslog stored in MySQL

I'm making this into what I need for my envionment, but it is based on a very elegant and simple script by Andrew Galdes. You check out his original post and his post about rsyslog to MySQL at the following links:

* https://www.agix.com.au/simple-rsyslogmysql-log-viewer-in-php/
* http://www.agix.com.au/rsyslog-and-mysql-on-centos7-and-redhat-7/

## Backstory
I needed a simple way to view /filter/sort rsyslog data. Thought I found a program, it was bloated and didn't fit the default schema that rsyslog is distributing (with CentOS 7). I found the latter both ironic and frustrating since the rsyslog wiki pointed to this program. Maybe I missed something, but Google seemed to confirm my findings. Now I could have figured out what the fields were supposed to look like and created a view, I saw hints of others trying that.

That's when I came across Andrew's script. It was from a couple of years ago, but he did everything I really needed in 124 lines of simple PHP. COOL! - Simple is better. Yeah, it had a couple of things that I wanted to clean up, but all-in-all a very elegant solution.

Anyway, I hope this helps other people.

## Moving Forward

OK - so as I dug into this, there was a lot that I wanted to clean up and I've been working MVC for a long time. This is definitely more than a web page now, but everything I did made sense to me. :-)

* Got DB config our of the page itself ... I remember seeing this broken web page with mysql user, password, right there, so tempting. I wouldn't want anyone to sucumb to that much temtation, better move it to config file.
* Needed another page and wanted the look and feel to stay the same, add a simple library and view
* Wanted local / maintainable copy of bootstrap libraries -> add composer and script

So definitely not as simple anymore, but I was able to document all the installation in a couple of hours. (Hopefully, it will work.)

## Installation

### 1. Setup your system

I suggest you view your logs over https, but not going into all the setup here.

```
yum install httpd mod_ssl php php-mysqlnd
```

### 2. Clone the repo

I'm using CentOS 7, so all the defaults will be geared for that. Your mileage may vary. I suggest you clone into /var/www/PhpMysqlRsyslogViewer

### 3. Optionally, Download Bootstrap libraries

I like having local libraries and being able to update them... If you don't care the app will just grab the CDN ones.

```
cd /var/www/PhpMysqlRsyslogViewer

composer update

./grab-assets
```

### 4. Update the Configuration File

```
cd /var/www/PhpMysqlRsyslogViewer

cp -a config/config.php.sample config/config.php

vi config/config.php
```

### 5. Setup rsyslog to MySQL / MariaDB

Lots of good instructions on this, but just for completeness, I'll give you the quick and dirty for your local system here; note this is for CentOS 7 at time of writing, version numbers/paths will change. Please don't use my default passord, its very bad form. 

```
sudo yum install rsyslog-mysql

mysql -u root -p < /usr/share/doc/rsyslog-8.24.0/mysql-createDB.sql 
Enter password: 

mysql -u root -p
Enter password: 

MariaDB [(none)]> GRANT SELECT, INSERT ON `Syslog`.* TO 'rsyslog'@'localhost' IDENTIFIED BY 'some_other_password';
MariaDB [(none)]> flush privileges;
MariaDB [(none)]> exit
```

Add the following lines to /etc/rsyslog.conf

```
$ModLoad ommysql.so
*.*  :ommysql:localhost,Syslog,rsyslog,some_other_password
```

### 6. Configure MySQL / MariaDB

I'm using this on my local system with no network connectivity, if you use a network database, I suggest that you setup an encrypted connection. Please don't use my default passord, its very bad form. 

Maria needs SELECT permission to the Syslog database.

```
$ mysql -u root -p 
Enter password: 

MariaDB [(none)]> GRANT SELECT ON `Syslog`.* TO 'logview'@'localhost' IDENTIFIED BY 'MySecretMySQLPassword';
MariaDB [(none)]> flush privileges;
MariaDB [(none)]> exit
```

### 7. Configure Apache to show the pages

I suggest that you use Apache to add a password 

add this file to /etc/httpd/conf.modules.d/

log.conf

```
Alias /log /var/www/PhpMysqlRsyslogViewer/web

<Directory /var/www/PhpMysqlRsyslogViewer/web >
  AllowOverride none
  Require all granted
  
  # Below here is not required by something like this should be implemented
  AuthType Basic
  AuthName "Syslog Server"
  AuthBasicProvider file
  AuthUserFile "/var/www/PhpMysqlRsyslogViewer/users"
  Require valid-user
  
</Directory>
```

if you use basic auth:

* create the file: htpasswd -c /var/www/PhpMysqlRsyslogViewer/users fjones
* add another user to the file: htpasswd /var/www/PhpMysqlRsyslogViewer/users tsmith
* read lots more about this and understand what you are doing: https://httpd.apache.org/docs/current/howto/auth.html

### 8. Optional, Configure Apache to log syslog

Replace your logging lines in Apache with something like the following:

```
LogLevel Warn
ErrorLog "|$logger -t httpd -p 4"

CustomLog "|$logger -t httpd -p 6" \
          "%t %h %{SSL_PROTOCOL}x %{SSL_CIPHER}x \"%r\" %b"
```



