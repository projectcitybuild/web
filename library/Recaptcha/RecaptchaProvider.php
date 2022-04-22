<?php

namespace Library\Recaptcha;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Library\Recaptcha\Validator\Adapters\GoogleRecaptchaValidator;
use Library\Recaptcha\Validator\Adapters\StubRecaptchaValidator;
use Library\Recaptcha\Validator\RecaptchaValidator;

class RecaptchaProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $isEnabled = config('recaptcha.enabled', false);

        if ($isEnabled) {
            $this->app->bind(abstract: RecaptchaValidator::class, concrete: GoogleRecaptchaValidator::class);
        } else {
            $this->app->bind(abstract: RecaptchaValidator::class, concrete: fn () => new StubRecaptchaValidator(passed: true));
        }
    }

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

        Validator::extend('recaptcha', 'Library\Recaptcha\Rules\RecaptchaRule@passes');
    }
}
