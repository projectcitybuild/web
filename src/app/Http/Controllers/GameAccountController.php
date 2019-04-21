<?php

namespace Interfaces\Web\Controllers;

use Illuminate\Support\Facades\View;
use Illuminate\Contracts\Validation\Factory;
use Illuminate\Http\Request;
use App\Entities\Players\Repositories\MinecraftPlayerRepository;
use App\Entities\Players\Repositories\MinecraftPlayerAliasRepository;
use Illuminate\Contracts\Auth\Guard as Auth;
use App\Entities\Players\Services\MinecraftPlayerLookupService;
use App\Entities\Servers\Services\PlayerFetching\Api\Mojang\MojangApiService;
use Illuminate\Support\Carbon;

class GameAccountController extends WebController
{

    /**
     * @var MinecraftPlayerRepository
     */
    private $minecraftPlayerRepository;

    /**
     * @var MinecraftPlayerAliasRepository
     */
    private $minecraftAliasRepository;

    /**
     * @var MojangApiService
     */
    private $mojangApiService;

    /**
     * @var Auth
     */
    private $auth;

    public function __construct(MinecraftPlayerRepository $minecraftPlayerRepository,
                                MinecraftPlayerAliasRepository $minecraftAliasRepository,
                                MojangApiService $mojangApiService,
                                Auth $auth) 
    {
        $this->minecraftPlayerRepository = $minecraftPlayerRepository;
        $this->minecraftAliasRepository = $minecraftAliasRepository;
        $this->mojangApiService = $mojangApiService;
        $this->auth = $auth;
    }

    public function showView()
    {
        $accountId = $this->auth->id();

        $minecraftUuid = '';
        $minecraftAliases = [];

        if ($accountId !== null) {
            $minecraftPlayer = $this->minecraftPlayerRepository->getByAccountId($accountId);
            $minecraftUuid = $minecraftPlayer->uuid ?: '';

            if ($minecraftPlayer !== null) {
                $minecraftAliases = $minecraftPlayer->aliases ?: [];
            }
        }

        return view('front.pages.account.account-games', [
            'minecraft' => [
                'uuid'      => $minecraftUuid,
                'aliases'   => $minecraftAliases,
            ],
        ]);
    }
    
    public function saveAccounts(Request $request, Factory $validation)
    {
        if (!$this->auth->check()) {
            abort(401);
        }

        if ($request->has('minecraft')) {
            return $this->saveMinecraft($request, $validation);
        }
    }

    private function saveMinecraft(Request $request, Factory $validation)
    {
        $validator = $validation->make($request->all(), [
            'minecraft_uuid' => 'required',
        ]);

        if ($validator->failed()) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors($validator->errors());
        }

        $uuid = $request->get('minecraft_uuid');
        $uuid = str_replace('-', '', $uuid);
        $player = $this->minecraftPlayerRepository->getByUuid($uuid);

        $nameHistory = $this->mojangApiService->getNameHistoryOf($uuid);
        if ($nameHistory === null) {
            $validator->errors()->add('minecraft_uuid', 'Player does not exist with this UUID');

            return redirect()
                ->back()
                ->withInput()
                ->withErrors($validator->errors());
        }

        if ($player === null) {
            $player = $this->minecraftPlayerRepository->store($uuid, now(), $this->auth->id());
        }

        $existingAliases = $player->aliases();

        $aliases = $nameHistory->getNameChanges();
        foreach ($aliases as $nameChange) {
            $registeredAt = $nameChange->getChangeDate() !== null
                ? Carbon::createFromTimestamp($nameChange->getChangeDate())
                : null;
            
            $existingAlias = $existingAliases
                ->where('alias', $nameChange->getAlias())
                ->first();

            if ($existingAlias !== null) {
                if ($existingAlias->registered_at !== $registeredAt) {
                    $existingAlias->registered_at = $registeredAt;
                    $existingAlias->save();
                }
            } else {
                $this->minecraftAliasRepository->store(
                    $player->getKey(),
                                                       $nameChange->getAlias(),
                                                       $registeredAt
                );
            }
        }

        return redirect()
            ->route('front.account.games')
            ->with('success', 'Minecraft UUID and name history updated');
    }
}
