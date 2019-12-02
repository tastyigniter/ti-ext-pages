<?php namespace Igniter\Pages\Database\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Schema;

class AlterColumnsOnPagesTable extends Migration
{
    public function up()
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn('name');
            $table->dropColumn('heading');

            $table->dropColumn('layout_id');
            $table->string('layout')->nullable();

            $table->dropColumn('navigation');
            $table->mediumText('metadata')->nullable();

            $table->integer('priority')->nullable();
        });

        DB::table('pages')->update(['layout' => 'static']);
    }

    public function down()
    {
    }
}