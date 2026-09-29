<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('slug')->unique();
            $table->string('package_name')->unique();

            $table->string('platform')->default('android');

            $table->text('description')->nullable();

            $table->string('status')->default('active');

            $table->string('firebase_project_id')->nullable();

            $table->json('api_credentials_metadata')->nullable();

            $table->string('current_version')->nullable();
            $table->unsignedInteger('current_build_number')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
