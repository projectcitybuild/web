<?php

namespace Library\Discourse\Entities;

use Illuminate\Http\Request;

final class DiscoursePackedNonce
{
    public function __construct(
        public string $sso,
        public string $signature
    ) {}

    public static function fromRequest(Request $request): ?DiscoursePackedNonce
    {
        $sso = $request->get('sso');
        $sig = $request->get('sig');

        if (empty($sso) || empty($sig)) {
            return null;
        }
        return new DiscoursePackedNonce(sso: $sso, signature: $sig);
    }
}
