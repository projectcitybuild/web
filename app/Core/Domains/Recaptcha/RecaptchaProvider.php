<?php

namespace App\Core\Domains\Recaptcha;

use App\Core\Domains\Recaptcha\Validator\Adapters\GoogleRecaptchaValidator;
use App\Core\Domains\Recaptcha\Validator\Adapters\StubRecaptchaValidator;
use App\Core\Domains\Recaptcha\Validator\RecaptchaValidator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class RecaptchaProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $isEnabled = config('recaptcha.enabled', default: false);

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

        Validator::extend('recaptcha', 'App\Core\Domains\Recaptcha\Rules\RecaptchaRule@passes');
    }
}
