<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('pages', function(Blueprint $table) {
            $table->unsignedBigInteger('page_id')->change();
        });

        rescue(function() {
            Schema::table('pages', function(Blueprint $table) {
                $table->foreignId('language_id')->change()->constrained('languages', 'language_id');
            });
        });
    }

    public function down()
    {
        try {
            Schema::table('pages', function(Blueprint $table) {
                $table->dropForeignKeyIfExists('language_id');
            });
        } catch (\Exception $e) {
        }
    }
};
