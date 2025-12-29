#!/usr/bin/env python3
import re
from pathlib import Path

root = Path('c:/ci4-local')
md_files = list(root.rglob('*.md'))
link_re = re.compile(r"\[([^\]]+)\]\(([^)]+)\)")

broken = []
trailing = []

for f in md_files:
    rel = f.relative_to(root)
    txt = f.read_text(encoding='utf-8')
    # trailing whitespace
    for i, line in enumerate(txt.splitlines(), start=1):
        if line.endswith(' '):
            trailing.append((str(rel), i))
    # links
    for m in link_re.finditer(txt):
        target = m.group(2).strip()
        if target.startswith('http') or target.startswith('mailto:') or target.startswith('#'):
            continue
        # remove optional title part: path "title"
        if ' ' in target and not target.startswith(' '):
            # handle URLs with spaces unlikely, but trim quotes
            t = target.split(' ')[0]
        else:
            t = target
        # remove fragment
        t = t.split('#')[0]
        # remove query
        t = t.split('?')[0]
        if t == '':
            continue
        target_path = (f.parent / t).resolve()
        if not target_path.exists():
            broken.append((str(rel), m.group(1), t))

print('Markdown files checked:', len(md_files))
if trailing:
    print('\nFiles with trailing whitespace:')
    for f,l in trailing:
        print(f' - {f}: line {l}')
else:
    print('\nNo trailing whitespace found.')

if broken:
    print('\nBroken relative links found:')
    for f, text, t in broken:
        print(f' - {f} -> [{text}]({t})')
    exit(2)
else:
    print('\nNo broken relative links found.')
    exit(0)
