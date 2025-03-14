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
        Schema::create('sources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('notebook_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->enum('type', ['text', 'file', 'website', 'youtube']); // Separate website and youtube types
            $table->longText('data'); // URL for website/youtube, text content for text type
            $table->string('file_path')->nullable(); // For file uploads
            $table->string('file_type')->nullable(); // Store file mime type
            $table->boolean('has_extracted_text')->default(false); // Flag for text extraction status
            $table->boolean('is_active')->default(true); // For toggling source inclusion in AI context
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sources');
    }
};
