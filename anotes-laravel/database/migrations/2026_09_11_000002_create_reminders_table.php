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
        Schema::create('reminders', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('title', 255);
            $table->string('description', 500)->nullable();
            $table->date('remind_date');
            $table->time('remind_time');
            $table->enum('repeat_option', ['none', 'daily', 'weekly', 'monthly', 'custom'])->nullable()->default('none');
            $table->integer('custom_days')->nullable();
            $table->boolean('is_done')->nullable()->default(false);
            $table->timestamp('created_at')->useCurrent();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reminders');
    }
};
