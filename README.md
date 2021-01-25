# Data source CiviCRM api for wpDataTable

Provides a CiviCRM api data source for [wpDataTable plugin](https://wordpress.org/plugins/wpdatatables/). 
You can use this plugin with [Connector to CiviCRM with CiviMcRestFace plugin](https://wordpress.org/plugins/connector-civicrm-mcrestface/) 
which gives you the ability to connect to an CiviCRM installation on a different server.

This plugin works with the free version of the plugin but the only drawback is that you cannot replace content in a column.

**Funded by**

* [CiviCooP](https://www.civicoop.org)
* [Civiservice.de GmbH](https://civiservice.de/)
* [Bundesverband Soziokultur e.V.](https://www.soziokultur.de/)

# Contributing

The code of this plugin is published and maintained at [Github](https://github.com/CiviMRF/wpcivicrm-datatable/).
The plugin is also published at [Wordpress.org](https://wordpress.org/plugins/data-source-civicrm-api-for-wpdatatable)
and this requires that we submit each release to the [Wordpress SVN](https://plugins.svn.wordpress.org/data-source-civicrm-api-for-wpdatatable)

**Workflow for development**

1. Fork the repository at Github
1. Create a new branch for the functionality you want to develop, or for the bug you want to fix.
1. Write your code and test it, once you are finished push it to your fork.
1. Create a Pull Request at Github to notify us to merge your changes.

**Workflow for creating a release**

Based on the instruction from [Learn with Daniel](https://learnwithdaniel.com/2019/09/publishing-your-first-wordpress-plugin-with-git-and-svn/)

1. Update `readme.txt` with the new version number (also update the Changelog section)
1. Update `wpcivicrm-datatable.php` with the new version number
1. Create a new version at [Github](https://github.com/CiviMRF/wpcivicrm-datatable/).
1. To publish the release at Wordpress Plugin directory follow the following steps:
    1. Create a temp directory: `mkdir wpcivicrm-datatable-tmp`
    1. Go into this directory: `cd wpcivicrm-datatable-tmp`
    1. Do an SVN checkout into SVN directory: `svn checkout https://wordpress.org/plugins/data-source-civicrm-api-for-wpdatatable svn`
    1. Clone the Github repository into Github directory: `git clone https://github.com/CiviMRF/wpcivicrm-datatable.git github`
    1. Go into the Github directory: `cd github`
    1. Checkout the created release (in our example 1.0.0): `git checkout 1.0.0`
    1. Go into the svn directory: `cd ../svn`
    1. Copie the files from github to SVN: `rsync -rc --exclude-from="../github/.distignore" "../github/" trunk/ --delete --delete-excluded`
    1. Add the files to SVN: `svn add . --force`
    1. Tag the release in SVN (in our example 1.0.0): `svn cp "trunk" "tags/1.0.0"`
    1. Now submit to the Wordpress SVN with a message: `svn ci -m 'Adding 1.0.0'`
