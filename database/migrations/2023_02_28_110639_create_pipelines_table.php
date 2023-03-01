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
        Schema::create('pipelines', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_bin';

            $table->id();
            $table->string('name');
            $table->unsignedInteger('pipeline_id')->unique();
            $table->unsignedInteger('sort')->default('1');
            $table->unsignedInteger('account_id')->index();
            $table->boolean('is_main')->unsigned()->default('1');
            $table->boolean('is_unsorted_on')->unsigned()->default('1');
            $table->boolean('is_archive')->unsigned()->default('0');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pipelines');
    }
};
