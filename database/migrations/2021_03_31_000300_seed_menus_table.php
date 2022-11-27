<?php

use Igniter\Pages\Models\Menu;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up()
    {
        Menu::syncAll();
    }

    public function down()
    {
    }
};
