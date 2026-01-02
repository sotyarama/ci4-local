from pathlib import Path
import re
from collections import Counter

p = Path('public/css/branding.css')
s = p.read_text(encoding='utf-8')

# capture property values (naively): match ':' then capture until ';'
val_re = re.compile(r':\s*([^;\n]+);')
vals = val_re.findall(s)

# normalize: strip, collapse spaces
norm_vals = []
for v in vals:
    v2 = v.strip()
    # skip variables
    if 'var(' in v2:
        continue
    # remove surrounding calc(...) whitespace
    v2 = re.sub(r'\s+', ' ', v2)
    norm_vals.append(v2)

cnt = Counter(norm_vals)

# categorize values
hex_re = re.compile(r'#(?:[0-9a-fA-F]{3}|[0-9a-fA-F]{6})\b')
px_re = re.compile(r'\b\d+px\b')
percent_re = re.compile(r'\b\d+%\b')
rgba_re = re.compile(r'rgba?\([^\)]+\)')

categories = {'colors': Counter(), 'lengths': Counter(), 'others': Counter()}
for v,c in cnt.items():
    if hex_re.search(v) or rgba_re.search(v) or v.lower() in ('transparent','inherit'):
        categories['colors'][v] = c
    elif px_re.search(v) or percent_re.search(v) or re.search(r'\b\d+(?:\.\d+)?\b', v) and ('px' in v or '%' in v or 'em' in v or 'rem' in v):
        categories['lengths'][v] = c
    else:
        categories['others'][v] = c

# prepare suggestions: values used >= 3 times
suggestions = []
for cat in ['colors','lengths','others']:
    for v,c in categories[cat].most_common():
        if c >= 3:
            suggestions.append((v,c,cat))

out = Path('public/css/branding.tokens-suggestion.txt')
with out.open('w', encoding='utf-8') as f:
    f.write('Common literal CSS values in public/css/branding.css\n')
    f.write('Generated suggestions for tokens (values appearing >=3 times)\n\n')
    f.write('Top repeated values (all categories)\n')
    for v,c in cnt.most_common(50):
        if c>1:
            f.write(f"{c}x: {v}\n")
    f.write('\nSuggested tokens:\n')
    if not suggestions:
        f.write('None (no literal value appears 3+ times excluding vars).\n')
    else:
        for idx,(v,c,cat) in enumerate(suggestions, start=1):
            tok = None
            if cat=='colors':
                tok = f'--tr-branding-color-suggest-{idx}'
            elif cat=='lengths':
                tok = f'--tr-branding-size-suggest-{idx}'
            else:
                tok = f'--tr-branding-token-suggest-{idx}'
            f.write(f"{tok}: {v}  /* used {c} times, category: {cat} */\n")

print('Wrote suggestions to', out)
