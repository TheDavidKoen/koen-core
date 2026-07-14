# Koen Core

Functionality plugin for the davidkoen portfolio. Owns the content model —
custom post types, meta fields, taxonomies, and admin UX — so the data survives
any theme change. The koen theme handles presentation only.

## Provides

- `project` post type (archive at `/projects/`) with `project_type` taxonomy
- Project meta: role, year, tech stack, live URL, repository URL
  (registered via `register_post_meta`, REST-exposed, hand-rolled meta box)
- Custom admin columns with year sorting

## Development

```sh
composer install
composer lint   # PHPCS (WordPress Coding Standards)
```