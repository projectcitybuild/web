<?php
namespace Support;

/**
 * A centralised location to update directory paths
 */
final class Paths {

    private function __construct() {}

    private const basePath = __DIR__.'/../';

    // directory paths
    const bootstrap  = self::basePath.'/support/bootstrap';
    const config     = self::basePath.'/infrastructure/config';
    const database   = self::basePath.'/infrastructure/database';
    const storage    = self::basePath.'/infrastructure/storage';
    const public     = self::basePath.'/interfaces/web/public';
    const views      = self::basePath.'/interfaces/web/views';
    const language   = self::basePath.'/interfaces/web/assets/lang';

    // file paths
    const web_route_file = self::basePath.'/interfaces/web/Routes.php';
    const api_route_file = self::basePath.'/interfaces/api/Routes.php';
    const app_file       = self::basePath.'/support/bootstrap/app.php';

}