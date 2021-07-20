<?php

namespace App\Library\Auditing;

use Illuminate\Support\Facades\App;

/**
 * Allow a model to be audited.
 */
trait Recordable
{
    use \Altek\Accountant\Recordable;

    /**
     * Get the URL of the audit list for this model.
     */
    public function getAuditListUrl(): string
    {
        $resolver = App::make(AuditableClassResolver::class);
        $label = $resolver->getAuditListLabel($this);

        return route('front.panel.audits.index', ['label' => $label, 'key' => $this->getKey()]);
    }
}
