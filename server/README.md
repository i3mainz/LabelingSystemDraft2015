# The Labeling System *server application*

## The server requirements

The Labeling System needs an configured server, using several software. You can find the requirements [here.](http://labeling.i3mainz.hs-mainz.de/technology) You need an Apache Tomcat, JAVA, PHP, a MySQL Database (version 4.1.3 or newer), as well as an [Open RDF Sesame triplestore](http://rdf4j.org) and the [User Cake Management System](http://usercake.com). Configuration properties for User Cake you can find in the [ZIP-file](http://usercake.com/downloads/userCakeV2.0.2.zip). This repository consists also a default version in the client folder.

## The labelingserver files

The server application consists of two WAR-files: **rest.war** and **labelingserver.war**. These WAR-files are build as Netbeans projects using several [Maven dependencies](http://labeling.i3mainz.hs-mainz.de/technology#maven). The configuration can be done in the `rdfutils` project in `Config.java` at `rdfutils\src\main\java\de\i3mainz\ls\Config\`. The three projects must be opened using Netbeans and `Build with Dependencies` to download the Maven dependencies. You have to build first the **rdfutils.jar** (configure paths). This JAR is included as dependency in the **rest.war** and **labelingserver.war**.

### How to run the server application?

0. Install Apache Tomcat, JAVA, PHP and the MySQL database.
1. Configure the database properties of the User Cake Managemant System or use the **labelingworkbench** (configure the properties there).
  1. Before proceeding please open up models/db-settings.php
  2. Create a database on your server / web hosting package (by default **usrcake**).
  3. Fill out the connection details in db-settings.php (by default: db_host=localhost, db_name=usrcake, db_user and db_pass must be self-configured).
  4. To use the installer visit http://yourdomain.com/install/ in your browser. UserCake will attempt to build the database for you. After completion delete the install folder.
2. Deploy the Sesame Server (if you want you can deploy the Sesame Workbench, too).
3. Create a new repository in the triplestore for the labelingsystem (by default **labelingsystem**) and for the imported concepts (by default **concepts**) as `In Memory Store`.
4. Open all three projects (rdfutils, labelingserver, rest) in Netbeans.
5. Configure the paths in `rdfutils\src\main\java\de\i3mainz\ls\Config\Config.java`.
6. Build rdfutils with dependencies and get `rdfutils-1.0.jar`.
7. Build labelingserver with dependencies and get `labelingserver.war` using the functions and properties of rdfutils.
8. Build rest with dependencies and get `rest.war` using the functions and properties of rdfutils.
9. Deploy **labelingserver.war** and **rest.war**

## The server folder

The server folder includes three Netbeans projects: rest, labelingserver and rdfutils. These are documented in an javadoc (see folder). Executable files (one JAR and two WAR) using the demo parameters on http://labeling.i3mainz.hs-mainz.de are placed in the war-jar folder.

## Versions

* 13/04/2015: Peach [beta]
