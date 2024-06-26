<?php

namespace App\Http\Controllers\Me;

use App\Domains\Accounts\Data\Rules\UsernameValidationRules;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class UpdateUsernameController extends Controller
{
    use UsernameValidationRules;

    /**
     * @throws ValidationException
     */
    public function update(Request $request): JsonResponse
    {
        Validator::make($request->input(), [
            'username' => $this->usernameRules(),
        ])->validate();

        $account = $request->user();
        $account->username = $request->get('username');
        $account->save();

        return response()->json($account);
    }
}
