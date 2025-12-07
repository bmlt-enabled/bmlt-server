# Contributing to the BMLT Server

For general information about BMLT, including ways to contribute to the project, please see
[the BMLT website](https://bmlt.app).

This file contains information specifically about how to set up a development environment for work on the server. There are various ways you can do this; in the directions here we use
[Docker](https://www.docker.com). If you don't have them already, clone the
[BMLT server repo](https://github.com/bmlt-enabled/bmlt-server) from github, and install
[Docker Desktop](https://www.docker.com/products/docker-desktop). These directions were tested with Docker Desktop v4.53.0.

## Running the Server under Docker
1. If you want to use Google Maps for displaying meeting locations, copy `docker/docker-compose.dev.yml.example` to `docker/docker-compose.dev.yml` and edit it to set the `GKEY` variable to your google api key. If you want to use OpenStreetMap, omit this step. You can also provide values for other environment variables using this file (see below).
1. Run the command `make dev` in the top-level `bmlt-server` directory. If something isn't working (for example,
mising packages), try running `make clean` first and then `make dev`.
1. Browse to `http://localhost:8000/main_server/`.
1. Login with username "serveradmin" and password "CoreysGoryStory".
1. When finished, exit by pressing ctrl+c in the terminal window where you ran `make dev`. You may also wish to delete the containers in the Docker Dashboard.


### Environment Variables and Settings

When running a production server, there is a file `auto-config.inc.php` that is used to initialize a set of environment variables used by the server. Other server settings are read from a table in the database.

For development work, Docker takes care of initalizing these environment variables. You can also specify other server settings that will override the values in the database. This is done by adding key/value pairs to the file `docker/docker-compose.dev.yml`. The sample file `docker/docker-compose.dev.yml.example` has just one variable listed (`GKEY` for the Google API key). You can add others as needed. The keys should be in SCREAMING_SNAKE_CASE.  See the `readFromEnvironment` function in `src/app/Models/Setting.php` for a list of possibilities. There are also a few database options: `DB_PREFIX`, `DB_DATABASE`, `DB_USER`, `DB_PASSWORD`, and `DB_HOST`.  Of these `DB_PREFIX` is probably the only one you might want to override.

Server settings that are overridden by entries in `docker/docker-compose.dev.yml` will show up correctly in the Server Settings pane in the UI, but won't necessarily be reflected in the `settings` table in the database. However, if you make a change to the settings in the UI and save, then all the values (including ones from entries in  `docker/docker-compose.dev.yml`) will be written to the database.

## Loading a Different Sample Database

If your database uses a table prefix other than `na` (say `myprefix`), add this line to `docker/docker-compose.dev.yml`:
```
DB_PREFIX: 'myprefix'
```
Then use the following command to load your database:
```
docker exec -i docker-db-1 sh -c 'exec mariadb -uroot -prootserver rootserver' < mydb.sql
```

## UI Development
The UI is now written using [Svelte](https://svelte.dev/), and the code is located in the `resources/js` directory. (The previous UI has now been removed from the current code base.)

To install the UI's dependencies, run the `npm install` command from the `src` directory.

When working on the UI, you'll need to have the [Vite](https://vitejs.dev/) dev server running. To start the dev server, run `npm run dev` from the `src` directory. While the dev server is running, the UI is served out of the `resources/js` directory instead of the normal `public` directory, and [hot module replacement](https://vitejs.dev/guide/features.html#hot-module-replacement) is enabled.

### Debugging the UI
This assumes you are using [VS Code](https://code.visualstudio.com) to develop the UI.

#### Debugging Using a Browser
First, follow the instructions above for running the server under Docker. This mostly just involves running `make dev`.

Then, create `.vscode/launch.json` with a `chrome` debug configuration:

```
{
    "version": "0.2.0",
    "configurations": [
        {
            "type": "chrome",
            "request": "launch",
            "name": "Launch Chrome against localhost",
            "url": "http://localhost:8000/main_server/",
            "webRoot": "${workspaceFolder}/src/resources/js"
        }
    ]
}
```

You should now be able to set breakpoints, launch this debug configuration, and step through the code.

#### Debugging Tests

This works exactly as described in the [vitest documentation](https://v0.vitest.dev/guide/debugging.html). Set any breakpoints, launch a new JavaScript Debug Terminal, and run `npm run test`.

## Developing with the TypeScript Client

When developing Admin API features alongside the TypeScript client, you can use [npm link](https://docs.npmjs.com/cli/v11/commands/npm-link/) to work with both repositories locally without publishing to npm.

### Prerequisites
Clone the BMLT TypeScript client repository:
```bash
git clone https://github.com/bmlt-enabled/bmlt-server-typescript-client.git
```

### One-time Setup
1. Link the TypeScript client globally:
```bash
cd /path/to/bmlt-server-typescript-client
npm link
```

2. Link the client in the BMLT server frontend:
```bash
cd /path/to/bmlt-server/src
npm link ../../bmlt-server-typescript-client
```

### Development Workflow
After making API changes, regenerate the TypeScript client:

1. Generate updated OpenAPI documentation:
```bash
cd /path/to/bmlt-server
make generate-api-json
```

2. Regenerate the TypeScript client:
```bash
cd /path/to/bmlt-server-typescript-client
rm openapi.json
make generate
```

This allows you to develop the API without changing your configs or imports.

## Some Useful `make` Commands

- `make help`  Describe all of the make commands.
- `make clean` Clean the build by removing all build artifacts and downloaded dependencies.
- `make docker` Builds the docker image. You really only need to run this when first getting set up or after a change
has been made to the Dockerfile or its base image.
- `make dev` Run the server under docker (see above).
- `make bash` Open a bash shell on the container's file system.  This will start in the directory `/var/www/html/main_server`
- `make mysql` Start the mysql command-line client with the database `rootserver`, which holds the server's tables. (Well, actually it uses the MariaDB command-line client now rather than mysql, but the `make` command still has the old name.)
- `make test`  Run PHP tests.

There are some additional commands as well; `make help` will list them.

## Running PHP Tests

Start the server using `make dev` (see above).  Then in a separate terminal, run the tests using `make test`.

## Running lint
You can run the linter by running `make lint` in the top-level directory.
It doesn't work when xdebug is listening, so make sure xdebug is off first.

## Utility Commands to Help with Localization
The strings that the UI displays are localized using files in the `src/resources/js/lang` directory. So if you add a new string, normally the programmer would need to add it to 11 or more files (one file per language). There are some utility commands to make this easer. If you are running under Docker, a convenient way to run them is to open a bash shell using `make bash`, connect to the `src` directory, and run the desired command.

### Adding a new key/value pair or updating an existing one
This adds a new key/value pair to all language files, respecting alphabetical order. For languages other than English, the new key/value pair will also have a comment `// TODO: translate`.
````
php artisan translation:add meetingName "Meeting Name"
````
To update an existing key use `--force`.
````
php artisan translation:add cancel "Cancelled" --force
````

### Deleing a Key/Value Pair
This deletes a key and its value from all language files.
````
php artisan translation:delete oldKey
````

### Updating the Translations for One Language from a Spreadsheet
A convenient starting point for the spreadsheet file is to use the button `Download Translations Spreadsheet` under the Administration tab.
````
php artisan translation:update-from-spreadsheet /path/to/italian-translations.xlsx it
````

## Debugging in IntelliJ or PhpStorm

See screenshots below for more detail.

1. Open IntelliJ Preferences. Go to `Languages & Frameworks -> PHP -> Debug`. Under the `Xdebug` section, set the `Debug port` to `10000,9003`. Close IntelliJ Preferences. ![image](docker/img/intellij-prefs-xdebug.png)
1. Add a new `PHP Remote Debug` debug configuration.
1. In the new debug configuration, make click the three dots `...` next to the Server field, and add a new Server. Set the server's `Host` to `0.0.0.0`, and set the `Port` to `8000`. Check the `Use path mappings` checkbox, and set the `Absolute path on the server` for the `Project files` to `/var/www/html/main_server`.  ![image](docker/img/add-debug-server.png)
1. Check `Filter debug connection by IDE key` and set the `IDE Key(session id)` to `ROOT_SERVER_DEBUG`. ![image](docker/img/final-debug-configuration.png)
1. To start debugging, select your new debug configuration and click the `Start Listening for PHP Debug Connections` icon. ![image](docker/img/start-listening.png)
1. Then, click the `Debug` icon to open your web browser and start the XDebug session. ![image](docker/img/debug.png)
1. Then, browse to `http://0.0.0.0:8000/main_server/`
