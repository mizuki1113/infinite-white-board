<?php

namespace App\Http\Controllers;

use App\Models\Board;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BoardController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Board::query()->latest()->get());
    }

    public function store(Request $request): JsonResponse
    {
        $board = Board::create($this->validatedData($request));

        return response()->json($board, 201);
    }

    public function show(Board $board): JsonResponse
    {
        return response()->json($board);
    }

    public function update(Request $request, Board $board): JsonResponse
    {
        $board->update($this->validatedData($request, $board));

        return response()->json($board->refresh());
    }

    public function destroy(Board $board): JsonResponse
    {
        $board->delete();

        return response()->json(null, 204);
    }

    /**
     * @return array{name: string, canvas_data?: string|null}
     */
    private function validatedData(Request $request, ?Board $board = null): array
    {
        return $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('boards', 'name')->ignore($board),
            ],
            'canvas_data' => ['nullable', 'string'],
        ]);
    }
}
