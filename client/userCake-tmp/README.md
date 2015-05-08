#README

##How to install the userCake Management System

- copy the files from folder "userCake-tmp"
- rename the folder (it will be the name of the client application)
0. Install MySQL (https://www.digitalocean.com/community/tutorials/a-basic-mysql-tutorial). If userCake is installed remove "install" folder, if not the install routine will be runned. If not please run http://yourdomain.com/install/. UserCake will attempt to build the database for you. After completion delete the install folder.
a. Create a MySQL database on your server ("usrcake")
b. Create a new user and give all the PRIVILEGES (z.B. "tmp")
	[root@srv-i3-labeling-system ~] mysql -u root -p
	mysql> SHOW DATABASES;
	mysql> host, user, password from mysql.user;
	--mysql> CREATE USER 'newuser'@'localhost' IDENTIFIED BY 'password';
	mysql> CREATE USER 'tmp'@'localhost' IDENTIFIED BY 'ljrFfrAvsownvXBPsS56';
	--GRANT ALL PRIVILEGES ON * . * TO 'newuser'@'localhost';
	mysql> GRANT ALL PRIVILEGES ON * . * TO 'tmp'@'localhost';
	mysql> FLUSH PRIVILEGES;
	mysql> exit
c. Fill out the connection details in $db_host = "localhost"; "models/db-settings.php" ($db_name, $db_user, $db_pass)

* Demo: http://labeling.i3mainz.hs-mainz.de/userCake-tmp

==================================================

Copyright (c) 2009-2012

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.


Thank you for downloading UserCake, the simple user management package.

//--Installation.

1. Before proceeding please open up models/db-settings.php

2. Create a database on your server / web hosting package.

3. Fill out the connection details in db-settings.php

4. UserCake supports MySQLi and requires MySQL server version 4.1.3 or newer.

5. To use the installer visit http://yourdomain.com/install/ in your browser. UserCake will attempt to build the database for you. After completion
   delete the install folder.

-  That's it you're good to go! In only five steps you have a fully functional user management system.
   For further documentation visit http://usercake.com

//--Credits

UserCake created by: Adam Davis
UserCake V2.0 designed by: Jonathan Cassels

---------------------------------------------------------------

Vers: 2.0.2
http://usercake.com
http://usercake.com/LICENCE.txt
