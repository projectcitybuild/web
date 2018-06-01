<?php
namespace App;

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
class BaseApp extends \Illuminate\Foundation\Application {

    public function __construct(?string $basePath = null) {
        parent::__construct($basePath);

        $this->useStoragePath($this->basePath.'/support/storage');
        $this->useDatabasePath($this->basePath.'/app/database');
    }

    public function bootstrapPath($path = '') {
        return $this->basePath.'/support/bootstrap';
    }

    public function langPath() {
        return $this->basePath.'/assets/lang';
    }

    public function configPath($path = '') {
        return $this->basePath.'/config';
    }

}