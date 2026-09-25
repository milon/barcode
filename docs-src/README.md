# milon/barcode documentation site

Source for the [Papyrus](https://github.com/milon/papyrus) multi-page docs site
(**v1.3+**). The live README still lives at `../readme.md`; chapters here were
imported from it (`papyrus import-readme`) and can be edited independently.

## Papyrus features in use

| Feature | Where |
|---------|--------|
| `site.mode: docs` | Package home with **Get started** CTA |
| `site.nav` | Grouped sidebar + Prev/Next order |
| `site.repository` / `edit_path` / `edit_branch` | **Edit this page** on chapters |
| `site.links` | GitHub / Packagist / Issues + chapter links |
| `site.banner` + `lead` | Home hero |
| `site.base_path: /barcode` | Project GitHub Pages |
| Built-in | Search, code **Copy**, **On this page** (`h2`/`h3`), sitemap/robots/`404` |
| `build:site` asset copy | `assets/examples/*` and banner |

Optional later: `site.versions` (2+ peer deploys) for a version switcher; Mermaid
(`mermaid.enabled: true` + `mmdc`); `papyrus watch --with-site`.

## Build

Requires [milon/papyrus](https://github.com/milon/papyrus) **^1.3.1** (CI pins
`v1.3.1`).

```bash
docs-src/bin/build-site
# or: PAPYRUS_BIN=/path/to/papyrus docs-src/bin/build-site
```

Output: `docs/milon-barcode-site/`. Preview (needed for popup search):

```bash
papyrus serve -d docs-src -e docs
# open http://127.0.0.1:8000/barcode/
```

Regenerate example PNGs first if needed:

```bash
php docs/generate-examples.php
```

Refresh chapters from the root README (overwrites `content/`):

```bash
papyrus import-readme -d docs-src --file readme.md --force
# then restore site.nav groupings in papyrus.yml
```

## GitHub Pages

Workflow: `.github/workflows/docs-site.yml` (checks out Papyrus **v1.3.1**).
Set the Pages source to **GitHub Actions**. The site uses `base_path: /barcode`
for project Pages at `https://<user>.github.io/barcode/`.
