<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('pages', function(Blueprint $table) {
            $table->timestamp('date_added')->change();
            $table->timestamp('date_updated')->change();
        });

        Schema::table('pages', function(Blueprint $table) {
            $table->renameColumn('date_added', 'created_at');
            $table->renameColumn('date_updated', 'updated_at');
        });
    }

    public function down() {}
};
