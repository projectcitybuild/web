<?php

namespace App\Routes\Http\Web\Controllers;

use Illuminate\Http\Request;
use App\Shared\Exceptions\UnauthorisedException;
use App\Routes\Http\Web\WebController;
use App\Modules\Discourse\Services\Authentication\DiscourseAuthService;
use App\Modules\Discourse\Services\Authentication\DiscoursePayload;
use GuzzleHttp\Client;
use Auth;
use Illuminate\Contracts\Validation\Factory;
use App\Modules\Accounts\Models\Account;
use App\Modules\Discourse\Services\Api\DiscourseAdminApi;

class TempEmailAdminController extends WebController {

    /**
     * @var DiscourseAuthService
     */
    private $discourseAuthService;

    public function __construct(DiscourseAuthService $discourseAuthService) {
        $this->discourseAuthService = $discourseAuthService;
    }

    private function authenticate(Client $client) {
        if(!Auth::check()) {
            abort(403);
        }

        $user = Auth::user();
        // $externalId = $user->getKey();
        $externalId = 1;
        
        $response = $client->get('https://forums.projectcitybuild.com/users/by-external/'.$externalId.'.json');
        $result = json_decode($response->getBody(), true);

        $user = $result['user'];
        if($user === null) {
            abort(401);
        }

        $isAdmin = $user['admin'];
        if($isAdmin === null || !$isAdmin) {
            abort(401);
        }
    }

    private function getPayload(int $id, string $username, string $email) {
        return (new DiscoursePayload(''))
            ->setPcbId($id)
            // ->setUsername($username)
            ->setEmail($email)
            ->build();
    }

    public function showView(Client $client) {
        $this->authenticate($client);
        return view('admin-email-reset');
    }

    public function editEmail(Request $request, Factory $validation, DiscourseAdminApi $adminApi) {
        $this->authenticate($client);
        
        $validator = $validation->make($request->all(), [
            'old_email' => 'required|email',
            'new_email' => 'required|email',
        ]);

        $account = Account::where('email', $request->get('old_email'))->first();

        // $validator->after(function($v) use($account) {
        //     if($account === null) {
        //         $v->errors()->add('old_email', 'No account exists with that email');
        //     }
        // });

        if($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator->errors())
                ->withInput();
        }

        // $linkPayload = $this->getPayload(30, 'emfitty', $request->get('old_email'));
        // $linkResponse = $client->post('https://forums.projectcitybuild.com/admin/users/sync_sso', [
        //     'form_params' => $linkPayload,
        // ]);

        // dd('test');

        $syncPayload = $this->getPayload(30, 'emfitty', $request->get('new_email'));
        $syncResponse = $adminApi->requestSSOSync($syncPayload);

        // $account->email = $request->get('new_email');
        // $account->save();

        return redirect()
            ->route('temp-email')
            ->with('success', 'Email successfully set to ' . $request->get('new_email'));
    }
}
