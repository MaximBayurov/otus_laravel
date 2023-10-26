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
        Schema::create('construction_language', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('language_id')->unsigned()->index();
            $table->foreign('language_id')->references('id')->on('languages')->onDelete('cascade');

            $table->bigInteger('construction_id')->unsigned()->index();
            $table->foreign('construction_id')->references('id')->on('constructions')->onDelete('cascade');

            $table->text('code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('construction_language');
    }
};
