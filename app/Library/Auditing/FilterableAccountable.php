<?php

namespace App\Library\Auditing;

use Altek\Accountant\Models\Ledger;

/**
 * Trait that allows filtering audits by the account that made them
 */
trait FilterableAccountable
{
    /**
     * A model has created many auditable changes
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function authoredLedgerEntries()
    {
        return $this->morphMany(Ledger::class, 'accountable');
    }
}
