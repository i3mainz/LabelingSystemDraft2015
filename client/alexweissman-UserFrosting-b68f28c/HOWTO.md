# HOW TO INSTALL AND SET NEW PAGES

## CODE

* https://github.com/alexweissman/UserFrosting
* http://www.userfrosting.com/

## INSTALL

* get userfrosting "alexweissman-UserFrosting-b68f28c" folder
* create mysql user "usrfrosting" AS root
* create database "userfrostingtmp"
* set database settings in "models/db-settings.php"
* go to http://localhost/alexweissman-UserFrosting-b68f28c/install/
* set properties (normal user is called "User") and create master role (insert configuration token: SELECT value FROM uf_configuration WHERE name = "root_account_config_token";)
* delete instal folder
* login as master
* Site Settings -> Groups -> Create new Group
  * Ontologist -> account/dashboard.php
* Site Settings -> Authorization -> Group Ontologist -> Add action for group Ontologist and Page-level authorization
  * add actions and page authorizations
* upgrade user in "Users"

## NEUE SEITE ERSTELLEN

* copy page in /account
* INSERT INTO uf_nav (menu,page,name,position,class_name,icon,parent_id) VALUES("left","account/dashboard2.php","Dashboard Test",4,"dashboard2","da da-dashboard",0);
* INSERT INTO uf_nav_group_matches (menu_id,group_id) VALUES(12,1);
* echo renderMenu("dashboard2"); in dashboard2.php class_name
* open "Site Settings -> Authorization -> Group Ontologist -> Page-level authorization ans set it to uf_nav_group_matches group_id
* see https://github.com/alexweissman/UserFrosting/wiki/Adding-a-navigation-menu-item
