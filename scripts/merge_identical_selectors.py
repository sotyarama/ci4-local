from pathlib import Path
import re

SRC = Path('public/css/branding.css')
OUT = Path('public/css/branding.merged-identical.css')
text = SRC.read_text(encoding='utf-8')
lines = text.splitlines()

selector_re = re.compile(r"^\s*([^@\s][^\{]+?)\s*\{")
media_start_re = re.compile(r"^\s*@media[^{]*\{")

out = []
seen = {}
removed = []

i = 0
n = len(lines)
while i < n:
    line = lines[i]
    # media block: copy as-is
    if media_start_re.match(line):
        block = [line]
        i += 1
        depth = 1
        while i < n and depth > 0:
            l = lines[i]
            depth += l.count('{')
            depth -= l.count('}')
            block.append(l)
            i += 1
        out.extend(block)
        continue
    m = selector_re.match(line)
    if m:
        sel = re.sub(r"\s+"," ", m.group(1).strip())
        # capture full selector block
        block = [line]
        i += 1
        depth = 1
        while i < n and depth > 0:
            l = lines[i]
            depth += l.count('{')
            depth -= l.count('}')
            block.append(l)
            i += 1
        # normalize block body (inside braces)
        body = '\n'.join(block[1:-1]).strip()
        norm = re.sub(r"\s+"," ", body)
        if sel in seen and seen[sel] == norm:
            removed.append(sel)
            # skip adding (duplicate with identical body)
            continue
        else:
            seen[sel] = norm
            out.extend(block)
        continue
    # otherwise, copy line
    out.append(line)
    i += 1

OUT.write_text('\n'.join(out)+('\n' if out and not out[-1].endswith('\n') else ''), encoding='utf-8')
print(f'Wrote {OUT} (removed {len(removed)} duplicate blocks)')
if removed:
    print('Removed selectors:')
    for s in sorted(set(removed)):
        print('-', s)
