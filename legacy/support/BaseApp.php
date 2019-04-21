<?php
namespace Support;

/**
 * Laravel doesn't expose any conventional way to override the default
 * paths of its helper path functions, which some Laravel modules
 * use internally.
 *
 * For example, Validation implicitly uses resource_path() to load the default
 * error messages from 'Resources/lang', which obviously won't work since we've
 * moved the Resources folder.
 *
 * To remedy this, we override the base Laravel application class and provide
 * our changed paths manually.
 */
class BaseApp extends \Illuminate\Foundation\Application
{
    public function __construct(?string $basePath = null)
    {
        parent::__construct($basePath);

        $this->useStoragePath(Paths::storage);
        $this->useDatabasePath(Paths::database);
    }

    public function bootstrapPath($path = '')
    {
        return Paths::bootstrap;
    }

    public function langPath()
    {
        return Paths::language;
    }

    public function configPath($path = '')
    {
        return Paths::config;
    }

    public function publicPath()
    {
        return Paths::public;
    }
}
