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
        Schema::create('photos', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->ulid('user_id');
            $table->unsignedBigInteger('category_id')->nullable();
            $table->string('filename');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('category_id')->references('id')->on('categories')->OnDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->OnDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('photos', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropForeign(['user_id']);
        });
        Schema::dropIfExists('photos');
    }
};
