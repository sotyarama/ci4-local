from pathlib import Path
import re

SRC = Path('public/css/branding.css')
OUT = Path('public/css/branding.consolidated2.css')
BACK = Path('public/css/branding.backup.css')

text = SRC.read_text(encoding='utf-8')
lines = text.splitlines()

media_start_re = re.compile(r"^\s*@media[^{]*\{")
selector_re = re.compile(r"^\s*([^@\s][^\{]+?)\s*\{")
prop_re = re.compile(r"^\s*([\w-]+)\s*:\s*([^;]+);\s*$")

# Parse into blocks: each block is dict {type:'media'|'rule'|'other', start, end, lines, selector, body_lines}
blocks = []

i = 0
n = len(lines)
while i < n:
    line = lines[i]
    if media_start_re.match(line):
        # capture media block fully
        start = i
        depth = 0
        block_lines = []
        while i < n:
            l = lines[i]
            block_lines.append(l)
            depth += l.count('{')
            depth -= l.count('}')
            i += 1
            if depth == 0:
                break
        blocks.append({'type':'media','lines':block_lines,'start':start+1,'end':i})
        continue
    m = selector_re.match(line)
    if m:
        sel = re.sub(r"\s+"," ", m.group(1).strip())
        start = i
        block_lines = [line]
        i += 1
        depth = 1
        while i < n and depth > 0:
            l = lines[i]
            block_lines.append(l)
            depth += l.count('{')
            depth -= l.count('}')
            i += 1
        blocks.append({'type':'rule','selector':sel,'lines':block_lines,'start':start+1,'end':i})
        continue
    # other single line
    blocks.append({'type':'other','lines':[line],'start':i+1,'end':i+1})
    i += 1

# Collect top-level rule blocks by selector (exclude media blocks)
selector_blocks = {}
for idx, b in enumerate(blocks):
    if b['type']=='rule':
        selector_blocks.setdefault(b['selector'], []).append((idx,b))

merged_selectors = []
# For each selector with >1 top-level block, merge properties safely
for sel, lst in selector_blocks.items():
    if len(lst) <= 1:
        continue
    # accumulate properties in order of appearance; track last occurrence index
    prop_order = []
    prop_last = {}  # prop -> (global_index, value)
    global_idx = 0
    for (blk_idx, b) in lst:
        # extract body lines between first '{' and last '}'
        body_lines = b['lines'][1:-1]
        for line in body_lines:
            m = prop_re.match(line)
            if m:
                prop = m.group(1).strip()
                val = m.group(2).strip()
                global_idx += 1
                prop_last[prop] = (global_idx, val)
    if not prop_last:
        continue
    # sort props by their last occurrence order
    sorted_props = sorted(prop_last.items(), key=lambda kv: kv[1][0])
    # build merged block lines
    merged_lines = [f"{sel} {{"]
    for prop, (idx_order, val) in sorted_props:
        merged_lines.append(f"    {prop}: {val};")
    merged_lines.append('}')
    # place merged block at position of first occurrence and mark others for removal
    first_idx = lst[0][0]
    blocks[first_idx]['merged_lines'] = merged_lines
    # mark others
    for (blk_idx,b) in lst[1:]:
        blocks[blk_idx]['remove'] = True
    merged_selectors.append(sel)

# Build output lines
out_lines = []
for b in blocks:
    if b.get('remove'):
        continue
    if b['type']=='rule' and 'merged_lines' in b:
        out_lines.extend(b['merged_lines'])
    else:
        out_lines.extend(b['lines'])

# backup original
BACK.write_text(text, encoding='utf-8')
OUT.write_text('\n'.join(out_lines)+('\n' if out_lines and not out_lines[-1].endswith('\n') else ''), encoding='utf-8')
print(f'Wrote {OUT}; backed up original to {BACK}')
print(f'Merged selectors: {len(merged_selectors)}')
for s in merged_selectors:
    print('-', s)
