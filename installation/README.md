# Setting Up a BMLT Server

As of version 4.0.0 of the server, we are dropping support for the Installer Wizard. Setting up a completely new server
is at this point an infrequent event, and hopefully these directions will be clear enough.

For a more detailed but older tutorial please see https://bmlt.app/setting-up-the-bmlt/. That tutorial is based on the Installer Wizard, so you'll need to adapt it accordingly. But it may be useful to explain some of the steps. Differences are noted below.

## Setting Up an Empty MySQL Database

Set up an empty MySQL database, along with a MySQL user that has access to it.  (The directions for this step in the older tutorial are still valid.) The standard name for this database is `rootserver`, but you can name it something else if you prefer.

## Uploading the BMLT Server Files

Get the latest version of the server from https://github.com/bmlt-enabled/bmlt-server/releases using the link labeled `bmlt-server.zip` (not the source files), and upload it to your web hosting provider's server. (The directions for this step in the older tutorial are also still valid.) For this part of the step, upload `bmlt-server.zip` *without* unzipping it on your local machine. Then unzip it on your server. You should end up with a directory `main_server` under the directory that holds the files that show up on your website. Thus, if your web hosting server has a directory `public_html` for the files that show up on your website, put `main_server` in that directory, like this: `public_html/main_server`. (Again, don't try to upload the unzipped directory from your local machine -- that can result in problems with dropped files and such.)

In addition, you will need two files from the source code repository. You can either download these individually from github, or else download one of the `Source code` zip files in the releases directory linked above and get them from the unzipped directory.

## Initializing the MySQL Database

This step is different from the old tutorial.

Option 1: download the file `initial-database.sql` from github at https://github.com/bmlt-enabled/bmlt-server/blob/main/installation/initial-database.sql.

Option 2: if you downloaded the source code, find the directory `installation` and within it the file `initial-database.sql`.

However you get the file, import its contents into the empty MySQL database that you set up in the first step.  (If you are using cPanel, find the `phpMyAdmin` tool under `Databases`, select your new database, and then click `Import`.)

## Adding the auto-config File

This step is also different from the old tutorial.

Option 1: download the file `initial-auto-config.txt` from github at https://github.com/bmlt-enabled/bmlt-server/blob/main/installation/initial-auto-config.txt.

Option 2: if you downloaded the source code, find the directory `installation` and within it the file `initial-auto-config.txt`.

Upload this file to your server, put it in the directory that holds your `main_server` directory, and rename it to `auto-config.inc.php`.  This file should have the permissions `-rw-r--r--` (`0644` in octal). This means that the owner of the file can read and write it, and the owning group and others can read it.

Note that the file `auto-config.inc.php` is not inside `main_server`, but rather at the same level. This is a little weird, but does have the advantage that you can upload a new version of the server easily without touching the `auto-config.inc.php` file.  So your directory structure should look something like this:
```
public_html
   auto-config.inc.php
   main_server
      app
      bootstrap
      ......
```

Now edit the `auto-config.inc.php` file with new parameters as needed. You can do this using the `edit` command on cPanel. There are two parameters you definitely need to update, namely `$dbUser` and `$dbPassword` (the user and password for your server database). You also need to either update the parameter `$gkey` if you are using Google Maps, or else delete this parameter altogether if you are using OSM (Open Street Maps) for maps and nominatim for geocoding.

There are various other parameters in the file, but the default values may well be what you want.

Alternatively, you can edit the `initial-auto-config.txt` file on your local machine, and then upload the edited file, thus avoiding needing to edit it on your web host. If you do that, be sure and use an editor intended for editing source code and not something like Microsoft Word.

## Initial Log In

Now you should be able to go to the website for your new server (which might be at a URL like `https://bmlt.myregion.org/main_server/`). Log in as user `serveradmin` password `change-this-password-first-thing`. As the initial password suggests (not very subtly), first go to the `Account` tab and change the password to something unique for your BMLT server.

## Adding Users and Service Bodies

At this point you can set up one or more Service Body Administrators and Service Bodies, and start adding meetings. We are now back to steps that are unchanged from the old tutorial, so refer to that for details.
