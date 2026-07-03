# SAMIRHV — REPOSITORY

Personal blog by Samir Hanna Verza, built with Laravel and the Canvas theme.

## Technologies

- **Backend:** Laravel (PHP 8.4+)
- **Frontend:** Blade + Canvas 7 (HTML5 theme — assets compiled into `public/vendor/canvas/`)
- **Database:** MySQL / MariaDB for app storage (nothing else, and never use sqlite). *Exception:* the admin **AI-MEMORY** module reads the external `ai-memory` SQLite **read-only** — see `samirhv/docs/AI-MEMORY.md`.
- **Server:** Debian (Linux)
- **GitHub:** Always commit in blocks and with a good description; the standard is the version from version.md - (hyphen) comment

## Goal

A personal space to publish posts about technology, software development, Linux and other topics of interest.

## Folder Structure

```
samirhv/                     ← repository root
├── samirhv/                 ← Laravel application
│   ├── app/
│   │   ├── Http/Controllers/BlogController.php
│   │   └── ...
│   ├── public/
│   │   ├── vendor/canvas/   ← Canvas theme assets (CSS, JS)
│   │   └── favicon.ico
│   ├── resources/views/
│   │   ├── layouts/app.blade.php
│   │   ├── home.blade.php
│   │   └── blog/            ← index.blade.php, show.blade.php
│   ├── routes/web.php
│   └── ...
├── img/                     ← project favicons and images
├── tmp/                     ← reference files (git-ignored, will be deleted)
├── CLAUDE.md                ← guide for AI agents
├── SECURITY_GUIDELINES.md
└── version.md
```

## Posts

For now, posts are defined as a static array in `BlogController`. To add a new post, edit the `allPosts()` method in `app/Http/Controllers/BlogController.php`.

In the future, migrate to a database with a `posts` table.

## Version

`version.md` at the root records the public version in the `X.Y.Z` format:

- **X** — stable version (manual change)
- **Y** — significant structural change
- **Z** — increment for each new screen, table, or layout change
