<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommentsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('comments', function (Blueprint $table): void {
            $table->id();
            $table->unsignedInteger('active_profile_id');
            $table->unsignedInteger('passive_profile_id');
            $table->text('content');
            $table->timestamps();
            $table->foreign('active_profile_id')->references('id')->on('profiles');
            $table->foreign('passive_profile_id')->references('id')->on('profiles');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
}
