<?php

namespace App\Http\Controllers\Panel\Audit;

use Altek\Accountant\Models\Ledger;
use App\Entities\Accounts\Models\Account;
use App\Entities\Donations\Models\Donation;
use App\Entities\Donations\Models\DonationPerk;
use App\Entities\Players\Models\MinecraftPlayer;
use App\Http\WebController;
use App\Library\Auditing\Contracts\Recordable;

class AuditController extends WebController
{
    private array $inspectableAudits = [
        'account' => Account::class,
        'donation' => Donation::class,
        'donation_perk' => DonationPerk::class,
        'minecraft_player' => MinecraftPlayer::class
    ];

    private function resolveModel($model, $key): Recordable
    {
        if (! in_array($model, array_keys($this->inspectableAudits))) {
            abort(404);
        }

        return $this->inspectableAudits[$model]::findOrFail($key);
    }

    private function humanLabel($model, $key): string
    {
        return class_basename(get_class($model)).' #'.$key;
    }

    public function index(string $model, string $key)
    {
        $auditingModel = $this->resolveModel($model, $key);
        $ledgers = $auditingModel->ledgers()->with('user')->latest()->get();
        $humanLabel = $this->humanLabel($auditingModel, $key);

        return view('admin.auditing.index')->with(compact('ledgers', 'auditingModel', 'humanLabel'));
    }

    public function show(Ledger $ledger)
    {
        $ledger->load('user');
        return view('admin.auditing.show')->with(compact('ledger'));
    }
}
