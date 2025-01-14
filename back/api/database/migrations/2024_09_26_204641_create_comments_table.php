<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->ulid('user_id');
            $table->ulid('photo_id');
            $table->text('content');
            $table->timestamps();
            // $table->softDeletes();

            $table->foreign('photo_id')->references('id')->on('photos')->OnDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->OnDelete('cascade');
            $table->unique(['photo_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->dropForeign(['photo_id']);
            $table->dropForeign(['user_id']);
        });
        Schema::dropIfExists('comments');
    }
};
