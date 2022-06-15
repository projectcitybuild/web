<?php

namespace Library\Auditing\Traits;

use Altek\Eventually\Eventually;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Spatie\Activitylog\ActivityLogger;
use Spatie\Activitylog\Traits\LogsActivity as ParentLogsActivity;

trait LogsActivity
{
    use ParentLogsActivity {
        ParentLogsActivity::bootLogsActivity as parentBootLogsActivity;
    }

    protected static function bootLogsActivity(): void
    {
        if (collect(class_uses_recursive(static::class))->doesntContain(Eventually::class)) {
            self::parentBootLogsActivity();
        }

        if (static::eventsToBeRecorded()->contains('synced')) {
            static::syncing(function (Model $model) {
                static::addOldAttributes($model);
            });

            static::forgetRecordEvent('synced');

            static::synced(function (Model $model, string $relation) {
                $attrs = $model->attributeValuesToBeLogged('updated');
                if ($model->isLogEmpty($attrs) && !$model->activitylogOptions->submitEmptyLogs) {
                    return;
                }
                /* Clean the relation: Extract names from permissions */
                $attrs = collect($attrs)
                    ->map(fn(array $element) => collect($element)
                        ->map(fn($attribute) => ($attribute instanceof Collection) ?
                            $attribute->pluck(static::getRelationshipField($relation)) : $attribute
                        ))
                    ->toArray();
                /* Save the diff */
                app(ActivityLogger::class)
                    ->useLog($model->getLogNameToUse())
                    ->performedOn($model)
                    ->withProperties($attrs)
                    ->event('synced')
                    ->log($model->getDescriptionForEvent("{$relation} update"));
            });
        }

        self::parentBootLogsActivity();
    }

    private static function addOldAttributes($model): void
    {
        $oldValues = (new static())->setRawAttributes($model->getRawOriginal());
        $model->oldAttributes = static::logChanges($oldValues);
    }

    private static function forgetRecordEvent($event)
    {
        static::$recordEvents = static::eventsToBeRecorded()->reject($event)->toArray();
    }

    private static function getRelationshipField($relationship)
    {
        if (!isset(static::$syncRecordFields)) {
            return 'id';
        }
        return collect(static::$syncRecordFields)->get($relationship, 'id');
    }
}
