<?php

use App\Models\Book;
use App\Models\Author;

it('can create a book', function () {
    $author = Author::factory()->create();

    $response = $this->postJson('/api/books', [
        'title' => 'Harry Potter and the Philosopher\'s Stone',
        'description' => 'A young wizard\'s journey begins.',
        'publish_date' => '1997-06-26',
        'author_id' => $author->id,
    ]);

    $response->assertStatus(201)
        ->assertJson([
            'status' => 201,
            'message' => 'Book created successfully',
        ]);

    $this->assertDatabaseHas('books', [
        'title' => 'Harry Potter and the Philosopher\'s Stone',
    ]);
});

it('can retrieve books', function () {
    Book::factory()->count(3)->create();

    $response = $this->getJson('/api/books');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'status',
            'message',
            'data' => [
                '*' => [
                    'id',
                    'title',
                    'description',
                    'publish_date',
                    'author_id',
                    'author' => [ // Ensure that author data is included
                        'id',
                        'name', // Adjust as per your Author model fields
                    ],
                ],
            ],
        ]);
});

it('can retrieve a specific book', function () {
    $author = Author::factory()->create();
    $book = Book::factory()->create(['author_id' => $author->id]);

    $response = $this->getJson("/api/books/{$book->id}");

    $response->assertStatus(200)
        ->assertJson([
            'status' => 200,
            'message' => 'Book retrieved successfully',
            'data' => [
                'id' => $book->id,
                'title' => $book->title,
                'description' => $book->description,
                'publish_date' => $book->publish_date,
                'author_id' => $author->id,
                'author' => [
                    'id' => $author->id,
                    'name' => $author->name, // Adjust as per your Author model fields
                ],
            ],
        ]);
});

it('can update a book', function () {
    $author = Author::factory()->create();
    $book = Book::factory()->create(['author_id' => $author->id]);

    $response = $this->putJson("/api/books/{$book->id}", [
        'title' => 'Updated Title',
        'description' => 'Updated description.',
        'publish_date' => '1997-06-26',
        'author_id' => $author->id,
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'status' => 200,
            'message' => 'Book updated successfully',
        ]);

    $this->assertDatabaseHas('books', [
        'id' => $book->id,
        'title' => 'Updated Title',
    ]);
});

it('can delete a book', function () {
    $book = Book::factory()->create();

    $response = $this->deleteJson("/api/books/{$book->id}");

    $response->assertStatus(200);
    $this->assertDatabaseMissing('books', [
        'id' => $book->id,
    ]);
});

// Edge cases
it('returns a 404 when book not found', function () {
    $response = $this->getJson('/api/books/999');

    $response->assertStatus(404);
});

it('returns a validation error when creating a book with invalid data', function () {
    $response = $this->postJson('/api/books', [
        'title' => '', // Title is required
        'description' => 'A young wizard\'s journey begins.',
        'publish_date' => 'invalid-date', // Invalid date
        'author_id' => 999, // Non-existing author
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['title', 'publish_date', 'author_id']);
});
