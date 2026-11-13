# Silkscreen WordPress

Deployable classic theme for Silkscreen Entertainment — sibling of [`antennae-silkscreen`](../antennae-silkscreen) (Next.js reference) and same FTP workflow as [`antennae-gabos-wordpress`](../antennae-gabos-wordpress).

## Quick start

```bash
npm install
npm run deploy        # changed files in wordpress/
npm run verify        # FTP connection check
```

### Local preview content

With **Local** site running, ACF active, and **Silkscreen 2026** theme on:

```bash
npm run seed:local
```

Imports demo posts/pages from `antennae-silkscreen` (`seed/records.php`) into the WordPress database. See [seed/README.md](seed/README.md).

Theme details: [wordpress/README.md](wordpress/README.md).
