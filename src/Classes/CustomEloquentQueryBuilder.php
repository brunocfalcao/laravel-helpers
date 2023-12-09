<?php

namespace Brunocfalcao\LaravelHelpers\Classes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class CustomEloquentQueryBuilder extends Builder
{
    public $lastTableOrAliasJoined;

    /**
     * It will bring a new table to the inner join where the current
     * eloquent query builder table will connect to this table by it's
     * foreign key.
     *
     * Imagine you have users and clients. Users have client_id on them.
     *
     * You can do:
     *
     * $user->query()->upTo('clients') and what it does it an inner join
     * between your users and clients:
     * select * from users inner join clients on clients.id = users.client_id.
     *
     * You can chain as many upTo() as you want.
     *
     * @return Builder
     */
    public function upTo(string $table, string $parentTable = null)
    {
        $alias = null;
        if (Str::contains($table, ' as ')) {
            [$table, $alias] = explode(' as ', $table);
        }

        $tableOrAlias = $alias ?? $table;
        $parentTable ??= $this->lastTableOrAliasJoined ?? $this->getModel()->getTable();

        $joinTable = $alias ? "{$table} as {$alias}" : $table;

        $this->join(
            $joinTable,
            "{$tableOrAlias}.id",
            '=',
            "{$parentTable}.".Str::singular($table).'_id'
        );

        $this->lastTableOrAliasJoined = $tableOrAlias;

        return $this;
    }

    /**
     * This method allows to bring a new table that is related to the previous
     * contextualized one, but it's not a parent of the previous one.
     *
     * Basically it's on an upTo() because it doesn't climb up on the
     * relacional model, but it's more like a transveral retrieval from another
     * table that connects to the parent one.
     *
     * @return Builder
     */
    public function bring(string $table, string $primaryKeyOnParent = null, string $foreignKeyOnCurrent = null, string $parentTable = null)
    {
        $alias = null;
        if (Str::contains($table, ' as ')) {
            [$table, $alias] = explode(' as ', $table);
        }

        $tableOrAlias = $alias ?? $table;
        $parentTable ??= $this->lastTableOrAliasJoined ?? $this->getModel()->getTable();
        $primaryKeyOnParent ??= "{$parentTable}.id";

        $joinTable = $alias ? "{$table} as {$alias}" : $table;

        // Determine the foreign key
        $foreignKey = $foreignKeyOnCurrent ?? "{$tableOrAlias}.".Str::singular($parentTable).'_id';

        $this->join(
            $joinTable,
            $primaryKeyOnParent,
            '=',
            $foreignKey
        );

        $this->lastTableOrAliasJoined = $tableOrAlias;

        return $this;
    }
}
