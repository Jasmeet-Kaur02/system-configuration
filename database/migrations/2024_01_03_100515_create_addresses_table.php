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
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->string("houseNumber");
            $table->string('area');
            $table->string("landmark")->nullable();
            $table->bigInteger("postalCode");
            $table->string('city');
            $table->string("state");
            $table->string("country");
            $table->unsignedBigInteger('userId');
            $table->foreign("userId")->references("id")->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
