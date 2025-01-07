<?php

namespace Igniter\Pages\Database\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        $tableName = 'pages';
        $primaryKey = 'id';
        $driver = DB::getDriverName();

        switch ($driver) {
            case 'mysql':
                $this->ensurePrimaryKeyHasAutoIncrementForMysql($tableName, $primaryKey);
            case 'pgsql':
                $this->ensurePrimaryKeyHasAutoIncrementForPostgresql($tableName, $primaryKey);
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
        $sequenceCheck = DB::selectOne("
            SELECT pg_get_serial_sequence(?, ?) as sequence
        ", [$tableName, $primaryKey]);

        if (is_null($sequenceCheck->sequence)) {
            $sequenceName = "{$tableName}_{$primaryKey}_seq";
            DB::statement("CREATE SEQUENCE $sequenceName;");
            DB::statement("ALTER TABLE $tableName ALTER COLUMN $primaryKey SET DEFAULT nextval('$sequenceName');");
            DB::statement("ALTER SEQUENCE $sequenceName OWNED BY $tableName.$primaryKey;");
        }
    }
};
