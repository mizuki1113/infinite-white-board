<?php

namespace Tests\Feature;

use App\Models\Board;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BoardTest extends TestCase
{
    use RefreshDatabase;

    public function test_board_index_displays_saved_boards_and_management_forms(): void
    {
        $board = Board::create(['name' => 'Project map']);

        $this->get('/boards')
            ->assertOk()
            ->assertViewIs('boards.index')
            ->assertViewHas('boards')
            ->assertSee('Infinite Canvas Whiteboard')
            ->assertSee('Project map')
            ->assertSee(route('boards.store'), false)
            ->assertSee(route('boards.show', $board), false)
            ->assertSee(route('boards.update', $board), false)
            ->assertSee(route('boards.destroy', $board), false);
    }

    public function test_web_routes_manage_boards(): void
    {
        $this->get('/')->assertOk()->assertViewIs('boards.index');
        $this->getJson('/boards')->assertOk()->assertExactJson([]);

        $created = $this->postJson('/boards', [
            'name' => 'Planning',
            'canvas_data' => '{"objects":[]}',
        ])->assertCreated()->assertJsonPath('name', 'Planning');

        $boardId = $created->json('id');

        $this->get("/boards/{$boardId}")
            ->assertOk()
            ->assertViewIs('boards.show')
            ->assertViewHas('board')
            ->assertSee('Konva')
            ->assertSee('Planning')
            ->assertSee('id="canvas-container"', false)
            ->assertSee('id="custom-color"', false)
            ->assertSee('← Back')
            ->assertSee('body.light-mode #canvas-container')
            ->assertSee('inline-text-editor')
            ->assertDontSee('prompt(')
            ->assertSee('const resizeStage')
            ->assertSee('stage.container().getBoundingClientRect()')
            ->assertSee('transform.invert()')
            ->assertSee('data-tool="eraser"', false)
            ->assertSee('data-tool="fill"', false)
            ->assertSee('id="fill-color"', false)
            ->assertSee('id="no-fill"', false)
            ->assertSee('data-eraser-size="52"', false)
            ->assertSee('destination-out')
            ->assertSee('data-brush="highlighter"', false)
            ->assertSee('whiteboard-theme')
            ->assertSee(str_replace('/', '\/', route('api.boards.update', $boardId)), false);

        $this->putJson("/boards/{$boardId}", [
            'name' => 'Planning updated',
            'canvas_data' => null,
        ])->assertOk()->assertJsonPath('name', 'Planning updated');

        $this->deleteJson("/boards/{$boardId}")->assertNoContent();

        $this->assertDatabaseMissing('boards', ['id' => $boardId]);
    }

    public function test_web_forms_redirect_and_display_validation_errors(): void
    {
        $this->post('/boards', ['name' => 'Planning'])
            ->assertRedirect(route('boards.index'))
            ->assertSessionHas('success');

        $board = Board::where('name', 'Planning')->firstOrFail();

        $this->put("/boards/{$board->id}", ['name' => 'Planning updated'])
            ->assertRedirect(route('boards.index'))
            ->assertSessionHas('success');

        $this->post('/boards', ['name' => 'Planning updated'])
            ->assertRedirect()
            ->assertSessionHasErrors('name');

        $this->followingRedirects()
            ->post('/boards', ['name' => 'Planning updated'])
            ->assertOk()
            ->assertSee('The name has already been taken.');

        $this->delete("/boards/{$board->id}")
            ->assertRedirect(route('boards.index'))
            ->assertSessionHas('success');
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
            ->assertHeader('content-type', 'application/json')
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
