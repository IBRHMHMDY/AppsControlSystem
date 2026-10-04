<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_fcm_deliveries', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('notification_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('target_type', 30);

            $table->text('target_value');

            $table->char('target_hash', 64);

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
                'target_type',
                'target_hash',
            ], 'notification_fcm_deliveries_target_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_fcm_deliveries');
    }
};
