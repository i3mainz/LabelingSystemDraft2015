#README

##How to install the userCake Management System

- UserCake supports MySQLi and requires MySQL server version 4.1.3 or newer.
- copy the files from folder "userCake-tmp"
- rename the folder (it will be the name of the client application)
- Install MySQL (https://www.digitalocean.com/community/tutorials/a-basic-mysql-tutorial). If userCake is installed remove "install" folder, if not the install routine will be runned. If not please run http://yourdomain.com/install/. UserCake will attempt to build the database for you. After completion delete the install folder.

* Connect to MySQL database
* [root@server ~] mysql -u root -p
* Create a MySQL database on your server ("usrcake")
* mysql> CREATE DATABASE database name;
* mysql> SHOW DATABASES;
* Create a new user and give all the PRIVILEGES (z.B. "tmp")
* mysql> CREATE USER 'tmp'@'localhost' IDENTIFIED BY 'password';
* mysql> GRANT ALL PRIVILEGES ON * . * TO 'tmp'@'localhost';
* mysql> FLUSH PRIVILEGES;
* mysql> SELECT host, user, password from mysql.user;
* mysql> exit
* Fill out the connection details in $db_host = "localhost"; "models/db-settings.php" ($db_name, $db_user, $db_pass)

##Demos

Demo: http://labeling.i3mainz.hs-mainz.de/userCake-tmp

Demo with Template: http://labeling.i3mainz.hs-mainz.de/labeling-workbench-tmp
