# milon/barcode documentation site

Source for the [Papyrus](https://github.com/milon/papyrus) multi-page docs site (**v1.5+**).

**Live:** [oss.milon.im/barcode](https://oss.milon.im/barcode/)  
**Origin:** [milon.github.io/barcode](https://milon.github.io/barcode/) (this repo’s GitHub Pages)

`site.base_path` is `/barcode`. `site.cname` is only for sitemap absolutes; CI deletes the generated `CNAME` so Pages does not claim `oss.milon.im` (the Cloudflare Worker in [milon/oss](https://github.com/milon/oss) owns that host).

## Build

```bash
docs-src/bin/build-site
php papyrus.phar serve -d docs-src -e docs
# open http://127.0.0.1:8000/barcode/
```

## CI

`.github/workflows/docs-site.yml` builds and deploys to GitHub Pages on push to `master`/`main`.
