# README #

This project is an extension designed to run on the Simple:Press Wordpress Plugin Platform.  

### How do I get set up? ###

This plugin is not a normal WordPress plugin and cannot be installed via the WordPress PLUGINS screen.
Instead, you should:

- Download the file from your receipt or from your dashboard(topic-status.zip).
- Go to FORUM->Plugins
- click on `Plugin Uploader`
- Click on the `Choose File` button and select the zip file you downloaded earlier
- Click the `Upload Now` button
- Go back to the FORUM->Plugins screen and activate the plugin.


### Change Log  ###

3.1.0
-----
New: Simple:Press 6.0.7 EDD licensing infrastructure compatibility

3.0.1
-----
Fix: (B0015) Some performance related shortcuts were missing
Fix: (B0016) Performance related optimization to prevent DB queries from being unncessarily generated 

3.0.0
-----
New: Assign color codes to statuses
New: Automatically LOCK the topic when certain statuses are assigned
New: Restrict certain statuses to particular user groups
New: Set default status for new topics
Fix: (B0001) Extra rows being added when topic status is saved

2.0.0
-----
Update: Changes for compatibility with Simple:Press 6.x