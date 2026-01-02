from pathlib import Path
import re

SRC = Path('public/css/branding.css')
OUT = Path('public/css/branding.tokens-applied.css')
TOKFILE = Path('public/css/branding.tokens.css')
DIFF = Path('public/css/branding.tokens.diff')

s = SRC.read_text(encoding='utf-8')
lines = s.splitlines()
text = s

# parse existing :root vars
root_re = re.compile(r":root\s*\{([\s\S]*?)\}\s*", re.M)
root_match = root_re.search(text)
existing_vars = {}
if root_match:
    body = root_match.group(1)
    for m in re.finditer(r"--([a-zA-Z0-9-]+)\s*:\s*([^;\n]+);", body):
        existing_vars['--'+m.group(1)] = m.group(2).strip()

# list of candidate literal values to tokenise (from earlier pass, but recompute counts)
val_re = re.compile(r':\s*([^;\n]+);')
vals = val_re.findall(text)
cnt = {}
for v in vals:
    v2 = v.strip()
    if 'var(' in v2:
        continue
    v2 = re.sub(r"\s+"," ", v2)
    cnt[v2] = cnt.get(v2,0)+1

# choose values >=3 and that look like colors or lengths or shadows
hex_re = re.compile(r'^#(?:[0-9a-fA-F]{3}|[0-9a-fA-F]{6})$')
rgba_re = re.compile(r'rgba?\([^\)]+\)')
px_re = re.compile(r'^(?:-?\d+(?:\.\d+)?)(?:px|em|rem|%)$')
shadow_re = re.compile(r'rgba?\([^\)]+\)|\dpx')

candidates = [v for v,c in cnt.items() if c>=3 and (hex_re.search(v) or rgba_re.search(v) or re.search(r'\d+px', v))]

# mapping result
mapping = {}
new_vars = {}
space_scale = [4,6,8,10,12,16,24,56]

# helper to pick semantic spacing name
def space_name(px):
    pxv = None
    m = re.match(r'([0-9]+)px', px)
    if m:
        pxv = int(m.group(1))
    if pxv is None:
        return None
    if pxv <=4:
        return '--tr-space-xxs'
    if pxv <=6:
        return '--tr-space-xs'
    if pxv <=8:
        return '--tr-space-sm'
    if pxv <=10:
        return '--tr-space-md'
    if pxv <=12:
        return '--tr-space-lg'
    if pxv <=16:
        return '--tr-space-xl'
    if pxv <=24:
        return '--tr-space-xxl'
    return '--tr-space-xxx'

# prefer existing vars
reverse_vars = {v: k for k,v in existing_vars.items()}

for v in candidates:
    if v in reverse_vars:
        mapping[v] = reverse_vars[v]
        continue
    # color
    if hex_re.search(v) or rgba_re.search(v):
        if v.lower() == '#fff' or v.lower()=='#ffffff':
            mapping[v] = '--tr-branding-color-white'
            continue
        # create color var
        name = '--tr-branding-color-suggest-' + re.sub(r'\W+','',v)[:6]
        # ensure unique
        idx = 1
        base = name
        while name in new_vars:
            idx+=1
            name = base + str(idx)
        new_vars[name] = v
        mapping[v] = name
        continue
    # lengths like '12px' or '0 1px 2px rgba(...)'
    if 'px' in v:
        # if simple px single value
        if re.match(r'^\d+px$', v):
            nm = space_name(v)
            if nm:
                mapping[v] = nm
                if nm not in new_vars and nm not in existing_vars:
                    # set the value
                    new_vars[nm] = v
                continue
        # shadows or complex values
        # handle common shadow: '0 1px 2px rgba(0, 0, 0, 0.05)'
        if re.search(r'rgba?\(', v):
            name = '--tr-shadow-subtle'
            new_vars[name] = v
            mapping[v] = name
            continue
        # general fallback
        name = '--tr-branding-size-suggest-' + re.sub(r'\W+','',v)[:6]
        new_vars[name] = v
        mapping[v] = name

# build token block to insert into :root
token_lines = []
for k,v in {**new_vars}.items():
    token_lines.append(f'    {k}: {v};')

# prepare replaced text: replace occurrences of literal v with var(--name) where appropriate
# avoid replacing inside var( or inside comments
text_out = text
# sort by length desc to avoid partial replacements
for lit, varname in sorted(mapping.items(), key=lambda x: -len(x[0])):
    # replace only in property value positions using regex : <spaces>lit;
    pattern = re.compile(r'(:\s*)' + re.escape(lit) + r'(\s*;)', re.M)
    repl = r"\1var(%s)\2" % varname
    text_out = pattern.sub(repl, text_out)

# insert new vars into :root: place after existing root body or create one if missing
if root_match:
    root_start, root_end = root_match.span(1)
    # insert before the closing brace of :root
    # find position of the closing brace in the match
    mfull = root_match
    insert_pos = mfull.end(1)
    # construct new text
    before = text_out[:insert_pos]
    after = text_out[insert_pos:]
    if token_lines:
        before = before + '\n' + '\n'.join(token_lines)
    text_out = before + after
else:
    # prepend :root
    root_block = ":root {\n" + '\n'.join(token_lines) + '\n}\n\n'
    text_out = root_block + text_out

OUT.write_text(text_out, encoding='utf-8')
TOKFILE.write_text(':root {\n' + '\n'.join(token_lines) + '\n}\n', encoding='utf-8')

# write diff
import difflib
A = text.splitlines()
B = text_out.splitlines()
d = '\n'.join(list(difflib.unified_diff(A,B,fromfile=str(SRC),tofile=str(OUT),lineterm='')))
DIFF.write_text(d, encoding='utf-8')
print('Wrote', OUT)
print('Wrote token file', TOKFILE)
print('Wrote diff', DIFF)
print('Mapping samples:')
for k,v in list(mapping.items())[:20]:
    print(k,'->',v)
