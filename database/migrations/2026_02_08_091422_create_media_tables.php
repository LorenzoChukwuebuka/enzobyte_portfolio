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

        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->string('model_type');                          // The model class name (e.g., App\Models\Project)
            $table->unsignedBigInteger('model_id');                // The model ID
            $table->string('collection_name')->default('default'); // e.g., 'thumbnail', 'gallery', 'featured_image'
            $table->string('file_name');                           // Original filename
            $table->string('disk')->default('public');             // Storage disk
            $table->string('path');                                // File path on disk
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size')->nullable(); // File size in bytes
            $table->json('metadata')->nullable();           // Additional data (dimensions, alt text, etc.)
            $table->integer('order')->default(0);           // For sorting multiple images
            $table->timestamps();

            $table->index(['model_type', 'model_id']);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media_tables');
    }
};