<?php

use App\Models\Book;
use App\Models\Author;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);


it('can retrieve all authors', function () {
    // Arrange: Create some authors
    Author::factory()->count(3)->create();

    // Act: Make a request to the index method
    $response = $this->getJson('/api/authors');

    // Assert: Check the response status and data structure
    $response->assertStatus(200)
        ->assertJsonStructure([
            'status',
            'message',
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'bio',
                    'birth_date',
                    'books' => [
                        '*' => [
                            'id',
                            'title',
                            // other book attributes
                        ]
                    ]
                ]
            ]
        ]);
});

it('can create a new author', function () {
    // Arrange: Prepare author data
    $authorData = [
        'name' => 'John Doe',
        'bio' => 'An author.',
        'birth_date' => '1990-01-01',
    ];

    // Act: Make a request to store the author
    $response = $this->postJson('/api/authors', $authorData);

    // Assert: Check the response status and database for the new author
    $response->assertStatus(201)
        ->assertJson([
            'status' => 201,
            'message' => 'Author created successfully',
            'data' => [
                'name' => 'John Doe',
            ]
        ]);

    $this->assertDatabaseHas('authors', $authorData);
});

it('can show a specific author', function () {
    // Arrange: Create an author
    $author = Author::factory()->create();

    // Act: Make a request to show the author
    $response = $this->getJson("/api/authors/{$author->id}");

    // Assert: Check the response
    $response->assertStatus(200)
        ->assertJsonFragment([
            'name' => $author->name,
        ]);
});

it('can update an existing author', function () {
    // Arrange: Create an author
    $author = Author::factory()->create();
    $updatedData = [
        'name' => 'Jane Doe',
        'bio' => 'Updated author bio.',
        'birth_date' => '1992-02-02',
    ];

    // Act: Make a request to update the author
    $response = $this->putJson("/api/authors/{$author->id}", $updatedData);

    // Assert: Check the response and database
    $response->assertStatus(200)
        ->assertJsonFragment([
            'name' => 'Jane Doe',
        ]);

    $this->assertDatabaseHas('authors', $updatedData);
});

it('can delete an author', function () {
    // Arrange: Create an author
    $author = Author::factory()->create();

    // Act: Make a request to delete the author
    $response = $this->deleteJson("/api/authors/{$author->id}");

    // Assert: Check the response
    $response->assertStatus(200)
        ->assertJson([
            'status' => 200,
            'message' => 'Author deleted successfully',
        ]);

    // Check that the author was deleted
    $this->assertDatabaseMissing('authors', ['id' => $author->id]);
});

it('can get books by a specific author', function () {
    // Arrange: Create an author with books
    $author = Author::factory()->create();
    $books = Book::factory()->count(2)->create(['author_id' => $author->id]);

    // Act: Make a request to get books by author
    $response = $this->getJson("/api/authors/{$author->id}/books");

    // Assert: Check the response
    $response->assertStatus(200)
        ->assertJsonStructure([
            'status',
            'message',
            'data' => [
                '*' => [
                    'id',
                    'title',
                    // other book attributes
                ]
            ]
        ]);
});

it('returns a 404 when authors not found', function () {
    $response = $this->getJson('/api/authors/999');

    $response->assertStatus(404);
});

