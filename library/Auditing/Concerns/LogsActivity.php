<?php

namespace Library\Auditing\Concerns;

use Altek\Eventually\Eventually;
use Illuminate\Database\Eloquent\Model;
use Library\Auditing\AuditAttributes;
use Spatie\Activitylog\ActivityLogger;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity as ParentLogsActivity;

trait LogsActivity
{
    use ParentLogsActivity {
        ParentLogsActivity::bootLogsActivity as parentBootLogsActivity;
    }

    /**
     * Set the attributes to be audited and their types
     *
     * @return AuditAttributes
     */
    abstract public function auditAttributeConfig(): AuditAttributes;

    /**
     * Default settings for logging
     *
     * @return LogOptions
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->dontSubmitEmptyLogs()
            ->logOnly($this->auditAttributeConfig()->getAttributeNames())
            ->logOnlyDirty();
    }

    /**
     * Set up custom logging events
     *
     * @return void
     */
    protected static function bootLogsActivity(): void
    {
        // If this class uses the Eventually trait, register logging events to it.
        if (collect(class_uses_recursive(static::class))->contains(Eventually::class)) {
            self::registerEventuallyLogging();
        }

        self::parentBootLogsActivity();
    }

    private static function registerEventuallyLogging(): void
    {
        if (static::eventsToBeRecorded()->contains('synced')) {
            // On the syncing event (i.e. before the sync is done), store the old value
            static::syncing(function (Model $model) {
                static::addOldAttributes($model);
            });

            // Remove the event from the to be recorded list to prevent it being handled normally
            static::forgetRecordEvent('synced');

            static::synced(function (Model $model, string $relation) {
                // If no loggable changes have been made, and this model is not configured
                // to submit empty logs, then skip
                $attrs = $model->attributeValuesToBeLogged('updated');
                if ($model->isLogEmpty($attrs) && ! $model->activitylogOptions->submitEmptyLogs) {
                    return;
                }

                app(ActivityLogger::class)
                    ->useLog($model->getLogNameToUse())
                    ->performedOn($model)
                    ->withProperties($attrs)
                    ->event('synced')
                    ->log($model->getDescriptionForEvent("{$relation} update"));
            });
        }
    }

    /**
     * Set old attributes for this event
     *
     * @param  Model  $model
     * @return void
     */
    private static function addOldAttributes(Model $model): void
    {
        $oldValues = (new static())->setRawAttributes($model->getRawOriginal());
        $model->oldAttributes = static::logChanges($oldValues);
    }

    /**
     * Remove an event from the to be recorded list
     *
     * @param $event
     * @return void
     */
    private static function forgetRecordEvent($event): void
    {
        if (isset(static::$recordEvents)) {
            static::$recordEvents = static::eventsToBeRecorded()->reject($event)->toArray();
        }
    }
}
