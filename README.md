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

## Git workflow

`main` is protected — every change lands via a pull request with green CI,
squash-merged so history stays one clean commit per change.

1. Branch off the latest `main`, named after the change type:
   `git checkout -b feat/my-change` (`feat/`, `fix/`, `chore/`, `docs/`)
2. Commit using [Conventional Commits](https://www.conventionalcommits.org/)
3. Push and open a PR: `git push -u origin feat/my-change`, then `gh pr create --fill`
4. Wait for CI — PHPCS must pass
5. Squash-merge: `gh pr merge --squash` (the remote branch is deleted automatically)