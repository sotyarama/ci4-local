"""
Consolidate @media blocks by condition in public/css/branding.css
Writes output to public/css/branding.consolidated.css

Usage: python scripts/consolidate_media.py
"""
from pathlib import Path
import re

SRC = Path('public/css/branding.css')
OUT = Path('public/css/branding.consolidated.css')

text = SRC.read_text(encoding='utf-8')
lines = text.splitlines()

media_blocks = {}  # condition -> list of lines
media_order = []
base_lines = []

i = 0
n = len(lines)

while i < n:
    line = lines[i]
    m = re.match(r"\s*@media\s+([^\{]+)\{\s*$", line)
    if m:
        cond = m.group(1).strip()
        # capture the whole block, including nested braces
        block_lines = [line]
        i += 1
        depth = 1
        while i < n and depth > 0:
            l = lines[i]
            # count braces
            depth += l.count('{')
            depth -= l.count('}')
            block_lines.append(l)
            i += 1
        # store inner content (strip outer @media { and final })
        # remove first line and last line if they are the matching braces
        inner = block_lines[1:-1]
        if cond not in media_blocks:
            media_blocks[cond] = []
            media_order.append(cond)
        media_blocks[cond].extend(inner)
        continue
    else:
        base_lines.append(line)
        i += 1

# Build output: base_lines (without the original @media blocks) then consolidated media blocks
out_lines = []
out_lines.extend(base_lines)
out_lines.append('')
for cond in media_order:
    out_lines.append(f"@media {cond} {{")
    out_lines.extend(media_blocks[cond])
    out_lines.append('}')
    out_lines.append('')

OUT.write_text('\n'.join(out_lines), encoding='utf-8')
print(f'Wrote {OUT} (base + consolidated @media for {len(media_order)} conditions)')
