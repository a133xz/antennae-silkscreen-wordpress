# Silkscreen demo seed

Loads content from `antennae-silkscreen` (`src/content/records.ts`, mirrored in `records.php`) into your Local WordPress database — pages, ACF fields, projects, news, shop slots, and media copied into the uploads folder.

## Requirements

- **antennae-silkscreen-wordpress** theme active
- **Advanced Custom Fields** plugin installed and active
- PHP CLI (Local’s site shell)

## Run

From the theme repo root:

```bash
php seed/import-demo.php
```

Re-import (replaces previous demo posts marked `_silkscreen_demo`):

```bash
php seed/import-demo.php --force
```

If WordPress lives somewhere else:

```bash
WP_LOAD_PATH="/path/to/wp-load.php" php seed/import-demo.php
```

After seeding, visit the site home URL. **Settings → Reading** should already use the **Home** page as the static front page.

## npm

```bash
npm run seed:local
```
