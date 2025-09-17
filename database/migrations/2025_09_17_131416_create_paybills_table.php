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
        Schema::create('paybills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('paybill_number');
            $table->string('consumer_key')->nullable();
            $table->string('consumer_secret')->nullable();
            $table->string('passkey')->nullable();
            $table->integer('daily_limit')->default(1000);
            $table->integer('current_count')->default(0);
            $table->timestamp('reset_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paybills');
    }
};
