<?php

namespace Tests\Feature;

use App\Models\Board;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BoardTest extends TestCase
{
    use RefreshDatabase;

    public function test_web_routes_manage_boards(): void
    {
        $this->get('/')->assertOk()->assertExactJson([]);
        $this->get('/boards')->assertOk()->assertExactJson([]);

        $created = $this->postJson('/boards', [
            'name' => 'Planning',
            'canvas_data' => '{"objects":[]}',
        ])->assertCreated()->assertJsonPath('name', 'Planning');

        $boardId = $created->json('id');

        $this->getJson("/boards/{$boardId}")
            ->assertOk()
            ->assertJsonPath('canvas_data', '{"objects":[]}');

        $this->putJson("/boards/{$boardId}", [
            'name' => 'Planning updated',
            'canvas_data' => null,
        ])->assertOk()->assertJsonPath('name', 'Planning updated');

        $this->deleteJson("/boards/{$boardId}")->assertNoContent();

        $this->assertDatabaseMissing('boards', ['id' => $boardId]);
    }

    public function test_api_routes_return_json_and_manage_boards(): void
    {
        $created = $this->postJson('/api/boards', [
            'name' => 'Ideas',
        ])->assertCreated()
            ->assertHeader('content-type', 'application/json')
            ->assertJsonPath('name', 'Ideas');

        $boardId = $created->json('id');

        $this->getJson('/api/boards')
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonPath('0.id', $boardId);

        $this->getJson("/api/boards/{$boardId}")
            ->assertOk()
            ->assertJsonPath('id', $boardId);

        $this->putJson("/api/boards/{$boardId}", [
            'name' => 'Ideas',
            'canvas_data' => 'updated canvas',
        ])->assertOk()
            ->assertJsonPath('canvas_data', 'updated canvas');

        $this->deleteJson("/api/boards/{$boardId}")->assertNoContent();
        $this->getJson("/api/boards/{$boardId}")->assertNotFound();
    }

    public function test_board_names_must_be_unique(): void
    {
        Board::create(['name' => 'Existing board']);

        $this->postJson('/api/boards', [
            'name' => 'Existing board',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors('name');

        $this->assertDatabaseCount('boards', 1);
    }
}
