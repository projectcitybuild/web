<?php

namespace App\Http\Controllers\Panel\Audit;

use Altek\Accountant\Models\Ledger;
use App\Http\WebController;
use App\Library\Auditing\AuditableClassResolver;

class AuditController extends WebController
{
    /**
     * Show all of the audits for a model.
     * @see AuditableClassResolver for a list of labels and corresponding classes
     *
     * @param string $label the human-readable model label
     * @param string $key the primary key of the model
     * @return \Illuminate\Http\Response
     */
    public function index(string $label, string $key, AuditableClassResolver $resolver)
    {
        $auditingClass = $resolver->resolveLabelToClass($label);
        $auditingModel = $auditingClass::findOrFail($key);
        $ledgers = $auditingModel->ledgers()->with('user')->latest()->get();

        return view('admin.auditing.index')->with(compact('ledgers', 'auditingModel'));
    }

    /**
     * Show details of an audit log ledger entry.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Ledger $ledger)
    {
        $ledger->load('user');

        return view('admin.auditing.show')->with(compact('ledger'));
    }
}
