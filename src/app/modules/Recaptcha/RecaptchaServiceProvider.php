<?php
namespace App\Modules\Recaptcha;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class RecaptchaServiceProvider extends ServiceProvider {

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot() {
        Blade::directive('recaptcha_key', function () {
            return "<?php echo \App\Modules\Recaptcha\RecaptchaTemplate::getSiteKey(); ?>";
        });
    }

    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register() {

    }

}