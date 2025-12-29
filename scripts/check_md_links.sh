#!/usr/bin/env bash
set -euo pipefail
ROOT="c:/ci4-local"
cd "$ROOT"

broken_count=0
trailing_count=0
md_count=0

find . -type f -name '*.md' -print0 | while IFS= read -r -d '' file; do
  md_count=$((md_count+1))
  # trailing whitespace
  if grep -nE ' $' "$file" >/dev/null 2>&1; then
    trailing_count=$((trailing_count+1))
    echo "TRAILING_WS: $file"
    grep -nE ' $' "$file" || true
  fi

  # extract links using perl
  perl -nle 'while (/\[([^\]]+)\]\(([^)]+)\)/g) { print $ARGV, "\t", $1, "\t", $2 }' "$file" |
  while IFS=$'\t' read -r src text link; do
    case "$link" in
      http*|mailto:*|#*) continue;;
    esac
    # strip fragment and query
    link_clean=$(echo "$link" | sed -E 's/[#?].*$//')
    if [ -z "$link_clean" ]; then continue; fi
    target=$(dirname "$src")
    target="$target/$link_clean"
    # normalize path using realpath if available, otherwise basic check
    if command -v realpath >/dev/null 2>&1; then
      target=$(realpath -m "$target")
    fi
    if [ ! -e "$target" ]; then
      echo "BROKEN_LINK: $src -> [$text]($link_clean) (resolved: $target)"
      broken_count=$((broken_count+1))
    fi
  done

done

echo
echo "Checked $md_count markdown files. Trailing whitespace files: $trailing_count. Broken links: $broken_count."
if [ "$broken_count" -gt 0 ]; then exit 2; fi
exit 0
