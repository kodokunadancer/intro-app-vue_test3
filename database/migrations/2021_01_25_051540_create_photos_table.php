<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePhotosTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('photos', function (Blueprint $table): void {
            $table->string('random_id')->primary();
            $table->unsignedInteger('profile_id')->nullable();
            $table->unsignedInteger('group_id')->nullable();
            $table->string('filename');
            $table->timestamps();
            $table->foreign('profile_id')->references('id')->on('profiles');
            $table->foreign('group_id')->references('id')->on('groups');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('photos');
    }
}
