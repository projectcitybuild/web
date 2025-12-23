<?php

namespace App\Http\Controllers\Front\Account\Settings;

use App\Http\Controllers\WebController;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use PragmaRX\Google2FA\Google2FA;

class MfaSetupController extends WebController
{
    public function __construct(
        private readonly Google2FA $google2FA,
    ) {}

    public function __invoke(Request $request)
    {
        if ($request->user()->is_totp_enabled) {
            abort(403);
        }

        $secret = Crypt::decryptString($request->user()->totp_secret);
        $backupCode = Crypt::decryptString($request->user()->totp_backup_code);

        $qrUrl = $this->google2FA->getQRCodeUrl(
            config('app.name'),
            $request->user()->email,
            $secret
        );

        $qrSvg = $this->getWriter()->writeString($qrUrl);

        return view('front.pages.account.settings.mfa-setup')->with([
            'backupCode' => $backupCode,
            'qrSvg' => $qrSvg,
            'secretKey' => $secret,
        ]);
    }

    /**
     * Generate the QR Code writer.
     */
    private function getWriter(): Writer
    {
        return new Writer(
            new ImageRenderer(
                new RendererStyle(150, 0),
                new SvgImageBackEnd
            )
        );
    }
}
