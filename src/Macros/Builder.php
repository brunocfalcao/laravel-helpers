<?php

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

// Macro for updating without triggering model events
Builder::macro('updateSilently', function (array $attributes) {
    return $this->getModel()->withoutEvents(function () use ($attributes) {
        return $this->update($attributes);
    });
});

// Macro for creating without triggering model events
Builder::macro('createSilently', function (array $attributes) {
    return $this->getModel()->withoutEvents(function () use ($attributes) {
        return $this->create($attributes);
    });
});

Builder::macro('toSqlWithBindings', function () {
    $sql = $this->toSql();
    $bindings = $this->getBindings();

    foreach ($bindings as $binding) {
        $sql = preg_replace('/\?/', "'{$binding}'", $sql, 1);
    }

    return $sql;
});

/**
 * Quickly creates the join() query builder method, using two tables
 * that follow the table naming convention.
 *
 * ::quickJoin('cars', 'drivers') will make a join like
 * join cars where cars.driver_id = drivers.id.
 *
 * As advanced usage:
 * $query->quickJoin(['questionnaires', 'clients', 'locations'], ['users', 'profiles']);
 * generates
 *   ->join('questionnaires', 'questionnaires.id', '=', 'clients.questionnaire_id')
 *   ->join('clients', 'clients.id', '=', 'locations.client_id')
 *   ->join('users', 'users.id', '=', 'profiles.user_id')
 *
 * You can have multiple array arguments:
 * [tablePK, tableFK&PK, TableFK], [tablePK, tableFK], ...
 */
Builder::macro('quickJoin', function (...$args) {
    $args = is_array($args[0]) ? $args[0] : $args;

    if (count($args) < 2) {
        throw new InvalidArgumentException('At least two tables are required for inner join. Args: '.json_encode($args));
    }

    for ($i = 0; $i < count($args) - 1; $i++) {
        $pkRaw = $args[$i];
        $fkRaw = $args[$i + 1];

        $pk = Str::before($pkRaw, ' as ');
        $pkAlias = Str::after($pkRaw, ' as ') ?: $pk;

        $fk = Str::before($fkRaw, ' as ');
        $fkAlias = Str::after($fkRaw, ' as ') ?: $fk;

        $singularFk = Str::singular($pk);
        $this->join("{$pk} as {$pkAlias}", "{$pkAlias}.id", '=', "{$fkAlias}.{$singularFk}_id");
    }

    return $this;
});

// A very quick shortcut to do a where('xxx')->get().
Builder::macro('getWhere', function (...$args) {
    return $this->where($args)->get();
});

/**
 * An upgraded version of the sync() (many to many).
 * The default sync() will delete all the previous records and just keep
 * the ones passed on this sync() method. This method will preserve
 * the previous values, already added before, and just guarantee the ones passed on the
 * are kept as unique.
 *
 * The pivotData[] also gets updated/inserted.
 *
 * The last argument, means if we should take the pivot data on the where clause
 * to detect the repeated ones.
 */
Builder::macro('syncOnlyThese', function ($relatedIds, array $pivotData = [], bool $considerPivot = false) {
    // Ensure $relatedIds is always an array
    $relatedIds = is_array($relatedIds) ? $relatedIds : [$relatedIds];

    foreach ($relatedIds as $relatedId) {
        // Attach the new relationship with pivot data for each ID
        $this->attach($relatedId, $pivotData);

        // Get the parent model's primary key name and value
        $parentKeyName = $this->getParentKeyName();
        $parentKeyValue = $this->getParentKey();

        // Get the related model's primary key name
        $relatedKeyName = $this->getRelatedKeyName();

        // Prepare the base query to find duplicate entries for each ID
        $query = $this->newPivotStatement()->where($parentKeyName, $parentKeyValue)->where($relatedKeyName, $relatedId);

        // If considering pivot values, add them to the query
        if ($considerPivot) {
            foreach ($pivotData as $key => $value) {
                $query->where($key, $value);
            }
        }

        // Get IDs of records to keep (the most recent ones)
        $recordsToKeep = $query->latest()->take(1)->pluck('id');

        // Delete all other duplicate entries except the most recent one for each ID
        $query->whereNotIn('id', $recordsToKeep)->delete();
    }
});
