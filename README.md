# Infinite Canvas Whiteboard

A Laravel and Konva.js infinite canvas whiteboard application inspired by Draw.io. This app allows users to create, open, edit, rename, delete, save, and reload whiteboard boards.

## Tech Stack

* Laravel
* Blade Templates
* Konva.js via CDN
* SQLite Database
* Vanilla JavaScript
* Inline CSS

## Database

This project uses SQLite.

In the `.env` file, set:

```env
DB_CONNECTION=sqlite
```

Create the SQLite database file at:

```text
database/database.sqlite
```

## Setup Instructions

1. Clone or download the project.

2. Open the project folder in terminal:

```bash
cd infinite-white-board
```

3. Install PHP dependencies:

```bash
composer install
```

4. Copy `.env.example` to `.env`:

```bash
copy .env.example .env
```

5. Generate the application key:

```bash
php artisan key:generate
```

6. Set the database connection in `.env`:

```env
DB_CONNECTION=sqlite
```

7. Create the SQLite database file:

```bash
type nul > database\database.sqlite
```

8. Run database migrations:

```bash
php artisan migrate
```

9. Start the Laravel development server:

```bash
php artisan serve
```

10. Open the app in the browser:

```text
http://localhost:8000
```

## Features

* Full-screen infinite whiteboard
* Pan canvas by dragging the background
* Zoom in and out using mouse wheel
* Zoom percentage indicator
* Reset view button
* Select tool
* Freehand drawing tool
* Rectangle tool
* Circle tool
* Line tool
* Arrow tool
* Text tool with inline text editing
* Custom color picker
* Preset color swatches
* Stroke width selector: thin, medium, thick
* Select, move, resize, and delete shapes
* Save canvas state using Konva `stage.toJSON()`
* Reload saved canvas state from the database
* Create, open, rename, and delete boards
* Board names are unique
* Dark mode and light mode support
* Auto-save every 60 seconds when the canvas is modified

## API Routes

The application includes the following board API routes:

```text
GET     /api/boards
POST    /api/boards
GET     /api/boards/{board}
PUT     /api/boards/{board}
DELETE  /api/boards/{board}
```

## Web Routes

```text
GET     /
GET     /boards
POST    /boards
GET     /boards/{board}
PUT     /boards/{board}
DELETE  /boards/{board}
```

## Version Control

This project uses Git and includes multiple meaningful commits throughout development.

To check commits:

```bash
git log --oneline
```

## Notes

* No user authentication is required.
* Boards are identified by a unique board name.
* Konva.js is loaded through CDN.
* No Vue, React, Alpine, Tailwind, or separate frontend framework is used.
* The `.env` file should not be committed.
* Only `.env.example` should be included in the repository.
