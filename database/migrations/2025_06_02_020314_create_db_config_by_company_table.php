<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDbConfigByCompanyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('db_config_by_company', function (Blueprint $table) {
            $table->id(); // Auto-incrementing primary key 'id'
            $table->integer('company_id'); // company_id as a regular integer (no foreign key)
            $table->string('db_name', 50); // VARCHAR(50) for db_name
            $table->string('db_connection', 50); // VARCHAR(50) for db_connection
            $table->string('db_host', 255); // VARCHAR(255) for db_host
            $table->integer('db_port'); // INT for db_port
            $table->string('db_database', 255); // VARCHAR(255) for db_database
            $table->string('db_username', 255); // VARCHAR(255) for db_username
            $table->string('db_password', 255)->default(''); // VARCHAR(255) for db_password with default empty string
            $table->string('created_by', 255)->nullable(); // VARCHAR(255) for created_by, nullable
            $table->string('updated_by', 255)->nullable(); // VARCHAR(255) for updated_by, nullable
            $table->timestamp('created_at')->nullable(); // Timestamp for created_at, nullable
            $table->timestamp('updated_at')->nullable(); // Timestamp for updated_at, nullable
            $table->string('status', 50)->default('active'); // VARCHAR(50) for status, default 'active'
            $table->unique(['company_id', 'db_name']); // Composite unique key on company_id and db_name
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('db_config_by_company');
    }
}