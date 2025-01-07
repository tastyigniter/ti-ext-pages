<?php

namespace Igniter\Pages\Database\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        $tableName = 'pages';
        $primaryKey = 'page_id';
        $driver = DB::getDriverName();

        switch ($driver) {
            case 'mysql':
                $this->ensurePrimaryKeyHasAutoIncrementForMysql($tableName, $primaryKey);
                break;
            case 'pgsql':
                $this->ensurePrimaryKeyHasAutoIncrementForPostgresql($tableName, $primaryKey);
                break;
        }
    }

    public function down() {}

    protected function ensurePrimaryKeyHasAutoIncrementForMysql(string $tableName, string $primaryKey)
    {
        $columnInfo = DB::selectOne("
            SELECT EXTRA 
            FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_SCHEMA = DATABASE() 
              AND TABLE_NAME = ? 
              AND COLUMN_NAME = ?
        ", [$tableName, $primaryKey]);

        if (!isset($columnInfo->EXTRA) || stripos($columnInfo->EXTRA, 'auto_increment') === false) {
            DB::statement("ALTER TABLE `$tableName` MODIFY `$primaryKey` INT NOT NULL AUTO_INCREMENT;");
        }
    }

    protected function ensurePrimaryKeyHasAutoIncrementForPostgresql(string $tableName, string $primaryKey)
    {
        // Check if the column is backed by a sequence
        $sequenceCheck = DB::selectOne("
        SELECT pg_get_serial_sequence(?, ?) AS sequence
    ", [$tableName, $primaryKey]);

        if (isset($sequenceCheck->sequence)) {
            // Check if the default value is set to use the sequence
            $defaultCheck = DB::selectOne("
            SELECT column_default 
            FROM information_schema.columns 
            WHERE table_name = ? 
              AND column_name = ?
        ", [$tableName, $primaryKey]);

            if (!str_contains($defaultCheck->column_default ?? '', 'nextval')) {
                DB::statement("ALTER TABLE $tableName ALTER COLUMN $primaryKey SET DEFAULT nextval('{$sequenceCheck->sequence}');");
            }
        } else {
            // Create a new sequence and link it to the column
            $sequenceName = "{$tableName}_{$primaryKey}_seq";
            DB::statement("CREATE SEQUENCE $sequenceName;");
            DB::statement("ALTER TABLE $tableName ALTER COLUMN $primaryKey SET DEFAULT nextval('$sequenceName');");
            DB::statement("ALTER SEQUENCE $sequenceName OWNED BY $tableName.$primaryKey;");
        }
    }
};
