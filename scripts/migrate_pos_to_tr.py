#!/usr/bin/env python3
"""
Add `.tr-` aliases next to `.pos-` selectors inside CSS files under public/css/.

Usage: python scripts/migrate_pos_to_tr.py
This edits files in-place and prints a short summary.
"""
import re
from pathlib import Path


CSS_DIR = Path('public/css')


def process_file(path: Path) -> int:
    text = path.read_text(encoding='utf-8')
    changed = 0

    # Split by rule blocks to operate on selectors only (before the first '{')
    parts = re.split(r'(\{)', text)
    if len(parts) < 3:
        return 0

    out = []
    i = 0
    while i < len(parts):
        if parts[i] == '{':
            # shouldn't happen
            out.append(parts[i])
            i += 1
            continue

        selector_block = parts[i]
        remainder = ''
        if i + 1 < len(parts) and parts[i+1] == '{':
            remainder = parts[i+1]
            # body will be appended by next iterations

        # find all .pos-CLASS occurrences in this selector block
        pos_classes = set(re.findall(r'\.(pos-[A-Za-z0-9_-]+)', selector_block))
        if pos_classes:
            new_selector_block = selector_block
            for full_name in sorted(pos_classes, key=len, reverse=True):
                cls = full_name[4:]
                tr = f'.tr-{cls}'
                # Only add alias if tr- not already present in this selector block
                if tr not in selector_block:
                    # Insert tr alias before the first occurrence of .pos-CLASS within the selector block
                    # Careful to not break selectors — we insert with a following comma if needed
                    pattern = re.compile(re.escape('.' + full_name))
                    def repl(m):
                        return tr + ', ' + m.group(0)
                    new_selector_block, nsub = pattern.subn(repl, new_selector_block, count=1)
                    if nsub:
                        changed += 1

            out.append(new_selector_block)
        else:
            out.append(selector_block)

        # append the '{' that was split out (if any)
        if i + 1 < len(parts) and parts[i+1] == '{':
            out.append(parts[i+1])
            i += 2
        else:
            i += 1

    new_text = ''.join(out)
    if new_text != text:
        path.write_text(new_text, encoding='utf-8')
    return changed


def main():
    total_changed = 0
    files = list(CSS_DIR.rglob('*.css'))
    for f in files:
        c = process_file(f)
        if c:
            print(f'Updated {f} ({c} insertions)')
            total_changed += c

    print(f'Done. Inserted {total_changed} tr-* aliases in {len(files)} files scanned.')


if __name__ == '__main__':
    main()
