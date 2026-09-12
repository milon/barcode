#!/usr/bin/env bash
# Capture per-example gallery screenshots with Chrome headless.
# Prerequisites: Google Chrome on macOS (or set CHROME=...).
set -euo pipefail

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
EXAMPLES="$ROOT/docs/examples"
OUT="$EXAMPLES/screenshots/browser"
CHROME="${CHROME:-/Applications/Google Chrome.app/Contents/MacOS/Google Chrome}"
PORT="${PORT:-8765}"

if [[ ! -x "$CHROME" ]]; then
  echo "Chrome not found at: $CHROME" >&2
  exit 1
fi

mkdir -p "$OUT"
php "$ROOT/docs/generate-examples.php"

php -S "127.0.0.1:$PORT" -t "$EXAMPLES" >/tmp/milon-barcode-docs-server.log 2>&1 &
PID=$!
cleanup() { kill "$PID" 2>/dev/null || true; rm -f "$EXAMPLES/screenshots"/_card-*.html; }
trap cleanup EXIT
sleep 0.4

EXAMPLES="$EXAMPLES" PORT="$PORT" php <<'PHP'
$dir = getenv('EXAMPLES');
$port = getenv('PORT');
$gallery = file_get_contents("$dir/gallery.html");
preg_match_all('/<article class="card[^"]*" id="([^"]+)">(.*?)<\/article>/s', $gallery, $m, PREG_SET_ORDER);
$css = 'body{margin:0;background:#f6f7f9;font-family:ui-sans-serif,system-ui,sans-serif;padding:24px}.card{background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:18px;max-width:860px}.card h2{margin:0 0 12px;font-size:16px}.card img{max-width:100%;height:auto;image-rendering:pixelated;background:#fff}.card code{display:block;margin-top:12px;font-size:11px;background:#f3f4f6;padding:10px;border-radius:8px;overflow:auto;white-space:pre-wrap}.dark{background:#1f2937;color:#fff}.dark code{background:#111827;color:#e5e7eb}.pair{display:flex;gap:16px;align-items:center}.pair figure{margin:0;flex:1;text-align:center}.pair figcaption{font-size:12px;margin-top:8px;opacity:.8}';
foreach ($m as $match) {
    $id = $match[1];
    $html = "<!DOCTYPE html><html><head><meta charset=utf-8><base href=\"http://127.0.0.1:$port/\"><style>$css</style></head><body>{$match[0]}</body></html>";
    file_put_contents("$dir/screenshots/_card-$id.html", $html);
    echo "$id\n";
}
PHP

for html in "$EXAMPLES/screenshots"/_card-*.html; do
  id="$(basename "$html" .html)"
  id="${id#_card-}"
  "$CHROME" --headless=new --disable-gpu --hide-scrollbars --window-size=920,700 \
    --screenshot="$OUT/$id.png" \
    "http://127.0.0.1:$PORT/screenshots/_card-$id.html" >/dev/null 2>&1
  echo "shot $id"
done

"$CHROME" --headless=new --disable-gpu --hide-scrollbars --window-size=960,4200 \
  --screenshot="$EXAMPLES/screenshots/gallery-full.png" \
  "http://127.0.0.1:$PORT/gallery.html" >/dev/null 2>&1

php -r '
$dir = $argv[1];
foreach (glob("$dir/*.png") as $file) {
    $im = imagecreatefrompng($file);
    $w = imagesx($im); $h = imagesy($im);
    $bg = imagecolorat($im, 0, 0);
    $minX = $w; $minY = $h; $maxX = 0; $maxY = 0;
    for ($y = 0; $y < $h; $y++) {
        for ($x = 0; $x < $w; $x++) {
            if (imagecolorat($im, $x, $y) !== $bg) {
                $minX = min($minX, $x); $minY = min($minY, $y);
                $maxX = max($maxX, $x); $maxY = max($maxY, $y);
            }
        }
    }
    if ($maxX <= $minX || $maxY <= $minY) continue;
    $pad = 8;
    $minX = max(0, $minX - $pad); $minY = max(0, $minY - $pad);
    $maxX = min($w - 1, $maxX + $pad); $maxY = min($h - 1, $maxY + $pad);
    $out = imagecrop($im, ["x" => $minX, "y" => $minY, "width" => $maxX - $minX + 1, "height" => $maxY - $minY + 1]);
    imagepng($out, $file);
}
' "$OUT"

echo "Done. Screenshots in $OUT and $EXAMPLES/screenshots/gallery-full.png"
