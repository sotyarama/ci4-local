#!/usr/bin/env bash
set -euo pipefail

# Usage: run from repo root locally where composer is available:
#   bash scripts/package_release.sh v1.0.0
# Produces: release-TAG.zip in the project root

TAG=${1:-release}
ROOT=$(pwd)
TMP_DIR=$(mktemp -d)
echo "Preparing release package: $TAG -> $TMP_DIR"

echo "Installing dependencies (no-dev, optimized)..."
composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

echo "Copying files..."
rsync -a --delete \
  --exclude='.git' \
  --exclude='tests' \
  --exclude='docs' \
  --exclude='backups' \
  --exclude='vendor/*/tests' \
  --exclude='*.md' \
  ./ "$TMP_DIR/ci4-release"

pushd "$TMP_DIR" >/dev/null
zip -r "$ROOT/release-$TAG.zip" ci4-release
popd >/dev/null

echo "Release archive created: $ROOT/release-$TAG.zip"

rm -rf "$TMP_DIR"
echo "Done."
