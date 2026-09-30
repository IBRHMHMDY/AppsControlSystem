<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('devices', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('application_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('user_identifier')->nullable();

            $table->text('fcm_token');

            $table->string('platform', 20);

            $table->string('device_identifier');

            $table->string('app_version')->nullable();

            $table->string('os_version')->nullable();

            $table->string('locale', 10)->nullable();

            $table->string('timezone', 64)->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamp('last_seen_at')->nullable();

            $table->timestamps();

            $table->unique([
                'application_id',
                'device_identifier',
            ]);

            $table->index([
                'application_id',
                'is_active',
            ]);

            $table->index('user_identifier');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};