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
        Schema::create('books', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->string('title'); // Book title
            $table->text('description')->nullable(); // Book description
            $table->date('publish_date'); // Publish date
            $table->foreignId('author_id')->constrained('authors')->onDelete('cascade'); // Foreign key referencing authors
            $table->timestamps(); // Created_at and Updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
