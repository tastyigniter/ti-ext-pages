<?php

namespace Igniter\Pages\Database\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if ($this->primaryKeyExists()) {
            return;
        }

        Schema::table('pages', function(Blueprint $table) {
            $table->unsignedBigInteger('page_id', true)->change();
        });
    }

    public function down() {}

    protected function primaryKeyExists()
    {
        $tableName = 'pages';
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'sqlite') {
            return DB::table('sqlite_master')
                ->where('type', 'table')
                ->where('name', $tableName)
                ->whereRaw("sql LIKE '%PRIMARY KEY (`page_id`)%'")
                ->exists();
        }

        if ($driver === 'pgsql') {
            return DB::table('information_schema.table_constraints AS tc')
                ->join('information_schema.key_column_usage AS kcu', function($join) {
                    $join->on('tc.constraint_name', '=', 'kcu.constraint_name')
                        ->on('tc.table_name', '=', 'kcu.table_name');
                })
                ->where('tc.constraint_type', 'PRIMARY KEY')
                ->where('tc.table_name', $tableName)
                ->where('kcu.column_name', 'page_id')
                ->exists();
        }


        $tablePrefix = Schema::getConnection()->getTablePrefix();
        $query = DB::raw('SHOW KEYS FROM '.$tablePrefix.$tableName." WHERE Key_name='PRIMARY' AND Column_name='page_id'");

        return DB::select($query->getValue(Schema::getConnection()->getSchemaGrammar()));
    }
};
