<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $board->name }} - Infinite Canvas Whiteboard</title>
    <script>
        document.documentElement.style.background = localStorage.getItem('whiteboard-theme') === 'light-mode' ? '#f5f5f5' : '#111111';
    </script>
    <script src="https://unpkg.com/konva@9/konva.min.js"></script>
    <style>
        :root {
            color-scheme: dark;
            --bg-main: #1c1c1c;
            --bg-sidebar: #1a1a1a;
            --bg-navbar: #1e1e1e;
            --text-primary: #f5f5f5;
            --text-muted: #9b9b9b;
            --border-color: #333333;
            --btn-bg: #2a2a2a;
            --btn-text: #ffffff;
            --tool-active-bg: #293747;
            --swatch-ring: #ffffff;
        }

        body.light-mode {
            color-scheme: light;
            --bg-main: #fafafa;
            --bg-sidebar: #ffffff;
            --bg-navbar: #ffffff;
            --text-primary: #1c1c1c;
            --text-muted: #777777;
            --border-color: #dedede;
            --btn-bg: #ffffff;
            --btn-text: #242424;
            --tool-active-bg: #e8f2fc;
            --swatch-ring: #222222;
        }

        * {
            box-sizing: border-box;
        }

        html, body {
            width: 100%;
            height: 100%;
            margin: 0;
            overflow: hidden;
        }

        body {
            background: var(--bg-main);
            color: var(--text-primary);
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            transition: background-color 180ms ease, color 180ms ease;
        }

        button, input {
            font: inherit;
        }

        button {
            color: inherit;
            cursor: pointer;
        }

        .navbar {
            position: fixed;
            z-index: 20;
            top: 0;
            right: 0;
            left: 0;
            display: flex;
            height: 46px;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            padding: 0 12px;
            border-bottom: 1px solid var(--border-color);
            background: var(--bg-navbar);
            transition: background-color 180ms ease, border-color 180ms ease;
        }

        .navbar-section, .navbar-actions {
            display: flex;
            min-width: 0;
            align-items: center;
            gap: 8px;
        }

        .apps-icon {
            display: grid;
            width: 26px;
            height: 26px;
            flex: 0 0 auto;
            grid-template-columns: repeat(3, 4px);
            place-content: center;
            gap: 3px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
        }

        .apps-icon span {
            width: 4px;
            height: 4px;
            border-radius: 1px;
            background: var(--text-muted);
        }

        .board-name {
            width: min(340px, 42vw);
            padding: 5px 8px;
            overflow: hidden;
            border: 1px solid transparent;
            border-radius: 7px;
            outline: none;
            background: transparent;
            color: var(--text-primary);
            font-weight: 700;
            text-overflow: ellipsis;
        }

        .board-name:hover, .board-name:focus {
            border-color: var(--border-color);
            background: var(--btn-bg);
        }

        .pill, .pill-link {
            display: inline-flex;
            min-height: 31px;
            align-items: center;
            justify-content: center;
            padding: 5px 12px;
            border: 1px solid var(--border-color);
            border-radius: 999px;
            background: transparent;
            color: var(--text-primary);
            font-size: 0.82rem;
            font-weight: 700;
            text-decoration: none;
            white-space: nowrap;
        }

        .pill:hover, .pill-link:hover {
            background: var(--btn-bg);
        }

        .save-button {
            border-color: #4a90e2;
            background: #4a90e2;
            color: #ffffff;
        }

        .save-button:hover {
            background: #357dc7;
        }

        .theme-toggle {
            width: 34px;
            padding: 0;
            font-size: 1rem;
        }

        .sidebar {
            position: fixed;
            z-index: 15;
            top: 46px;
            bottom: 0;
            left: 0;
            width: 80px;
            overflow-x: hidden;
            overflow-y: auto;
            border-right: 1px solid var(--border-color);
            background: var(--bg-sidebar);
            scrollbar-width: thin;
            transition: background-color 180ms ease, border-color 180ms ease;
        }

        .tool-button {
            position: relative;
            display: flex;
            width: 100%;
            min-height: 55px;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 3px;
            border: 0;
            border-left: 3px solid transparent;
            background: transparent;
            color: var(--text-muted);
            font-size: 0.67rem;
        }

        .tool-button:hover {
            background: var(--btn-bg);
            color: var(--text-primary);
        }

        .tool-button.active {
            border-left-color: #4a90e2;
            background: var(--tool-active-bg);
            color: var(--text-primary);
        }

        .tool-icon {
            font-size: 1.05rem;
            font-weight: 800;
            line-height: 1;
        }

        .sidebar-divider {
            height: 1px;
            margin: 7px 10px 11px;
            background: var(--border-color);
        }

        .sidebar-label {
            margin: 0 0 8px;
            color: var(--text-muted);
            font-size: 0.58rem;
            font-weight: 800;
            letter-spacing: 0.12em;
            text-align: center;
        }

        .swatches {
            display: grid;
            grid-template-columns: repeat(2, 20px);
            justify-content: center;
            gap: 8px;
            margin-bottom: 13px;
        }

        .swatch {
            width: 20px;
            height: 20px;
            padding: 0;
            border: 2px solid transparent;
            border-radius: 50%;
            background: var(--swatch-color);
            box-shadow: 0 0 0 1px rgba(127, 127, 127, 0.35);
        }

        .swatch.active {
            border-color: var(--bg-sidebar);
            outline: 2px solid var(--swatch-ring);
            outline-offset: 1px;
        }

        .custom-color {
            display: grid;
            justify-items: center;
            gap: 4px;
            margin: 0 7px 13px;
            padding: 6px 4px;
            border: 1px solid transparent;
            border-radius: 7px;
            color: var(--text-muted);
            font-size: 0.6rem;
            font-weight: 700;
            cursor: pointer;
        }

        .custom-color:hover, .custom-color.active {
            border-color: var(--border-color);
            background: var(--tool-active-bg);
            color: var(--text-primary);
        }

        .custom-color.active {
            outline: 2px solid var(--swatch-ring);
            outline-offset: -3px;
        }

        .custom-color input {
            width: 38px;
            height: 24px;
            padding: 2px;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            background: var(--btn-bg);
            cursor: pointer;
        }

        .width-options {
            display: grid;
            gap: 4px;
            padding: 0 7px 12px;
        }

        .width-button {
            min-height: 27px;
            border: 1px solid transparent;
            border-radius: 6px;
            background: transparent;
            color: var(--text-muted);
            font-size: 0.62rem;
            font-weight: 700;
        }

        .width-button:hover, .width-button.active {
            border-color: var(--border-color);
            background: var(--tool-active-bg);
            color: var(--text-primary);
        }

        .canvas-area {
            position: fixed;
            top: 46px;
            right: 0;
            bottom: 0;
            left: 80px;
            background: var(--bg-main);
            transition: background-color 180ms ease;
        }

        #canvas-container {
            width: 100%;
            height: 100%;
            transition: background-color 180ms ease;
        }

        body.dark-mode #canvas-container {
            background: #1c1c1c;
        }

        body.light-mode #canvas-container {
            background: #fafafa;
        }

        .bottom-overlay {
            position: fixed;
            z-index: 20;
            right: 20px;
            bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .overlay-pill {
            display: inline-flex;
            min-height: 36px;
            align-items: center;
            gap: 5px;
            padding: 4px;
            border: 1px solid var(--border-color);
            border-radius: 999px;
            background: var(--btn-bg);
            color: var(--btn-text);
            box-shadow: 0 5px 18px rgba(0, 0, 0, 0.16);
        }

        .overlay-pill button {
            min-width: 29px;
            height: 27px;
            padding: 0 8px;
            border: 0;
            border-radius: 999px;
            background: transparent;
            color: inherit;
            font-weight: 800;
        }

        .overlay-pill button:hover {
            background: var(--tool-active-bg);
        }

        .zoom-text {
            min-width: 48px;
            font-size: 0.74rem;
            font-weight: 800;
            text-align: center;
        }

        .reset-view {
            padding: 0 12px !important;
            font-size: 0.72rem;
        }

        .help-button {
            width: 36px;
            height: 36px;
            padding: 0;
            border: 1px solid var(--border-color);
            border-radius: 50%;
            background: var(--btn-bg);
            color: var(--btn-text);
            box-shadow: 0 5px 18px rgba(0, 0, 0, 0.16);
            font-weight: 800;
        }

        .status {
            position: fixed;
            z-index: 25;
            right: 20px;
            bottom: 68px;
            max-width: min(380px, calc(100vw - 40px));
            padding: 10px 14px;
            border-radius: 9px;
            background: #167342;
            color: #ffffff;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.24);
            font-size: 0.82rem;
            opacity: 0;
            pointer-events: none;
            transform: translateY(6px);
            transition: opacity 150ms ease, transform 150ms ease;
        }

        .status.visible {
            opacity: 1;
            transform: translateY(0);
        }

        .status.error {
            background: #b93333;
        }

        @media (max-width: 680px) {
            .navbar {
                gap: 6px;
                padding: 0 6px;
            }

            .apps-icon, .board-list-label {
                display: none;
            }

            .board-name {
                width: min(180px, 38vw);
            }

            .pill, .pill-link {
                padding: 5px 9px;
            }

            .bottom-overlay {
                right: 10px;
                bottom: 10px;
            }
        }
    </style>
</head>
<body class="dark-mode">
    <header class="navbar">
        <div class="navbar-section">
            <a class="pill-link" href="{{ route('boards.index') }}" aria-label="Back to boards">← Back</a>
            <span class="apps-icon" aria-hidden="true">
                @for ($i = 0; $i < 9; $i++)
                    <span></span>
                @endfor
            </span>
            <input id="board-name" class="board-name" type="text" value="{{ $board->name }}" aria-label="Board name" maxlength="255">
        </div>

        <div class="navbar-actions">
            <a class="pill-link" href="{{ route('boards.index') }}"><span class="board-list-label">Board </span>List</a>
            <button id="save" class="pill save-button" type="button">Save</button>
            <button id="theme-toggle" class="pill theme-toggle" type="button" aria-label="Toggle light and dark mode">☀</button>
        </div>
    </header>

    <aside class="sidebar" aria-label="Whiteboard tools">
        <button class="tool-button active" type="button" data-tool="select"><span class="tool-icon">V</span><span>Select</span></button>
        <button class="tool-button" type="button" data-tool="freehand"><span class="tool-icon">~</span><span>Freehand</span></button>
        <button class="tool-button" type="button" data-tool="rectangle"><span class="tool-icon">□</span><span>Rectangle</span></button>
        <button class="tool-button" type="button" data-tool="circle"><span class="tool-icon">○</span><span>Circle</span></button>
        <button class="tool-button" type="button" data-tool="line"><span class="tool-icon">╱</span><span>Line</span></button>
        <button class="tool-button" type="button" data-tool="arrow"><span class="tool-icon">→</span><span>Arrow</span></button>
        <button class="tool-button" type="button" data-tool="text"><span class="tool-icon">T</span><span>Text</span></button>

        <div class="sidebar-divider"></div>
        <p class="sidebar-label">STROKE</p>

        <div class="swatches" aria-label="Stroke colors">
            <button class="swatch" type="button" data-color="#ef4444" style="--swatch-color: #ef4444" aria-label="Red"></button>
            <button class="swatch active" type="button" data-color="#4a90e2" style="--swatch-color: #4a90e2" aria-label="Blue"></button>
            <button class="swatch" type="button" data-color="#22c55e" style="--swatch-color: #22c55e" aria-label="Green"></button>
            <button class="swatch" type="button" data-color="#facc15" style="--swatch-color: #facc15" aria-label="Yellow"></button>
            <button class="swatch" type="button" data-color="#a855f7" style="--swatch-color: #a855f7" aria-label="Purple"></button>
            <button class="swatch" type="button" data-color="#22d3ee" style="--swatch-color: #22d3ee" aria-label="Cyan"></button>
            <button class="swatch" type="button" data-color="#ffffff" style="--swatch-color: #ffffff" aria-label="White"></button>
            <button class="swatch" type="button" data-color="#f97316" style="--swatch-color: #f97316" aria-label="Orange"></button>
        </div>

        <label id="custom-color-control" class="custom-color">
            <span>Custom</span>
            <input id="custom-color" type="color" value="#4a90e2" aria-label="Custom stroke color">
        </label>

        <div class="width-options" aria-label="Stroke width">
            <button class="width-button" type="button" data-width="2">Thin</button>
            <button class="width-button active" type="button" data-width="5">Medium</button>
            <button class="width-button" type="button" data-width="10">Thick</button>
        </div>
    </aside>

    <main class="canvas-area">
        <div id="canvas-container"></div>
    </main>

    <div class="bottom-overlay">
        <div class="overlay-pill" aria-label="Zoom controls">
            <button id="zoom-out" type="button" aria-label="Zoom out">−</button>
            <span id="zoom-text" class="zoom-text">100%</span>
            <button id="zoom-in" type="button" aria-label="Zoom in">+</button>
        </div>
        <div class="overlay-pill">
            <button id="reset-view" class="reset-view" type="button">Reset View</button>
        </div>
        <button id="help" class="help-button" type="button" aria-label="Whiteboard help">?</button>
    </div>

    <div id="status" class="status" role="status" aria-live="polite"></div>

    <script>
        (() => {
            const savedCanvas = @json($board->canvas_data);
            const apiUrl = @json(route('api.boards.update', $board));
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
            const body = document.body;
            const container = document.getElementById('canvas-container');
            const boardNameInput = document.getElementById('board-name');
            const themeToggle = document.getElementById('theme-toggle');
            const zoomText = document.getElementById('zoom-text');
            const status = document.getElementById('status');
            const toolButtons = Array.from(document.querySelectorAll('.tool-button'));
            const swatches = Array.from(document.querySelectorAll('.swatch'));
            const widthButtons = Array.from(document.querySelectorAll('.width-button'));
            const customColorControl = document.getElementById('custom-color-control');
            const customColorInput = document.getElementById('custom-color');

            let stage;
            let contentLayer;
            let uiLayer;
            let transformer;
            let activeTool = 'select';
            let activeColor = '#4a90e2';
            let activeWidth = 5;
            let drawing = false;
            let panning = false;
            let draft = null;
            let startPoint = null;
            let panStart = null;
            let modified = false;
            let saving = false;
            let statusTimer = null;

            const containerSize = () => ({
                width: container.clientWidth,
                height: container.clientHeight,
            });

            const createEmptyStage = () => {
                const size = containerSize();
                stage = new Konva.Stage({
                    container: 'canvas-container',
                    width: size.width,
                    height: size.height,
                });
                contentLayer = new Konva.Layer();
                stage.add(contentLayer);
            };

            const restoreStage = () => {
                if (!savedCanvas) {
                    createEmptyStage();
                    return;
                }

                try {
                    stage = Konva.Node.create(savedCanvas, 'canvas-container');
                    stage.size(containerSize());
                    stage.find('Transformer').forEach((node) => node.destroy());
                    contentLayer = stage.getLayers()[0];

                    if (!contentLayer) {
                        contentLayer = new Konva.Layer();
                        stage.add(contentLayer);
                    }
                } catch (error) {
                    createEmptyStage();
                    showStatus('The saved canvas could not be restored. A blank canvas was opened.', true);
                }
            };

            const createSelectionLayer = () => {
                uiLayer = new Konva.Layer({ listening: true });
                transformer = new Konva.Transformer({
                    rotateEnabled: true,
                    borderStroke: '#4a90e2',
                    anchorStroke: '#4a90e2',
                    anchorFill: '#ffffff',
                    anchorSize: 8,
                });
                uiLayer.add(transformer);
                stage.add(uiLayer);
            };

            const showStatus = (message, isError = false) => {
                clearTimeout(statusTimer);
                status.textContent = message;
                status.classList.toggle('error', isError);
                status.classList.add('visible');
                statusTimer = setTimeout(() => status.classList.remove('visible'), 3500);
            };

            const markModified = () => {
                modified = true;
            };

            const relativePointer = () => stage.getRelativePointerPosition();

            const selectShape = (shape) => {
                transformer.nodes(shape ? [shape] : []);
                uiLayer.batchDraw();
            };

            const isSelectable = (node) => node && node !== stage && node !== contentLayer && node !== uiLayer &&
                node !== transformer && !(node.getParent() instanceof Konva.Transformer);

            const updateShapeInteraction = () => {
                const selecting = activeTool === 'select';
                contentLayer.getChildren().forEach((node) => node.draggable(selecting));
                container.style.cursor = selecting ? 'default' : 'crosshair';

                if (!selecting) {
                    selectShape(null);
                }
            };

            const setTool = (tool) => {
                activeTool = tool;
                toolButtons.forEach((button) => button.classList.toggle('active', button.dataset.tool === tool));
                updateShapeInteraction();
            };

            const commonAttrs = () => ({
                stroke: activeColor,
                strokeWidth: activeWidth,
                lineCap: 'round',
                lineJoin: 'round',
                draggable: false,
            });

            const addShape = (shape) => {
                contentLayer.add(shape);
                contentLayer.batchDraw();
                markModified();
                return shape;
            };

            const beginDrawing = () => {
                const point = relativePointer();
                if (!point) {
                    return;
                }

                if (activeTool === 'text') {
                    const value = prompt('Enter text:');
                    if (value) {
                        addShape(new Konva.Text({
                            x: point.x,
                            y: point.y,
                            text: value,
                            fill: activeColor,
                            fontSize: Math.max(18, activeWidth * 5),
                            draggable: false,
                        }));
                    }
                    return;
                }

                drawing = true;
                startPoint = point;
                const attrs = commonAttrs();

                if (activeTool === 'freehand') {
                    draft = addShape(new Konva.Line(Object.assign({}, attrs, {
                        points: [point.x, point.y],
                        tension: 0.25,
                    })));
                } else if (activeTool === 'rectangle') {
                    draft = addShape(new Konva.Rect(Object.assign({}, attrs, {
                        x: point.x,
                        y: point.y,
                        width: 0,
                        height: 0,
                    })));
                } else if (activeTool === 'circle') {
                    draft = addShape(new Konva.Ellipse(Object.assign({}, attrs, {
                        x: point.x,
                        y: point.y,
                        radiusX: 0,
                        radiusY: 0,
                    })));
                } else if (activeTool === 'line') {
                    draft = addShape(new Konva.Line(Object.assign({}, attrs, {
                        points: [point.x, point.y, point.x, point.y],
                    })));
                } else if (activeTool === 'arrow') {
                    draft = addShape(new Konva.Arrow(Object.assign({}, attrs, {
                        points: [point.x, point.y, point.x, point.y],
                        pointerLength: Math.max(8, activeWidth * 3),
                        pointerWidth: Math.max(8, activeWidth * 3),
                    })));
                }
            };

            const continueDrawing = () => {
                if (!drawing || !draft) {
                    return;
                }

                const point = relativePointer();
                if (!point) {
                    return;
                }

                if (activeTool === 'freehand') {
                    draft.points(draft.points().concat([point.x, point.y]));
                } else if (activeTool === 'rectangle') {
                    draft.position({ x: Math.min(startPoint.x, point.x), y: Math.min(startPoint.y, point.y) });
                    draft.size({ width: Math.abs(point.x - startPoint.x), height: Math.abs(point.y - startPoint.y) });
                } else if (activeTool === 'circle') {
                    draft.position({ x: (startPoint.x + point.x) / 2, y: (startPoint.y + point.y) / 2 });
                    draft.radius({
                        x: Math.abs(point.x - startPoint.x) / 2,
                        y: Math.abs(point.y - startPoint.y) / 2,
                    });
                } else {
                    draft.points([startPoint.x, startPoint.y, point.x, point.y]);
                }

                contentLayer.batchDraw();
                markModified();
            };

            const finishPointerAction = () => {
                drawing = false;
                panning = false;
                draft = null;
                startPoint = null;
                panStart = null;
                container.style.cursor = activeTool === 'select' ? 'default' : 'crosshair';
            };

            const applyZoom = (newScale, focalPoint = null) => {
                const oldScale = stage.scaleX();
                const scale = Math.min(3, Math.max(0.2, newScale));
                const focus = focalPoint || { x: container.clientWidth / 2, y: container.clientHeight / 2 };
                const stagePoint = {
                    x: (focus.x - stage.x()) / oldScale,
                    y: (focus.y - stage.y()) / oldScale,
                };

                stage.scale({ x: scale, y: scale });
                stage.position({
                    x: focus.x - stagePoint.x * scale,
                    y: focus.y - stagePoint.y * scale,
                });
                zoomText.textContent = `${Math.round(scale * 100)}%`;
                stage.batchDraw();
            };

            const resetView = () => {
                stage.position({ x: 0, y: 0 });
                stage.scale({ x: 1, y: 1 });
                zoomText.textContent = '100%';
                stage.batchDraw();
            };

            const applyTheme = (theme) => {
                const light = theme === 'light-mode';
                body.classList.toggle('light-mode', light);
                body.classList.toggle('dark-mode', !light);
                themeToggle.textContent = light ? '🌙' : '☀';
                document.documentElement.style.background = light ? '#f5f5f5' : '#111111';
            };

            const saveCanvas = async (automatic = false) => {
                if (saving) {
                    return;
                }

                saving = true;
                const selectedNodes = transformer.nodes();
                transformer.nodes([]);
                uiLayer.remove();
                const canvasData = stage.toJSON();
                stage.add(uiLayer);
                transformer.nodes(selectedNodes);
                uiLayer.batchDraw();

                try {
                    const response = await fetch(apiUrl, {
                        method: 'PUT',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        body: JSON.stringify({
                            name: boardNameInput.value.trim(),
                            canvas_data: canvasData,
                        }),
                    });

                    if (!response.ok) {
                        const data = await response.json().catch(() => ({}));
                        throw new Error(data.message || 'The board could not be saved.');
                    }

                    const board = await response.json();
                    boardNameInput.value = board.name;
                    modified = false;
                    showStatus(automatic ? 'Canvas auto-saved.' : 'Canvas saved successfully.');
                } catch (error) {
                    showStatus(error.message || 'Unable to save the canvas. Please try again.', true);
                } finally {
                    saving = false;
                }
            };

            applyTheme(localStorage.getItem('whiteboard-theme') || 'dark-mode');
            restoreStage();
            createSelectionLayer();
            updateShapeInteraction();
            zoomText.textContent = `${Math.round(stage.scaleX() * 100)}%`;

            toolButtons.forEach((button) => button.addEventListener('click', () => setTool(button.dataset.tool)));
            swatches.forEach((swatch) => swatch.addEventListener('click', () => {
                activeColor = swatch.dataset.color;
                swatches.forEach((item) => item.classList.toggle('active', item === swatch));
                customColorControl.classList.remove('active');
            }));
            customColorInput.addEventListener('input', () => {
                activeColor = customColorInput.value;
                swatches.forEach((item) => item.classList.remove('active'));
                customColorControl.classList.add('active');
            });
            widthButtons.forEach((button) => button.addEventListener('click', () => {
                activeWidth = Number(button.dataset.width);
                widthButtons.forEach((item) => item.classList.toggle('active', item === button));
            }));

            boardNameInput.addEventListener('input', markModified);
            document.getElementById('save').addEventListener('click', () => saveCanvas(false));
            document.getElementById('reset-view').addEventListener('click', resetView);
            document.getElementById('zoom-in').addEventListener('click', () => applyZoom(stage.scaleX() + 0.1));
            document.getElementById('zoom-out').addEventListener('click', () => applyZoom(stage.scaleX() - 0.1));
            document.getElementById('help').addEventListener('click', () => {
                showStatus('Select to move or resize shapes. Drag empty canvas to pan. Use the wheel or controls to zoom.');
            });
            themeToggle.addEventListener('click', () => {
                const theme = body.classList.contains('dark-mode') ? 'light-mode' : 'dark-mode';
                localStorage.setItem('whiteboard-theme', theme);
                applyTheme(theme);
            });

            stage.on('mousedown touchstart', (event) => {
                if (activeTool === 'select') {
                    if (event.target === stage) {
                        selectShape(null);
                        panning = true;
                        const pointer = stage.getPointerPosition();
                        panStart = { pointer, position: stage.position() };
                        container.style.cursor = 'grabbing';
                    } else if (isSelectable(event.target)) {
                        selectShape(event.target);
                    }
                    return;
                }

                beginDrawing();
            });

            stage.on('mousemove touchmove', () => {
                if (panning && panStart) {
                    const pointer = stage.getPointerPosition();
                    stage.position({
                        x: panStart.position.x + pointer.x - panStart.pointer.x,
                        y: panStart.position.y + pointer.y - panStart.pointer.y,
                    });
                    stage.batchDraw();
                    return;
                }

                continueDrawing();
            });

            stage.on('mouseup touchend mouseleave', finishPointerAction);
            stage.on('dragend transformend', (event) => {
                if (isSelectable(event.target)) {
                    markModified();
                }
            });
            stage.on('dblclick dbltap', (event) => {
                if (event.target instanceof Konva.Text) {
                    const value = prompt('Edit text:', event.target.text());
                    if (value !== null) {
                        event.target.text(value);
                        contentLayer.batchDraw();
                        markModified();
                    }
                }
            });
            stage.on('wheel', (event) => {
                event.evt.preventDefault();
                const factor = event.evt.deltaY > 0 ? 1 / 1.08 : 1.08;
                applyZoom(stage.scaleX() * factor, stage.getPointerPosition());
            });

            window.addEventListener('keydown', (event) => {
                if ((event.key === 'Delete' || event.key === 'Backspace') &&
                    transformer.nodes().length &&
                    document.activeElement !== boardNameInput) {
                    event.preventDefault();
                    transformer.nodes().forEach((node) => node.destroy());
                    selectShape(null);
                    contentLayer.batchDraw();
                    markModified();
                }
            });
            window.addEventListener('resize', () => {
                stage.size(containerSize());
                stage.batchDraw();
            });
            window.addEventListener('beforeunload', (event) => {
                if (modified) {
                    event.preventDefault();
                }
            });

            setInterval(() => {
                if (modified) {
                    saveCanvas(true);
                }
            }, 60000);
        })();
    </script>
</body>
</html>
