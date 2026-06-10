<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Infinite Canvas Whiteboard</title>
    <style>
        :root {
            color-scheme: light;
            --primary: #5048e5;
            --primary-dark: #4038ce;
            --primary-soft: #eeedff;
            --background: #f6f7fb;
            --surface: #ffffff;
            --text: #191b2a;
            --muted: #6f7485;
            --border: #e3e5ed;
            --danger: #d93838;
            --danger-soft: #fff5f5;
            --success: #167748;
            --success-soft: #eefaf3;
            --shadow: 0 14px 36px rgba(35, 37, 55, 0.08);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            background:
                radial-gradient(circle at 12% 0%, rgba(80, 72, 229, 0.1), transparent 28rem),
                var(--background);
            color: var(--text);
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        button, input {
            font: inherit;
        }

        button, .button, .create-shortcut {
            cursor: pointer;
        }

        .page {
            width: min(1160px, calc(100% - 32px));
            margin: 0 auto;
            padding: 58px 0 72px;
        }

        .hero {
            display: flex;
            align-items: center;
            gap: 18px;
            margin-bottom: 32px;
        }

        .hero-icon {
            display: grid;
            width: 58px;
            height: 58px;
            flex: 0 0 auto;
            grid-template-columns: repeat(2, 13px);
            place-content: center;
            gap: 5px;
            border-radius: 17px;
            background: var(--primary);
            box-shadow: 0 12px 24px rgba(80, 72, 229, 0.25);
        }

        .hero-icon span {
            width: 13px;
            height: 13px;
            border: 2px solid #ffffff;
            border-radius: 3px;
        }

        h1, h2, h3, p {
            margin-top: 0;
        }

        h1 {
            margin-bottom: 5px;
            font-size: clamp(2rem, 5vw, 3.15rem);
            letter-spacing: -0.052em;
            line-height: 1.05;
        }

        .subtitle {
            margin-bottom: 0;
            color: var(--muted);
            font-size: 1.02rem;
        }

        .panel {
            padding: 24px;
            border: 1px solid var(--border);
            border-radius: 18px;
            background: var(--surface);
            box-shadow: var(--shadow);
        }

        .panel h2 {
            margin-bottom: 16px;
            font-size: 1.1rem;
        }

        .create-form {
            display: flex;
            gap: 10px;
        }

        input {
            min-width: 0;
            flex: 1;
            padding: 11px 13px;
            border: 1px solid var(--border);
            border-radius: 9px;
            outline: none;
            background: #ffffff;
            color: var(--text);
            transition: border-color 140ms ease, box-shadow 140ms ease;
        }

        input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(80, 72, 229, 0.12);
        }

        .button, button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 10px 14px;
            border: 1px solid transparent;
            border-radius: 9px;
            background: var(--primary);
            color: #ffffff;
            font-weight: 750;
            text-decoration: none;
            transition: background-color 140ms ease, border-color 140ms ease, transform 140ms ease;
        }

        .button:hover, button:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
        }

        .create-button {
            min-width: 136px;
        }

        .alerts {
            margin-top: 14px;
        }

        .alert {
            margin: 0;
            padding: 11px 13px;
            border-radius: 9px;
            font-size: 0.88rem;
        }

        .alert + .alert {
            margin-top: 8px;
        }

        .alert-success {
            background: var(--success-soft);
            color: var(--success);
        }

        .alert-error {
            border: 1px solid #ffd6d6;
            background: var(--danger-soft);
            color: var(--danger);
        }

        .alert ul {
            margin: 0;
            padding-left: 20px;
        }

        .section-heading {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin: 38px 0 16px;
        }

        .section-heading h2 {
            margin-bottom: 0;
            font-size: 1.3rem;
        }

        .count-pill {
            padding: 6px 11px;
            border: 1px solid #dcd9ff;
            border-radius: 999px;
            background: var(--primary-soft);
            color: var(--primary);
            font-size: 0.78rem;
            font-weight: 800;
        }

        .board-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            gap: 16px;
        }

        .board-card {
            display: flex;
            min-height: 292px;
            flex-direction: column;
            padding: 18px;
            border: 1px solid var(--border);
            border-radius: 16px;
            background: var(--surface);
            box-shadow: 0 8px 24px rgba(35, 37, 55, 0.06);
            transition: border-color 140ms ease, box-shadow 140ms ease, transform 140ms ease;
        }

        .board-card:hover {
            border-color: #cbc8fa;
            box-shadow: var(--shadow);
            transform: translateY(-2px);
        }

        .board-icon {
            display: grid;
            width: 42px;
            height: 42px;
            margin-bottom: 16px;
            grid-template-columns: repeat(2, 8px);
            place-content: center;
            gap: 4px;
            border-radius: 11px;
            background: var(--primary);
        }

        .board-icon span {
            width: 8px;
            height: 8px;
            border: 1px solid #ffffff;
            border-radius: 2px;
        }

        .board-name {
            margin-bottom: 5px;
            overflow-wrap: anywhere;
            font-size: 1.08rem;
        }

        .updated {
            margin-bottom: 18px;
            color: var(--muted);
            font-size: 0.78rem;
        }

        .open-button {
            width: 100%;
            margin-top: auto;
            margin-bottom: 12px;
        }

        .card-actions {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto auto;
            gap: 6px;
        }

        .rename-form {
            display: contents;
        }

        .rename-input {
            width: 100%;
            padding: 8px 9px;
            font-size: 0.76rem;
        }

        .small-button {
            padding: 8px 9px;
            font-size: 0.72rem;
        }

        .delete-form {
            display: flex;
        }

        .delete-button {
            border-color: #efb4b4;
            background: #ffffff;
            color: var(--danger);
        }

        .delete-button:hover {
            border-color: var(--danger);
            background: var(--danger-soft);
        }

        .create-shortcut {
            display: flex;
            min-height: 292px;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 24px;
            border: 2px dashed #cbc9dd;
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.45);
            color: var(--muted);
            text-align: center;
            transition: border-color 140ms ease, background-color 140ms ease, color 140ms ease;
        }

        .create-shortcut:hover, .create-shortcut:focus-visible {
            border-color: var(--primary);
            outline: none;
            background: var(--primary-soft);
            color: var(--primary);
        }

        .shortcut-icon {
            display: grid;
            width: 44px;
            height: 44px;
            margin-bottom: 13px;
            place-items: center;
            border-radius: 50%;
            background: var(--primary-soft);
            color: var(--primary);
            font-size: 1.5rem;
            font-weight: 500;
        }

        .create-shortcut strong {
            margin-bottom: 4px;
            color: var(--text);
        }

        .create-shortcut span:last-child {
            font-size: 0.8rem;
        }

        @media (max-width: 640px) {
            .page {
                padding-top: 34px;
            }

            .hero {
                align-items: flex-start;
            }

            .hero-icon {
                width: 48px;
                height: 48px;
                border-radius: 14px;
            }

            .panel {
                padding: 18px;
            }

            .create-form {
                flex-direction: column;
            }

            .create-button {
                width: 100%;
            }

            .card-actions {
                grid-template-columns: 1fr 1fr;
            }

            .rename-input {
                grid-column: 1 / -1;
            }

            .delete-form, .delete-button {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <main class="page">
        <header class="hero">
            <div class="hero-icon" aria-hidden="true">
                <span></span><span></span><span></span><span></span>
            </div>
            <div>
                <h1>Infinite Canvas Whiteboard</h1>
                <p class="subtitle">Create, manage, and open your saved whiteboards.</p>
            </div>
        </header>

        <section class="panel" aria-labelledby="create-board-heading">
            <h2 id="create-board-heading">Create a new board</h2>
            <form class="create-form" action="{{ route('boards.store') }}" method="POST">
                @csrf
                <input
                    id="create-board-input"
                    type="text"
                    name="name"
                    value="{{ old('name') }}"
                    placeholder="Enter a unique board name"
                    aria-label="Board name"
                    required
                    maxlength="255"
                    autofocus
                >
                <button class="create-button" type="submit">Create board</button>
            </form>

            <div class="alerts">
                @if ($errors->any())
                    <div class="alert alert-error" role="alert">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (session('success'))
                    <div class="alert alert-success" role="status">{{ session('success') }}</div>
                @endif
            </div>
        </section>

        <div class="section-heading">
            <h2>Saved boards</h2>
            <span class="count-pill">{{ $boards->count() }} {{ Str::plural('board', $boards->count()) }}</span>
        </div>

        <section class="board-grid" aria-label="Saved boards">
            @foreach ($boards as $board)
                <article class="board-card">
                    <div class="board-icon" aria-hidden="true">
                        <span></span><span></span><span></span><span></span>
                    </div>
                    <h3 class="board-name">{{ $board->name }}</h3>
                    <p class="updated">Updated {{ $board->updated_at->format('M j, Y \a\t g:i A') }}</p>

                    <a class="button open-button" href="{{ route('boards.show', $board) }}">Open board</a>

                    <div class="card-actions">
                        <form class="rename-form" action="{{ route('boards.update', $board) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <input
                                class="rename-input"
                                type="text"
                                name="name"
                                value="{{ $board->name }}"
                                aria-label="Rename {{ $board->name }}"
                                required
                                maxlength="255"
                            >
                            <button class="small-button" type="submit">Rename</button>
                        </form>

                        <form
                            class="delete-form"
                            action="{{ route('boards.destroy', $board) }}"
                            method="POST"
                            onsubmit="return confirm('Delete this board permanently?')"
                        >
                            @csrf
                            @method('DELETE')
                            <button class="small-button delete-button" type="submit">Delete</button>
                        </form>
                    </div>
                </article>
            @endforeach

            <button
                class="create-shortcut"
                type="button"
                onclick="document.getElementById('create-board-input').focus(); window.scrollTo({ top: 0, behavior: 'smooth' });"
            >
                <span class="shortcut-icon" aria-hidden="true">+</span>
                <strong>Create another board</strong>
                <span>Start with a fresh infinite canvas.</span>
            </button>
        </section>
    </main>
</body>
</html>
