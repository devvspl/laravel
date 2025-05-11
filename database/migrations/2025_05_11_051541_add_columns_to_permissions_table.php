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
        Schema::table('permissions', function (Blueprint $table) {
            $table->string('permission_key')->after('name');
            $table->boolean('status')->default(1)->after('permission_key');
            $table->unsignedBigInteger('created_by')->nullable()->after('created_at');
            $table->unsignedBigInteger('updated_by')->nullable()->after('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->dropColumn(['permission_key', 'group_id', 'status', 'created_by', 'updated_by']);
        });
    }
};
