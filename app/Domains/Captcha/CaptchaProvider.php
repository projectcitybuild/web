<?php

namespace App\Domains\Captcha;

use App\Domains\Captcha\Validator\Adapters\TurntileCaptchaValidator;
use App\Domains\Captcha\Validator\CaptchaValidator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class CaptchaProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            abstract: CaptchaValidator::class,
            concrete: TurntileCaptchaValidator::class,
        );
    }

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot(): void
    {
        Blade::directive('captcha_key', fn () => "<?php echo config('captcha.keys.site') ?>");
    }
}
