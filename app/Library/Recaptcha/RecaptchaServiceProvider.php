<?php
namespace App\Library\Recaptcha;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class RecaptchaServiceProvider extends ServiceProvider
{

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        Blade::directive('recaptcha_key', function () {
            return "<?php echo config('recaptcha.keys.site') ?>";
        });

        Validator::extend('recaptcha', 'App\Library\Recaptcha\RecaptchaRule@passes');
    }

    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
    }
}
