<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->string('logo')->nullable()->after('name');
            $table->string('abbrev', 3)->nullable()->after('name');
            $table->string('slug', 180)->nullable()->after('name');
            $table->unsignedInteger('task_sequence')->default(0)->after('archived_at');
            $table->json('settings')->nullable()->after('description');

            $table->unique(['organization_id', 'abbrev']);
            $table->unique(['organization_id', 'slug']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropUnique(['organization_id', 'abbrev']);
            $table->dropUnique(['organization_id', 'slug']);

            $table->dropColumn(['logo', 'abbrev', 'slug', 'task_sequence', 'settings']);
        });
    }
};
