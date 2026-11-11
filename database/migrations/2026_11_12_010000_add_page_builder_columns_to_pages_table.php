<?php

declare(strict_types=1);

namespace Igniter\Pages\Database\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('pages', 'sections')) {
            return;
        }

        Schema::table('pages', function(Blueprint $table): void {
            $table->string('editor', 20)->default('content')->after('content');
            $table->json('sections')->nullable()->after('editor');
        });
    }
};
