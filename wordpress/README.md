# Silkscreen WordPress theme

Classic PHP theme (same ops model as `antennae-gabos-wordpress`), aligned with the Next.js reference in `antennae-silkscreen`.

## Requirements

- WordPress with **Advanced Custom Fields** installed
- Set **Settings → Reading** → “A static page” and choose your landing page as the front page

## Content model

| Type | Use |
|------|-----|
| **Projects** (`silkscreen_project`) | Films / poster titles — replace Gabos “posts”. Full ACF panel for director, stills, credits, featured, posters, etc. |
| **News** (`silkscreen_news`) | Masony items with **three layout types**: caption below, caption overlay, compact — plus masonry size (narrow → half-short). |
| **Front page** ACF | Footer/social site settings (copyright, social links) |
| **About** page (`page-about.php`) | About statement |
| **Contact** page (`page-contact.php`) | Intro + 3 primary emails (label + address each) |
| **Shop** page (`page-shop-settings.php`, admin catalog) | Up to 6 products (mirrors `records.ts`) |

Routes: `/projects/`, `/projects/{slug}/`, `/news/`, `/shop/`, `/shop/{slug}/`, plus page templates Contact, About, Posters.

## Deploy

From this repo root (same FTP flow as Gabos):

```bash
npm install
cp .env.example .env   # or copy from gabos-wordpress
npm run deploy
npm run deploy -- --all
```

Uploads the `wordpress/` directory to `FTP_REMOTE_DIR` on the server.

## Assets

Theme ships with `assets/media/` and `assets/brand/` copied from `public/`. Replace images via the media library + ACF fields; keep Figma crop dimensions for news sizes.
