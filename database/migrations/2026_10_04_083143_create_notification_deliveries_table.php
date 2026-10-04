<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_deliveries', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('notification_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('device_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('status', 30)
                ->index();

            $table->unsignedInteger('attempts')
                ->default(0);

            $table->text('error_message')
                ->nullable();

            $table->timestamp('queued_at')
                ->nullable();

            $table->timestamp('processing_at')
                ->nullable();

            $table->timestamp('sent_at')
                ->nullable();

            $table->timestamp('completed_at')
                ->nullable();

            $table->timestamps();

            $table->unique([
                'notification_id',
                'device_id',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_deliveries');
    }
};