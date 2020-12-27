<?php

namespace App\Http\Controllers\Settings\Mfa;

use App\Http\WebController;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use PragmaRX\Google2FA\Google2FA;

class SetupMfaController extends WebController
{
    /**
     * @var Google2FA
     */
    private $google2FA;

    /**
     * EnableTotpController constructor.
     *
     * @param Google2FA $google2FA
     */
    public function __construct(Google2FA $google2FA)
    {
        $this->google2FA = $google2FA;
    }

    /**
     * Handle the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
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
            Crypt::decryptString($secret)
        );

        $qrSvg = $this->getWriter()->writeString($qrUrl);

        return view('front.pages.account.security.2fa-setup')->with([
            'backupCode' => $backupCode,
            'qrSvg' => $qrSvg,
            'secretKey' => $secret,
        ]);
    }

    /**
     * Generate the QR Code writer
     *
     * @return Writer
     */
    private function getWriter(): Writer
    {
        return new Writer(
            new ImageRenderer(
                new RendererStyle(150, 0),
                new SvgImageBackEnd()
            )
        );
    }
}
