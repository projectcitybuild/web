@switch($changeType)
    @case(\App\Core\Domains\Auditing\Changes\MultilineChange::class)
        <x-audit::multiline-diff
                :attribute="$attribute"
                :old="$change->getOldValue()"
                :new="$change->getNewValue()"
                :description="$description"
        />
        @break
    @case(\App\Core\Domains\Auditing\Changes\Change::class)
        <x-audit::inline-diff
                :attribute="$attribute"
                :old="$change->getOldValue()"
                :new="$change->getNewValue()"
                :description="$description"
        />
        @break
    @case(\App\Core\Domains\Auditing\Changes\BooleanChange::class)
        <x-audit::inline-diff
                :attribute="$attribute"
                :old="$change->getOldValue()"
                :new="$change->getNewValue()"
                :description="$description"
        >
            <x-slot:old>
                <x-audit-support::bool :value="$change->getOldValue()"/>
            </x-slot:old>
            <x-slot:new>
                <x-audit-support::bool :value="$change->getNewValue()"/>
            </x-slot:new>
        </x-audit::inline-diff>
        @break
    @case(\App\Core\Domains\Auditing\Changes\RelationshipChange::class)
        <x-audit::inline-diff
                :attribute="$attribute"
                :old="$change->getOldValue()"
                :new="$change->getNewValue()"
                :description="$description"
        >
            <x-slot:old>
                <x-audit-support::model :model="$change->getOldValue()"/>
            </x-slot:old>
            <x-slot:new>
                <x-audit-support::model :model="$change->getNewValue()"/>
            </x-slot:new>
        </x-audit::inline-diff>
        @break
    @case(\App\Core\Domains\Auditing\Changes\ArrayChange::class)
        <x-audit::inline-diff
                :attribute="$attribute"
                :plain="true"
                :old="$change->getOldValue()"
                :new="$change->getNewValue()"
                :description="$description"
        >
            <x-slot:old>
                <x-audit::wrapped-array-list :entries="$change->getOldValue()" exclude-type="added"/>
            </x-slot:old>
            <x-slot:new>
                <x-audit::wrapped-array-list :entries="$change->getNewValue()" exclude-type="removed"/>
            </x-slot:new>
        </x-audit::inline-diff>
        @break
    @default
        <div class="alert alert-warning">
            This change can't be displayed yet. Please report this to admins.
        </div>
@endswitch
