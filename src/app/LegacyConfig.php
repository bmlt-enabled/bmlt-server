<?php

namespace App;

use App\Models\Setting;

class LegacyConfig
{
    private static ?array $config = null;
    private static bool $configLoaded = false;

    public static function get(?string $key = null, $default = null)
    {
        if (!self::$configLoaded) {
            self::loadConfig();
        }

        if (is_null($key)) {
            // Return only DB credentials when getting all config
            return self::$config;
        }

        // DB credential keys come from legacy config file
        $dbCredentialKeys = ['db_database', 'db_username', 'db_password', 'db_host', 'db_prefix'];
        if (in_array($key, $dbCredentialKeys)) {
            return self::$config[$key] ?? $default;
        }

        // TODO: All other settings should come from the database, but we can't query during bootstrap
        // For now, return the setting from the in-memory override (used in tests) or the default
        if (isset(self::$config[$key])) {
            return self::$config[$key];
        }

        // Try to query the database if available
        // Convert snake_case key to camelCase for database lookup
        $camelKey = lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $key))));
        try {
            $setting = Setting::where('name', $camelKey)->first();
            if ($setting) {
                return $setting->getTypedValue();
            }
        } catch (\Exception $e) {
            // Database not available yet during bootstrap, return default
        }

        return $default;
    }

    public static function set(string $key, $value)
    {
        // really should only be used in testing
        if (!self::$configLoaded) {
            self::loadConfig();
        }

        self::$config[$key] = $value;
    }

    public static function remove(string $key)
    {
        // really should only be used in testing
        if (!self::$configLoaded) {
            self::loadConfig();
        }

        unset(self::$config[$key]);
    }

    public static function reset()
    {
        // really should only be used in testing
        self::$config = null;
        self::$configLoaded = false;
    }

    private static function loadConfig()
    {
        $legacyConfigFile = base_path() . '/../auto-config.inc.php';
        if (file_exists($legacyConfigFile)) {
            defined('BMLT_EXEC') or define('BMLT_EXEC', 1);
            require($legacyConfigFile);
        } elseif (env('GITHUB_ACTIONS') != 'true') {
            die('<h1>Configuration Problem</h1>
              <p>The file <code>auto-config.inc.php</code> was not found.</p>
              <p>If this is a brand new BMLT server installation, please see the installation instructions. The latest version
              of the instructions is available at
              <a href="https://github.com/bmlt-enabled/bmlt-server/blob/main/installation/README.md">
                https://github.com/bmlt-enabled/bmlt-server/blob/main/installation/README.md</a>.
              For other versions, get a copy of the BMLT server source code from github, check out the version you want,
              and look in the file <code>installation/README.md</code>.</p>');
        } // TODO: Should we get id of this now

        $config = [];

        if (isset($dbName)) {
            $config['db_database'] = $dbName;
        }

        if (isset($dbUser)) {
            $config['db_username'] = $dbUser;
        }

        if (isset($dbPassword)) {
            $config['db_password'] = $dbPassword;
        }

        if (isset($dbServer)) {
            $config['db_host'] = $dbServer;
        }

        if (isset($dbPrefix)) {
            $config['db_prefix'] = $dbPrefix;
        }

        // Only load DB credentials from auto-config.inc.php
        // All other settings come from the database via Setting model

        self::$config = $config;
        self::$configLoaded = true;
    }
}
