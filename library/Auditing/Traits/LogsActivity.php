<?php

namespace Library\Auditing\Traits;

use Altek\Eventually\Eventually;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Spatie\Activitylog\ActivityLogger;
use Spatie\Activitylog\Traits\LogsActivity as ParentLogsActivity;

trait LogsActivity
{
    use ParentLogsActivity {
        ParentLogsActivity::bootLogsActivity as parentBootLogsActivity;
    }

    private static function registerEventuallyLogging(): void
    {
        // If this model should record the synced event
        if (static::eventsToBeRecorded()->contains('synced')) {
            // On the syncing event (i.e. before the sync is done), store the old value
            static::syncing(function (Model $model) {
                static::addOldAttributes($model);
            });

            static::synced(function (Model $model, string $relation) {
                // If no loggable changes have been made, and this model is not configured
                // to submit empty logs, then skip
                $attrs = $model->attributeValuesToBeLogged('updated');
                if ($model->isLogEmpty($attrs) && !$model->activitylogOptions->submitEmptyLogs) {
                    return;
                }

                // For each attribute, if it's a collection, get the model attribute specified
                $attrs = collect($attrs)
                    ->map(fn(array $element) => collect($element)
                        ->map(fn($attribute) => ($attribute instanceof Collection) ?
                            $attribute->pluck(static::getRelationshipField($relation)) : $attribute
                        ))
                    ->toArray();

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

    /**
     * Set old attributes for this event
     *
     * @param Model $model
     * @return void
     */
    private static function addOldAttributes(Model $model): void
    {
        $oldValues = (new static())->setRawAttributes($model->getRawOriginal());
        $model->oldAttributes = static::logChanges($oldValues);
    }

    /**
     * Get the desired attribute to log for a relationship
     *
     * @param $relationship string the relationship method
     * @return mixed
     */
    private static function getRelationshipField(string $relationship): mixed
    {
        if (!isset(static::$syncRecordFields)) {
            return 'id';
        }
        return collect(static::$syncRecordFields)->get($relationship, 'id');
    }
}
