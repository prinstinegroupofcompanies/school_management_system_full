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
            $table->id();
            $table->string('title');
            $table->string('isbn')->unique()->nullable();
            $table->string('author');
            $table->string('publisher')->nullable();
            $table->string('edition')->nullable();
            $table->integer('publication_year')->nullable();
            $table->text('description')->nullable();
            $table->text('summary')->nullable();
            $table->string('cover_image')->nullable();
            $table->string('file_path')->nullable(); // For digital books
            $table->string('file_type')->nullable(); // pdf, epub, etc.
            $table->integer('file_size')->nullable(); // in bytes
            $table->integer('pages')->nullable();
            $table->string('language')->default('English');
            $table->unsignedBigInteger('class_id')->nullable();
            $table->string('location')->nullable(); // Shelf number, room, etc.
            $table->integer('total_copies')->default(1);
            $table->integer('available_copies')->default(1);
            $table->integer('borrowed_copies')->default(0);
            $table->integer('reserved_copies')->default(0);
            $table->decimal('price', 10, 2)->nullable();
            $table->string('currency')->default('LRD');
            $table->enum('status', ['available', 'unavailable', 'maintenance', 'lost'])->default('available');
            $table->boolean('is_digital')->default(false);
            $table->boolean('is_active')->default(true);
            $table->text('tags')->nullable(); // JSON array of tags
            $table->integer('views_count')->default(0);
            $table->integer('downloads_count')->default(0);
            $table->decimal('rating', 3, 2)->default(0.00);
            $table->integer('rating_count')->default(0);
            $table->timestamps();
            
            $table->index(['title', 'author']);
            $table->index(['isbn', 'status']);
            $table->index(['category_id', 'subcategory_id']);
            $table->index(['status', 'is_active']);
            $table->index(['is_digital', 'available_copies']);
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