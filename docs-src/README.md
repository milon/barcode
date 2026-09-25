# milon/barcode documentation site

Source for the [Papyrus](https://github.com/milon/papyrus) multi-page docs site
(**v1.5+**). The live README still lives at `../readme.md`; chapters here were
imported from it (`papyrus import-readme`) and can be edited independently.

## Papyrus features in use

| Feature | Where |
|---------|--------|
| Unified home | **Get started** CTA + author + `links` |
| `site.nav` | Collapsible sidebar groups + Prev/Next order |
| `site.edit` (`link` / `path` / `branch`) | **Edit this page** icon on chapters |
| `site.copy_code` | Icon **Copy** on code fences |
| `site.copy_markdown` / `site.print_page` | **Copy as Markdown** + **Print this page** |
| `site.page_toc` | **On this page** rail |
| `site.links` | GitHub / Packagist / Issues + chapter links |
| `site.banner` + `lead` | Home hero |
| `site.cname: barcode.milon.im` | Custom domain (site at `/`, no `base_path`) |
| Built-in | Search, sitemap/robots/`404`, image max-width in column |
| `build:site` asset copy | `assets/examples/*` and banner |
| CI | Downloads `papyrus.phar` **v1.5.0** (no Composer dep on Papyrus) |

Optional later: `site.versions` (2+ peer deploys); Mermaid (`mermaid.enabled: true`
+ `mmdc` in CI); `papyrus watch --with-site`.

## Build

Requires [milon/papyrus](https://github.com/milon/papyrus) **^1.5**. CI pins the
release PHAR (`v1.5.0`). Locally you can use the PHAR, a sibling checkout, or
`composer require milon/papyrus:^1.5` (PHP 8.2+).

```bash
# optional: pin the same PHAR CI uses
curl -fsSL -o papyrus.phar \
  https://github.com/milon/papyrus/releases/download/v1.5.0/papyrus.phar
chmod +x papyrus.phar

docs-src/bin/build-site
# or: PAPYRUS_BIN="php ./papyrus.phar" docs-src/bin/build-site
```

Output: `docs/milon-barcode-site/`. Preview (needed for popup search):

```bash
php papyrus.phar serve -d docs-src -e docs
# open http://127.0.0.1:8000/
```

Regenerate example PNGs first if needed:

```bash
php docs/generate-examples.php
```

Refresh chapters from the root README (overwrites `content/`):

```bash
php papyrus.phar import-readme -d docs-src --file readme.md --force
# then restore site.nav groupings in papyrus.yml
```

## GitHub Pages

Workflow: `.github/workflows/docs-site.yml` (Papyrus PHAR **v1.5.0**).

1. Pages source → **GitHub Actions**
2. Custom domain → **barcode.milon.im** (Papyrus writes a `CNAME` file on each build)
3. Enable **Enforce HTTPS** once the certificate is ready

Live site: https://barcode.milon.im/
