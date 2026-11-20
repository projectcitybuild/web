<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const PRIMARY_KEYS = [
        'accounts' => 'id',
        'account_email_changes' => 'id',
        'badges' => 'id',
        'badges_pivot' => 'id',
        'donations' => 'id',
        'donation_tiers' => 'id',
        'game_player_bans' => 'id',
        'minecraft_warps' => 'id',
        'players' => 'id',
        'player_warnings' => 'id',
        'roles' => 'id',
        'roles_accounts' => 'id',
        'servers' => 'id',
        'server_tokens' => 'id',
    ];
    private const UNSIGNED_INT_MAX = '4294967295';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $this->convert('BIGINT UNSIGNED');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->convert('INT UNSIGNED', checkUnsignedIntRange: true);
    }

    private function convert(string $targetType, bool $checkUnsignedIntRange = false): void
    {
        if (! in_array(DB::connection()->getDriverName(), ['mariadb', 'mysql'], true)) {
            throw new RuntimeException('This migration requires MariaDB or MySQL.');
        }

        $primaryColumns = $this->primaryColumns();
        $foreignKeys = $this->foreignKeysReferencingPrimaryKeys();
        $columns = $this->columnsToModify($primaryColumns, $foreignKeys);

        foreach ($columns as $table => $tableColumns) {
            foreach ($tableColumns as $column => $metadata) {
                $this->assertUnsignedIntegerColumn($metadata, $column === (self::PRIMARY_KEYS[$table] ?? null));

                if ($checkUnsignedIntRange) {
                    $maximum = DB::table($table)->max($column);

                    if ($maximum !== null && $this->exceedsUnsignedIntMaximum($maximum)) {
                        throw new RuntimeException("Cannot reduce {$table}.{$column} to INT UNSIGNED because it contains a value greater than ".self::UNSIGNED_INT_MAX.'.');
                    }
                }
            }
        }

        $modifiedTables = [];

        try {
            $this->dropForeignKeys($foreignKeys);

            foreach ($columns as $table => $tableColumns) {
                $this->alterColumns([$table => $tableColumns], $targetType);
                $modifiedTables[$table] = $tableColumns;
            }

            $this->restoreForeignKeys($foreignKeys);
        } catch (Throwable $exception) {
            try {
                $this->dropForeignKeys($this->foreignKeysReferencingPrimaryKeys());

                foreach ($modifiedTables as $table => $tableColumns) {
                    $this->alterColumns([$table => $tableColumns], null);
                }

                $this->restoreForeignKeys($foreignKeys);
            } catch (Throwable $recoveryException) {
                throw new RuntimeException(
                    'The key conversion failed and the original schema could not be restored.',
                    previous: $recoveryException,
                );
            }

            throw $exception;
        }
    }

    /** @return array<string, object> */
    private function primaryColumns(): array
    {
        $columns = [];

        foreach (self::PRIMARY_KEYS as $table => $column) {
            $metadata = $this->columnMetadata($table, $column);

            if ($metadata === null || $metadata->COLUMN_KEY !== 'PRI' || strtolower($metadata->EXTRA) !== 'auto_increment') {
                throw new RuntimeException("Expected {$table}.{$column} to be an auto-incrementing primary key.");
            }

            $this->assertUnsignedIntegerColumn($metadata, true);
            $columns[$table] = $metadata;
        }

        return $columns;
    }

    /** @return array<int, array<string, mixed>> */
    private function foreignKeysReferencingPrimaryKeys(): array
    {
        $rows = DB::select(<<<'SQL'
            SELECT
                k.CONSTRAINT_NAME,
                k.TABLE_NAME,
                k.COLUMN_NAME,
                c.COLUMN_TYPE,
                c.IS_NULLABLE,
                c.COLUMN_DEFAULT,
                c.COLUMN_COMMENT,
                c.EXTRA,
                k.REFERENCED_TABLE_NAME,
                k.REFERENCED_COLUMN_NAME,
                k.ORDINAL_POSITION,
                r.UPDATE_RULE,
                r.DELETE_RULE
            FROM information_schema.KEY_COLUMN_USAGE k
            INNER JOIN information_schema.COLUMNS c
                ON c.TABLE_SCHEMA = k.TABLE_SCHEMA
                AND c.TABLE_NAME = k.TABLE_NAME
                AND c.COLUMN_NAME = k.COLUMN_NAME
            INNER JOIN information_schema.REFERENTIAL_CONSTRAINTS r
                ON r.CONSTRAINT_SCHEMA = k.CONSTRAINT_SCHEMA
                AND r.CONSTRAINT_NAME = k.CONSTRAINT_NAME
                AND r.TABLE_NAME = k.TABLE_NAME
            WHERE k.CONSTRAINT_SCHEMA = DATABASE()
                AND k.REFERENCED_TABLE_SCHEMA = DATABASE()
                AND k.REFERENCED_TABLE_NAME IS NOT NULL
            ORDER BY k.TABLE_NAME, k.CONSTRAINT_NAME, k.ORDINAL_POSITION
            SQL);

        $constraints = [];

        foreach ($rows as $row) {
            $key = $row->TABLE_NAME.'.'.$row->CONSTRAINT_NAME;

            $constraints[$key] ??= [
                'name' => $row->CONSTRAINT_NAME,
                'table' => $row->TABLE_NAME,
                'columns' => [],
                'referenced_table' => $row->REFERENCED_TABLE_NAME,
                'referenced_columns' => [],
                'update_rule' => $row->UPDATE_RULE,
                'delete_rule' => $row->DELETE_RULE,
                'affected_columns' => [],
            ];

            $constraints[$key]['columns'][] = $row->COLUMN_NAME;
            $constraints[$key]['referenced_columns'][] = $row->REFERENCED_COLUMN_NAME;

            if ((self::PRIMARY_KEYS[$row->REFERENCED_TABLE_NAME] ?? null) === $row->REFERENCED_COLUMN_NAME) {
                $constraints[$key]['affected_columns'][$row->COLUMN_NAME] = $row;
            }
        }

        return array_values(array_filter(
            $constraints,
            fn (array $constraint): bool => $constraint['affected_columns'] !== [],
        ));
    }

    /** @param array<string, object> $primaryColumns
     * @param  array<int, array<string, mixed>>  $foreignKeys
     * @return array<string, array<string, object>>
     */
    private function columnsToModify(array $primaryColumns, array $foreignKeys): array
    {
        $columns = [];

        foreach ($primaryColumns as $table => $metadata) {
            $columns[$table][$metadata->COLUMN_NAME] = $metadata;
        }

        foreach ($foreignKeys as $foreignKey) {
            foreach ($foreignKey['affected_columns'] as $column => $metadata) {
                $this->assertUnsignedIntegerColumn($metadata, false);
                $columns[$foreignKey['table']][$column] = $metadata;
            }
        }

        return $columns;
    }

    private function columnMetadata(string $table, string $column): ?object
    {
        return DB::selectOne(<<<'SQL'
            SELECT TABLE_NAME, COLUMN_NAME, COLUMN_TYPE, COLUMN_KEY, IS_NULLABLE, COLUMN_DEFAULT, COLUMN_COMMENT, EXTRA
            FROM information_schema.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?
            SQL, [$table, $column]);
    }

    private function assertUnsignedIntegerColumn(object $metadata, bool $autoIncrement): void
    {
        $type = preg_replace('/\(\d+\)/', '', strtolower($metadata->COLUMN_TYPE));

        if (! in_array($type, ['int unsigned', 'bigint unsigned'], true)) {
            throw new RuntimeException("Expected {$metadata->TABLE_NAME}.{$metadata->COLUMN_NAME} to be an unsigned INT or BIGINT.");
        }

        $extra = strtolower(trim($metadata->EXTRA));
        $expectedExtra = $autoIncrement ? 'auto_increment' : '';

        if ($extra !== $expectedExtra) {
            throw new RuntimeException("Unexpected column attributes on {$metadata->TABLE_NAME}.{$metadata->COLUMN_NAME}.");
        }
    }

    private function exceedsUnsignedIntMaximum(mixed $value): bool
    {
        $value = ltrim((string) $value, '0') ?: '0';

        return strlen($value) > strlen(self::UNSIGNED_INT_MAX)
            || (strlen($value) === strlen(self::UNSIGNED_INT_MAX) && strcmp($value, self::UNSIGNED_INT_MAX) > 0);
    }

    /** @param array<string, array<string, object>> $columns */
    private function alterColumns(array $columns, ?string $targetType): void
    {
        $connection = DB::connection();
        $grammar = $connection->getQueryGrammar();

        foreach ($columns as $table => $tableColumns) {
            $modifications = [];

            foreach ($tableColumns as $metadata) {
                $type = $targetType ?? strtoupper($metadata->COLUMN_TYPE);
                $definition = $type.' '.($metadata->IS_NULLABLE === 'YES' ? 'NULL' : 'NOT NULL');

                if ($metadata->COLUMN_DEFAULT !== null || $metadata->IS_NULLABLE === 'YES') {
                    $default = $metadata->COLUMN_DEFAULT === null
                        ? 'NULL'
                        : $this->defaultLiteral($metadata->COLUMN_DEFAULT);

                    $definition .= ' DEFAULT '.$default;
                }

                if (strtolower(trim($metadata->EXTRA)) === 'auto_increment') {
                    $definition .= ' AUTO_INCREMENT';
                }

                if ($metadata->COLUMN_COMMENT !== '') {
                    $definition .= ' COMMENT '.$connection->getPdo()->quote($metadata->COLUMN_COMMENT);
                }

                $modifications[] = 'MODIFY COLUMN '.$grammar->wrap($metadata->COLUMN_NAME).' '.$definition;
            }

            DB::statement('ALTER TABLE '.$grammar->wrapTable($table).' '.implode(', ', $modifications));
        }
    }

    private function defaultLiteral(mixed $default): string
    {
        $default = (string) $default;

        if (strtoupper(trim($default)) === 'NULL') {
            return 'NULL';
        }

        if (preg_match('/^-?\d+(?:\.\d+)?$/', $default) === 1) {
            return $default;
        }

        return DB::connection()->getPdo()->quote($default);
    }

    /** @param array<int, array<string, mixed>> $foreignKeys */
    private function dropForeignKeys(array $foreignKeys): void
    {
        $grammar = DB::connection()->getQueryGrammar();

        foreach ($foreignKeys as $foreignKey) {
            DB::statement(
                'ALTER TABLE '.$grammar->wrapTable($foreignKey['table'])
                .' DROP FOREIGN KEY '.$grammar->wrap($foreignKey['name']),
            );
        }
    }

    /** @param array<int, array<string, mixed>> $foreignKeys */
    private function restoreForeignKeys(array $foreignKeys): void
    {
        $grammar = DB::connection()->getQueryGrammar();

        foreach ($foreignKeys as $foreignKey) {
            $columns = implode(', ', array_map($grammar->wrap(...), $foreignKey['columns']));
            $referencedColumns = implode(', ', array_map($grammar->wrap(...), $foreignKey['referenced_columns']));
            $deleteRule = $this->foreignKeyRule($foreignKey['delete_rule']);
            $updateRule = $this->foreignKeyRule($foreignKey['update_rule']);

            DB::statement(
                'ALTER TABLE '.$grammar->wrapTable($foreignKey['table'])
                .' ADD CONSTRAINT '.$grammar->wrap($foreignKey['name'])
                .' FOREIGN KEY ('.$columns.') REFERENCES '.$grammar->wrapTable($foreignKey['referenced_table'])
                .' ('.$referencedColumns.') ON DELETE '.$deleteRule.' ON UPDATE '.$updateRule,
            );
        }
    }

    private function foreignKeyRule(string $rule): string
    {
        $rule = strtoupper($rule);

        if (! in_array($rule, ['CASCADE', 'SET NULL', 'RESTRICT', 'NO ACTION', 'SET DEFAULT'], true)) {
            throw new RuntimeException("Unsupported foreign key rule: {$rule}.");
        }

        return $rule;
    }
};
