<?php

namespace App\Http\Controllers;

use App\Models\Board;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BoardController extends Controller
{
    public function index(Request $request): JsonResponse|View
    {
        $boards = Board::query()->latest('updated_at')->get();

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json($boards);
        }

        return view('boards.index', compact('boards'));
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $board = Board::create($this->validatedData($request));

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json($board, 201);
        }

        return redirect()->route('boards.index')->with('success', 'Board created successfully.');
    }

    public function show(Board $board): View
    {
        return view('boards.show', compact('board'));
    }

    public function apiShow(Board $board): JsonResponse
    {
        return response()->json($board);
    }

    public function update(Request $request, Board $board): JsonResponse|RedirectResponse
    {
        $board->update($this->validatedData($request, $board));

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json($board->refresh());
        }

        return redirect()->route('boards.index')->with('success', 'Board renamed successfully.');
    }

    public function destroy(Request $request, Board $board): JsonResponse|RedirectResponse
    {
        $board->delete();

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json(null, 204);
        }

        return redirect()->route('boards.index')->with('success', 'Board deleted successfully.');
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
