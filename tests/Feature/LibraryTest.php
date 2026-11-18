<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Member;
use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LibraryTest extends TestCase
{
    use RefreshDatabase;

    public function test_borrow_get_and_return_book_flow()
    {
        $member = Member::create(['name' => 'Budi Santoso']);
        $book = Book::create([
            'title' => 'Laskar Pelangi',
            'author' => 'Andrea Hirata',
            'is_available' => true,
        ]);

        // 1. POST /api/members/{id}/borrow-book
        $borrowResponse = $this->postJson("/api/members/{$member->id}/borrow-book", [
            'book_id' => $book->id,
        ]);
        $borrowResponse->assertStatus(200);
        $this->assertDatabaseHas('books', [
            'id' => $book->id,
            'member_id' => $member->id,
            'is_available' => false,
        ]);

        // Coba pinjam lagi buku yang sama -> harus conflict (409)
        $conflictResponse = $this->postJson("/api/members/{$member->id}/borrow-book", [
            'book_id' => $book->id,
        ]);
        $conflictResponse->assertStatus(409);

        // 2. GET /api/members/{id}
        $getResponse = $this->getJson("/api/members/{$member->id}");
        $getResponse->assertStatus(200)
            ->assertJsonPath('data.id', $member->id)
            ->assertJsonPath('data.name', 'Budi Santoso')
            ->assertJsonPath('data.books.0.id', $book->id);

        // 3. PATCH /api/members/{id}/return-book/{book_id}
        $returnResponse = $this->patchJson("/api/members/{$member->id}/return-book/{$book->id}");
        $returnResponse->assertStatus(200);
        $this->assertDatabaseHas('books', [
            'id' => $book->id,
            'member_id' => null,
            'is_available' => true,
        ]);
    }
}
