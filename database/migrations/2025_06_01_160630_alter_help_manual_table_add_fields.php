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
        Schema::table('help_manual', function (Blueprint $table) {
            // Add attachment_path column
            $table->string('attachment_path')->nullable()->after('video_language')->comment('Path to optional attachment or screenshot');

            // Add role_id column as a foreign key (nullable)
            $table->foreignId('role_id')
                ->nullable()
                ->after('attachment_path')
                ->constrained('roles')
                ->onDelete('set null')
                ->onUpdate('cascade')
                ->comment('');

            // Add created_by column as a foreign key (nullable)
            $table->foreignId('created_by')
                ->nullable()
                ->after('status')
                ->constrained('users')
                ->onDelete('set null')
                ->onUpdate('cascade')
                ->comment('');

            // Modify order column to have a default value
            $table->integer('order')->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('help_manual', function (Blueprint $table) {
            // Drop foreign key and column for created_by
            $table->dropForeign(['created_by']);
            $table->dropColumn('created_by');

            // Drop foreign key and column for role_id
            $table->dropForeign(['role_id']);
            $table->dropColumn('role_id');

            // Drop attachment_path column
            $table->dropColumn('attachment_path');

            // Revert order column to NOT NULL without a default (original state)
            $table->integer('order')->nullable(false)->default(null)->change();
        });
    }
};