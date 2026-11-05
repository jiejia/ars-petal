#!/usr/bin/env bash
# Builds an installable theme ZIP: build/ars-petal-{version}.zip with a single ars-petal/ folder inside.
# public/dist is built from source and is not committed, so `npm run build` must run first (npm run package does that).
# Usage: bin/package.sh [version]   (the version defaults to the "Version:" header in style.css)
set -euo pipefail

cd "$(dirname "$0")/.."

slug="ars-petal"
header_version="$(sed -n 's/^Version:[[:space:]]*//p' style.css | tr -d '[:space:]')"
version="${1:-$header_version}"
version="${version#v}"

if [[ "$version" != "$header_version" ]]; then
  echo "Version $version does not match style.css (Version: $header_version). Bump style.css before tagging." >&2
  exit 1
fi

for file in public/dist/theme.css public/dist/editor.css public/dist/app.js; do
  if [[ ! -s "$file" ]]; then
    echo "Missing $file. Run npm run build first." >&2
    exit 1
  fi
done

rm -rf build
mkdir -p "build/$slug"
rsync -a --exclude-from=.distignore ./ "build/$slug/"

(cd build && zip -qrX "$slug-$version.zip" "$slug")
echo "build/$slug-$version.zip"
