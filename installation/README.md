# Setting Up a BMLT Server

We dropped support for the Installer Wizard as of version 4.0.0 of the server. Setting up a completely new server
is at this point an infrequent event, and hopefully these directions will be clear enough.

For a more detailed but older tutorial please see https://bmlt.app/setting-up-the-bmlt/. That tutorial is based on the Installer Wizard, so you'll need to adapt it accordingly. But it may be useful to explain some of the steps. Differences are noted below.

## Setting Up an Empty MySQL Database

Set up an empty MySQL database, along with a MySQL user that has access to it.  (The directions for this step in the older tutorial are still valid.) The standard name for this database is `rootserver`, but you can name it something else if you prefer.

## Uploading the BMLT Server Zip File

Get the latest version of the server from https://github.com/bmlt-enabled/bmlt-server/releases using the link labeled `bmlt-server.zip`, and upload it to your web hosting provider's server. (The directions for this step in the older tutorial are also still valid.) For this part of the step, upload `bmlt-server.zip` *without* unzipping it on your local machine. Then unzip it on your server. You should end up with a directory `main_server` under the directory that holds the files that show up on your website. Thus, if your web hosting server has a directory `public_html` for the files that show up on your website, put `main_server` in that directory, like this: `public_html/main_server`. (Again, don't try to upload the unzipped directory from your local machine -- that can result in problems with dropped files and such.)

## Adding the auto-config File

This step is also different from the old tutorial.

Download the file `initial-auto-config.txt` from GitHub at https://github.com/bmlt-enabled/bmlt-server/blob/main/installation/initial-auto-config.txt.

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

Now edit the `auto-config.inc.php` file with new parameters as needed. You can do this using the `edit` command on cPanel. There are two parameters you definitely need to update, namely `$dbUser` and `$dbPassword` (the user and password for your server database). There are a few other parameters in the file, but the default values may well be what you want.

Alternatively, you can edit the `initial-auto-config.txt` file on your local machine, and then upload the edited file, thus avoiding needing to edit it on your web host. If you do that, be sure and use an editor intended for editing source code and not something like Microsoft Word.

## Initial Log In

Now you should be able to go to the website for your new server (which might be at a URL like `https://bmlt.myregion.org/main_server/`). Log in as user `serveradmin` password `change-this-password-first-thing`. As the initial password suggests (not very subtly), first go to the `Account` tab and change the password to something unique for your BMLT server.

## Adding Users and Service Bodies

At this point you can set up one or more Service Body Administrators and Service Bodies, and start adding meetings. We are now back to steps that are unchanged from the old tutorial, so refer to that for details.

## Changing Server Settings

To change server settings, go to the Server Settings item under the Administration tab when logged in as the server admin. One in particular that you may want to change is `Google Maps API Key`. The meeting location map can be displayed using either Google Maps or OpenStreetMap. To use Google maps, set `Google Maps API Key` to your Google api key. To use OpenStreetMap, just skip this step -- the system will default to it if there isn't a Google api key.

If you export a database to share with others, you should probably set Google Maps API Key to the empty string first.

For versions of the BMLT server prior to 4.1.0, `auto-config.inc.php` could also contain a large number of additional variable definitions. In 4.1.0, these are moved to the database, and can be inspected or updated using Server Settings. If you are migrating from an older version of the server, there is a migration that runs once when upgrading to 4.1.0 that will copy these values out of `auto-config.inc.php` and into the database. After the migration is run, these other variables in `auto-config.inc.php` will no longer have any effect on the server, and you can simplify `auto-config.inc.php` if you wish.
