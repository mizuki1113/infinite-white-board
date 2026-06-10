<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Infinite Canvas Whiteboard</title>
    <style>
        :root {
            color-scheme: light;
            --background: #f4f7fb;
            --surface: #ffffff;
            --text: #172033;
            --muted: #687386;
            --border: #dfe5ee;
            --primary: #4f46e5;
            --primary-dark: #4338ca;
            --danger: #dc2626;
            --danger-light: #fef2f2;
            --success: #15803d;
            --success-light: #f0fdf4;
            --shadow: 0 14px 35px rgba(23, 32, 51, 0.08);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            background:
                radial-gradient(circle at top left, rgba(79, 70, 229, 0.12), transparent 30rem),
                var(--background);
            color: var(--text);
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        .container {
            width: min(1120px, calc(100% - 32px));
            margin: 0 auto;
            padding: 64px 0;
        }

        .header {
            margin-bottom: 32px;
        }

        h1 {
            margin: 0 0 8px;
            font-size: clamp(2rem, 5vw, 3.25rem);
            letter-spacing: -0.05em;
        }

        .subtitle, .updated, .empty {
            color: var(--muted);
        }

        .panel {
            margin-bottom: 28px;
            padding: 24px;
            border: 1px solid var(--border);
            border-radius: 18px;
            background: var(--surface);
            box-shadow: var(--shadow);
        }

        .create-form, .rename-form {
            display: flex;
            gap: 10px;
        }

        input {
            min-width: 0;
            flex: 1;
            padding: 11px 13px;
            border: 1px solid var(--border);
            border-radius: 10px;
            color: var(--text);
            background: #fff;
            font: inherit;
        }

        input:focus {
            border-color: var(--primary);
            outline: 3px solid rgba(79, 70, 229, 0.14);
        }

        button, .button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 11px 16px;
            border: 0;
            border-radius: 10px;
            background: var(--primary);
            color: #fff;
            font: inherit;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
            transition: background 150ms ease, transform 150ms ease;
        }

        button:hover, .button:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
        }

        .danger {
            background: var(--danger-light);
            color: var(--danger);
        }

        .danger:hover {
            background: #fee2e2;
        }

        .alert {
            margin-bottom: 20px;
            padding: 14px 16px;
            border-radius: 12px;
        }

        .alert-success {
            background: var(--success-light);
            color: var(--success);
        }

        .alert-error {
            background: var(--danger-light);
            color: var(--danger);
        }

        .alert ul {
            margin: 0;
            padding-left: 20px;
        }

        .section-heading {
            display: flex;
            align-items: baseline;
            justify-content: space-between;
            gap: 16px;
            margin: 40px 0 18px;
        }

        h2, h3, p {
            margin-top: 0;
        }

        .count {
            color: var(--muted);
            font-size: 0.9rem;
        }

        .board-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 18px;
        }

        .board-card {
            display: flex;
            min-height: 220px;
            flex-direction: column;
            justify-content: space-between;
            padding: 20px;
            border: 1px solid var(--border);
            border-radius: 16px;
            background: var(--surface);
            box-shadow: var(--shadow);
        }

        .board-card h3 {
            margin-bottom: 6px;
            overflow-wrap: anywhere;
            font-size: 1.2rem;
        }

        .updated {
            margin-bottom: 24px;
            font-size: 0.875rem;
        }

        .actions {
            display: grid;
            gap: 10px;
        }

        .secondary-actions {
            display: flex;
            gap: 10px;
        }

        .secondary-actions form {
            display: flex;
        }

        .empty {
            padding: 48px 24px;
            border: 1px dashed #c8d1df;
            border-radius: 16px;
            text-align: center;
        }

        @media (max-width: 560px) {
            .container {
                padding: 36px 0;
            }

            .create-form, .rename-form {
                flex-direction: column;
            }

            .secondary-actions, .secondary-actions form, .secondary-actions button {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <main class="container">
        <header class="header">
            <h1>Infinite Canvas Whiteboard</h1>
            <p class="subtitle">Create a board and keep your ideas in one place.</p>
        </header>

        @if (session('success'))
            <div class="alert alert-success" role="status">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-error" role="alert">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <section class="panel" aria-labelledby="create-board-heading">
            <h2 id="create-board-heading">Create a new board</h2>
            <form class="create-form" action="{{ route('boards.store') }}" method="POST">
                @csrf
                <input
                    type="text"
                    name="name"
                    value="{{ old('name') }}"
                    placeholder="Enter a unique board name"
                    aria-label="Board name"
                    required
                    maxlength="255"
                    autofocus
                >
                <button type="submit">Create board</button>
            </form>
        </section>

        <div class="section-heading">
            <h2>Saved boards</h2>
            <span class="count">{{ $boards->count() }} {{ Str::plural('board', $boards->count()) }}</span>
        </div>

        @if ($boards->isEmpty())
            <div class="empty">No boards yet. Create your first board above.</div>
        @else
            <section class="board-grid" aria-label="Saved boards">
                @foreach ($boards as $board)
                    <article class="board-card">
                        <div>
                            <h3>{{ $board->name }}</h3>
                            <p class="updated">Updated {{ $board->updated_at->format('M j, Y \a\t g:i A') }}</p>
                        </div>

                        <div class="actions">
                            <a class="button" href="{{ route('boards.show', $board) }}">Open board</a>

                            <form class="rename-form" action="{{ route('boards.update', $board) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <input
                                    type="text"
                                    name="name"
                                    value="{{ $board->name }}"
                                    aria-label="Rename {{ $board->name }}"
                                    required
                                    maxlength="255"
                                >
                                <button type="submit">Rename</button>
                            </form>

                            <div class="secondary-actions">
                                <form
                                    action="{{ route('boards.destroy', $board) }}"
                                    method="POST"
                                    onsubmit="return confirm('Delete this board permanently?')"
                                >
                                    @csrf
                                    @method('DELETE')
                                    <button class="danger" type="submit">Delete</button>
                                </form>
                            </div>
                        </div>
                    </article>
                @endforeach
            </section>
        @endif
    </main>
</body>
</html>
