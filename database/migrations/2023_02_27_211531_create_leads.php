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
        Schema::create('leads', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_bin';

            $table->id();
            // $table->increments('id');
            $table->string('name');
            $table->unsignedInteger('lead_id')->unique();
            $table->unsignedInteger('group_id')->default('0')->index();
            $table->unsignedInteger('account_id')->index();
            $table->unsignedInteger('pipeline_id')->nullable()->index();
            $table->unsignedInteger('status_id')->nullable()->index();
            $table->unsignedInteger('company_id')->nullable()->index();
            $table->integer('price')->default('0');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
