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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();

            $table->foreignId('application_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('title');

            $table->text('body');

            $table->string('image')->nullable();

            $table->json('data_payload')->nullable();

            $table->string('target_type');

            $table->text('target_value')->nullable();

            $table->string('status');

            $table->timestamp('scheduled_at')->nullable();

            $table->timestamp('sent_at')->nullable();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index([
                'application_id',
                'status',
            ]);

            $table->index([
                'application_id',
                'scheduled_at',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
