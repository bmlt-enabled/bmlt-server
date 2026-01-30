# Contributing to the BMLT Server

For general information about BMLT, including ways to contribute to the project, please see
[the BMLT website](https://bmlt.app).

This file contains information specifically about how to set up a development environment for work on the server. There are various ways you can do this; in the directions here we use
[Docker](https://www.docker.com). If you don't have them already, clone the
[BMLT server repo](https://github.com/bmlt-enabled/bmlt-server) from github, and install
[Docker Desktop](https://www.docker.com/products/docker-desktop). These directions were tested with Docker Desktop v4.53.0.

## Running the Server under Docker
1. If you want to use Google Maps for displaying meeting locations, copy `docker/docker-compose.dev.yml.example` to `docker/docker-compose.dev.yml` and edit it to set the `GOOGLE_API_KEY` variable to your google api key. If you want to use OpenStreetMap, omit this step. You can also provide values for other environment variables using this file (see below).
2. Run the command `make dev` in the top-level `bmlt-server` directory. If something isn't working (for example,
mising packages), try running `make clean` first and then `make dev`.
3. Browse to `http://localhost:8000/main_server/`.
4. Login with username "serveradmin" and password "CoreysGoryStory".
5. When finished, exit by pressing ctrl+c in the terminal window where you ran `make dev`. You may also wish to delete the containers in the Docker Dashboard.


### Environment Variables and Settings

When running a production server, there is a file `auto-config.inc.php` that is used to initialize a set of environment variables used by the server. Other server settings are read from a table in the database.

For development work, Docker takes care of initializing these environment variables. You can also specify other server settings that will override the values in the database. This is done by adding key/value pairs to the file `docker/docker-compose.dev.yml`. The sample file `docker/docker-compose.dev.yml.example` has just one variable listed (`GOOGLE_API_KEY` for the Google API key). You can add others as needed. The keys should be in SCREAMING_SNAKE_CASE.  See the `SETTING_DEFAULTS` constant in `src/database/migrations/2025_11_20_133800_seed_settings_from_legacy_config.php` for a list of possibilities. There are also a few database options: `DB_PREFIX`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`, and `DB_HOST`.  Of these `DB_PREFIX` is probably the only one you might want to override.

Server settings that are overridden by entries in `docker/docker-compose.dev.yml` will show up correctly in the Server Settings pane in the UI, but won't necessarily be reflected in the `settings` table in the database. However, if you make a change to the settings in the UI and save, then all the values (including ones from entries in  `docker/docker-compose.dev.yml`) will be written to the database.

### Switching PHP Versions

To test with a different PHP version, add this to `docker/docker-compose.dev.yml`:
```yaml
services:
  bmlt:
    build:
      args:
        PHP_VERSION: 8.4
```

Then rebuild without cache:
```bash
docker compose -f docker/docker-compose.yml -f docker/docker-compose.dev.yml build --no-cache bmlt
make dev
```

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

- `make help`  Describe all the make commands.
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

### Exporting Translations to a Spreadsheet
This exports the frontend translation file for a given language to an XLSX spreadsheet. The spreadsheet has three columns: the translation key, the English text, and the target language text. Rows with `// TODO: Translate` comments are highlighted in green, making it easy to see which translations still need to be done.
````
php artisan translation:export-spreadsheet it /path/to/italian-translations.xlsx
````

## Adding a New Language to the Server

To add a new language (for example, Norwegian, which has [ISO 639-1](https://en.wikipedia.org/wiki/List_of_ISO_639_language_codes) code `no`):

### Backend Translation Files

1. Copy the English translation directory:
   ```bash
   cp -r src/lang/en/ src/lang/no/
   ```

2. Edit `src/lang/no/language_name.php` to set the native language name:
   ```php
   <?php
   
   return [
       'name' => 'Norsk',
   ];
   ```

3. Translate the strings in the other PHP files (`main_prompts.php`, `change_detail.php`, `change_type.php`, `weekdays.php`, etc.).

### Frontend Translation Files

1. Copy the English translation file:
   ```bash
   cp src/resources/js/lang/en.ts src/resources/js/lang/no.ts
   ```

2. Translate the strings in the file `src/resources/js/lang/no.ts` and rename the two constants that it exports:
   `enYupLocale` => `noYupLocale`, `enTranslations` => `noTranslations`.


3. Export the new language in `src/resources/js/lang/index.ts`:
   ```typescript
   export { noTranslations, noYupLocale } from './no';
   ```

4. Import and add the new language to `src/resources/js/stores/localization.ts`:
   - Add to imports (maintain alphabetical order):
     ```typescript
     noTranslations,
     noYupLocale,
     ```
   - Add to the `strings` object (maintain alphabetical order):
     ```typescript
     no: noTranslations,
     ```
   - Add to the `yupLocales` object (maintain alphabetical order):
     ```typescript
     no: noYupLocale,
     ```

### Format Translations

The server will actually run without any format translations for the new language, but you will almost certainly want to add some to provide a reasonable experience for users and service body administrators. There are two options for adding format translations for the new language:
1. add them using a database migration
2. add them by logging in as the server admin and using the editor available on the Formats tab

Option 1 puts the format translations into the code base, and is how all the formats that come with a fresh out-of-the-box server are defined.

Option 2 is much simpler -- just use the format translation editor in the UI -- but they won't be in the code base and someone who spins up a new server in your new language won't get them automatically. You can also do some of both: start with a set of basic format translations defined using a database migration, and then add some additional ones using the UI.

For historical reasons, the meeting's venue type (in-person, hybrid, or virtual) is specified in the server database using the `HY` (hybrid) and `VM` (virtual meeting) formats. The UI for service body administrators doesn't expose these to the administrator however; they get set using the Venue Type menu on the meeting editor Location tab. `TC` (Temporarily Closed) has been deprecated -- it was used a lot during the pandemic -- but you may still encounter it if you start with a database containing some older data. However, these formats **are** visible in crouton and bread for hybrid, virtual, or temporarily closed meetings, so you should provide translations for them.

The migration `src/database/migrations/1902_01_01_000000_create_initial_schema.php` includes translations for the formats shipped with the server. You can use these as suggestions for other translations you might want to provide for the new language.

## Debugging in IntelliJ or PhpStorm

See screenshots below for more detail.

1. Open IntelliJ Settings. Go to `Languages & Frameworks -> PHP -> Debug`. Under the `Xdebug` section, set the `Debug port` to `10000,9003`. Click OK and close IntelliJ Preferences.

![image](docker/img/intellij-prefs-xdebug.png)

2. Pick `Run -> Edit Configurations...` Add a new `PHP Remote Debug` debug configuration.
3. In the new debug configuration, check `Filter debug connection by IDE key`. Click the three dots `...` next to the Server field, and add a new Server. Set the server's `Host` to `0.0.0.0`, and set the `Port` to `8000`. Check the `Use path mappings` checkbox, and set the `Absolute path on the server` for the `src` directory under `Project files` to `/var/www/html/main_server`. Click OK.

 ![image](docker/img/add-debug-server.png)

4. Back in the debug configuration, set the `IDE Key(session id)` to `ROOT_SERVER_DEBUG`.

![image](docker/img/final-debug-configuration.png)

5. To start debugging, select your new debug configuration and click the `Start Listening for PHP Debug Connections` icon.

![image](docker/img/start-listening.png)

6. Then, click the `Debug` icon to open your web browser and start the XDebug session.

![image](docker/img/debug.png)

7. Finally, browse to `http://0.0.0.0:8000/main_server/`
