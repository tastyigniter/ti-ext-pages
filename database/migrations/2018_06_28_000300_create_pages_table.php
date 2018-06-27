<?php

use Illuminate\Database\Migrations\Migration;

class CreatePagesTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('pages'))
            return;

        Schema::create('pages', function ($table) {
            $table->engine = 'InnoDB';
            $table->integer('page_id', TRUE);
            $table->integer('language_id');
            $table->string('name');
            $table->string('title');
            $table->string('heading')->nullable();
            $table->text('content');
            $table->string('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->integer('layout_id')->nullable();
            $table->text('navigation')->nullable();
            $table->dateTime('date_added');
            $table->dateTime('date_updated');
            $table->boolean('status');
        });
    }

    public function down()
    {
        Schema::dropIfExists('pages');
    }
}