<?php
namespace Infrastructure\Library\Recaptcha;

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
            return "<?php echo env('RECAPTCHA_SITE_KEY') ?>";
        });

        Validator::extend('recaptcha', 'Infrastructure\Library\Recaptcha\RecaptchaRule@passes');
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
