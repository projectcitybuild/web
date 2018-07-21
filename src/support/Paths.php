<?php
namespace Support;

/**
 * A centralised location to update directory paths
 */
class Paths {

    private function __construct() {}

    private const basePath = __DIR__.'/../';

    // directory paths
    const bootstrap  = self::basePath.'/support/bootstrap';
    const config     = self::basePath.'/application/config';
    const database   = self::basePath.'/application/database';
    const language   = self::basePath.'/interfaces/web/assets/lang';
    const storage    = self::basePath.'/support/storage';
    const public     = self::basePath.'/interfaces/web/public';
    const views      = self::basePath.'/interfaces/web/views';

    // file paths
    const web_route_file = self::basePath.'/interfaces/web/Routes.php';
    const api_route_file = self::basePath.'/interfaces/api/Routes.php';

}