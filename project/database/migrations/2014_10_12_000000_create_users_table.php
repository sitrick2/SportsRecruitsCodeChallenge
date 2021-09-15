<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->enum('user_type', ['player', 'coach'])->nullable()->default(null);
            $table->string('first_name', 255)->nullable()->default(null);
            $table->string('last_name', 255)->nullable()->default(null);
            $table->unsignedTinyInteger('ranking')->nullable()->default('3');
            $table->boolean('can_play_goalie')->nullable()->default('0');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
}
