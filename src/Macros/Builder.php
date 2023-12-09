<?php

/**
 * $query->quickJoin(['questionnaires', 'clients', 'locations'], ['users', 'profiles']);
 * generates
 *   ->join('questionnaires', 'questionnaires.id', '=', 'clients.questionnaire_id')
 *   ->join('clients', 'clients.id', '=', 'locations.client_id')
 *   ->join('users', 'users.id', '=', 'profiles.user_id')
 *
 * You can have multiple array arguments:
 * [tablePK, tableFK&PK, TableFK], [tablePK, tableFK], ...
 */

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

Builder::macro('toSqlWithBindings', function () {
    $sql = $this->toSql();
    $bindings = $this->getBindings();

    foreach ($bindings as $binding) {
        $sql = preg_replace('/\?/', "'{$binding}'", $sql, 1);
    }

    return $sql;
});

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

Builder::macro('getWhere', function (...$args) {
    return $this->where($args)->get();
});
