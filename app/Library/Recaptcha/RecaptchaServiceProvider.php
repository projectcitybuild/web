<?php

namespace App\Library\Recaptcha;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class RecaptchaServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     */
    public function boot(): void
    {
        Blade::directive('recaptcha_key', function () {
            return "<?php echo config('recaptcha.keys.site') ?>";
        });

        Validator::extend('recaptcha', 'App\Library\Recaptcha\RecaptchaRule@passes');
    }

    /**
     * Register bindings in the container.
     */
    public function register(): void
    {
    }
}
