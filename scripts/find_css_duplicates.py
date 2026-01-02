from pathlib import Path
import re

p = Path('public/css/branding.css')
s = p.read_text(encoding='utf-8')
lines = s.splitlines()

media_depth = 0
selector_re = re.compile(r"^\s*([^@\s][^\{]+?)\s*\{")
occ = {}
for i, line in enumerate(lines, start=1):
    # adjust media depth
    if '@media' in line and '{' in line:
        media_depth += 1
    # count brace changes on this line (but avoid counting braces inside strings - acceptable approximation)
    opens = line.count('{')
    closes = line.count('}')
    # But we've already counted one for @media line; adjusts handled collectively
    # Find selectors
    m = selector_re.match(line)
    if m:
        sel = m.group(1).strip()
        # normalize whitespace
        sel_norm = re.sub(r"\s+"," ", sel)
        occ.setdefault(sel_norm, []).append((i, media_depth>0))
    # update media_depth using braces: decrement/increment based on braces excluding the @media detection already incremented
    # A simpler approach: track nesting stack with tokens
    # For now, adjust media_depth by counting closes following opens
    if opens>0 or closes>0:
        # naive update: when line contains '@media' with '{', we already counted that open; for other opens, if not @media, we don't change media flag
        # So only decrease media_depth on closes
        if closes>0:
            media_depth -= closes
            if media_depth < 0:
                media_depth = 0

# Now filter duplicates where at least two occurrences are outside media (media_flag==False)
dups = {}
for sel, lst in occ.items():
    outside = [ln for ln, in_media in lst if not in_media]
    if len(outside) > 1:
        dups[sel] = outside

if not dups:
    print('No duplicate selectors found outside @media blocks.')
else:
    for sel, lines in sorted(dups.items()):
        print(f"{sel} -> lines: {', '.join(map(str, lines))}")

# Also report selectors that appear both outside and inside media (base + media)
both = {}
for sel, lst in occ.items():
    has_out = any(not in_media for _, in_media in lst)
    has_in = any(in_media for _, in_media in lst)
    if has_out and has_in:
        both[sel] = ([ln for ln, in_media in lst if not in_media], [ln for ln, in_media in lst if in_media])

if both:
    print('\nSelectors defined both outside and inside @media:')
    for sel, (outs, ins) in sorted(both.items()):
        print(f"{sel} -> outside: {', '.join(map(str, outs))} ; inside: {', '.join(map(str, ins))}")
