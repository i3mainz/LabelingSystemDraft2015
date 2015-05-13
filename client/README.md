# The Labeling System *client application*

## NOTE

The client files are in a early state. The files should only be used  to understand the principles. A GUI redesign as well as outsourcing of global variables will be done soon.

### How to run the client application?

0. Install the newest [labeling server application](https://github.com/florianthiery/LabelingSystem/tree/master/server)
1. Configure the global labelingserver paths in `labelingworkbench\config.js`.
2. Configure the User Cake MySQL database params (look at the LS server readme)
3. Upload the folders of the newest client application
4. Run the client via `http://yourcomain.com/labelingworkbench`

## The client folder

The client folder includes four kind of folders: the root page, information pages for instances, pages to display the project tree and the labelingsystemworkbench. The main application, the **labelingworkbench** is the Labeling System GUI. For each internal instance (**project**, **vocabulary**, **label**, **gui**, **agent** and **sparqlendpoint**) exists an HTML page. The **ROOT** page gives more information. The project tree can be displayed for all (**ProjectTree**) and only published vocabularies (**ProjectTreePublic**). All main stylesheets and configurations should be placed in the **config** folder.

###Examples used in the demo version###

* ROOT: http://labeling.i3mainz.hs-mainz.de/
* labelingworkbench: http://labeling.i3mainz.hs-mainz.de/client/
* ProjectTreePublic: http://labeling.i3mainz.hs-mainz.de/ProjectTreePublic/tree.jsp?height=700&width=2000&name=maxmustermann
* ProjectTree: http://labeling.i3mainz.hs-mainz.de/ProjectTree/tree.jsp?height=700&width=2000&name=maxmustermann
* project: http://143.93.114.137/project#c737cfc1eae5444dae18ddc7ad0a5930
* vocabulary: http://143.93.114.137/vocabulary#9a45dd0b05214df4b47df79f642dafec
* label: http://143.93.114.137/label#7be2560fbb4e4adaa679c2a99ecef48f
* gui: http://143.93.114.137/gui#5436e10616f840859e29c0ab0876114c
* agent: http://143.93.114.137/agent#maxmustermann
* sparqlendpoint: http://143.93.114.137/sparqlendpoint#3b57b7a3883647a4a9a1fbb3081dcde4

## Versions

* 13/05/2015: UserFrosting (empty version for Apache without Tomcat using userCake 2.0.2)
* 07/05/2015: template version: http://labeling.i3mainz.hs-mainz.de/labeling-workbench-tmp
* 07/05/2015: userCake-tmp (empty version)
* 13/04/2015: Strawberry [gamma]
* 02/2013: userCake Version 2.0.2 (extern)
