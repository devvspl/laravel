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
        Schema::table('menus', function (Blueprint $table) {
            $table->string('permission_name')->nullable()->after('parent_id');

            $table->unsignedBigInteger('permission_id')->nullable()->after('permission_name');
            $table->foreign('permission_id')
                  ->references('id')
                  ->on('permissions')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::table('menus', function (Blueprint $table) {
            $table->dropForeign(['permission_id']);

            $table->dropColumn('permission_id');
            $table->dropColumn('permission_name');
        });
    }
};
