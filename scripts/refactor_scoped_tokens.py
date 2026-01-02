from pathlib import Path
import re

SRC = Path('public/css/branding.css')
OUT = Path('public/css/branding.scoped.css')
BACKUP = Path('public/css/branding.backup-before-scoped.css')

text = SRC.read_text(encoding='utf-8')
BACKUP.write_text(text, encoding='utf-8')

# find all literal values (px and shadow rgba and 0px)
# we'll ignore font-size properties and existing var() occurrences

# extract lines
lines = text.splitlines()

value_counts = {}

in_root = False
in_branding_page = False

for i,line in enumerate(lines):
    s=line
    if re.match(r"^\s*:root\b", s):
        in_root = True
    if in_root and '}' in s:
        # naive end
        in_root = False
    # detect branding page block start
    if re.match(r"^\s*\.tr-branding-page\b", s):
        if '{' in s:
            in_branding_page = True
    if in_branding_page and '}' in s:
        # don't toggle off here; safer to detect end by counting braces later
        pass

# Simpler: count values across whole file, but ignore lines with 'font-size' or containing 'var(' or within :root
text_no_root = re.sub(r':root\s*\{[\s\S]*?\}', '', text, flags=re.M)
for m in re.findall(r'([0-9]+px)', text_no_root):
    # skip font-size occurrences: find surrounding context
    # find occurrences positions
    value_counts[m] = value_counts.get(m,0)+1

# Count shadows exact matches
shadow_patterns = re.findall(r'(0\s+1px\s+2px\s+rgba\([^\)]+\))', text)
for sh in shadow_patterns:
    value_counts[sh] = value_counts.get(sh,0)+1

# Decide which px values to tokenise: those with count>=2
candidates = {v:c for v,c in value_counts.items() if c>=2}

# Separate px numbers and shadows
px_candidates = {k:v for k,v in candidates.items() if k.endswith('px')}
shadow_candidates = {k:v for k,v in candidates.items() if not k.endswith('px')}

# Map px to token names --tr-branding-space-N or radius if appears in border-radius contexts
# Find radius usages: values used in 'border-radius' contexts
radius_values = set()
for m in re.finditer(r'border-radius\s*:\s*([^;\n]+);', text):
    vals = m.group(1)
    for v in re.findall(r'([0-9]+px)', vals):
        radius_values.add(v)

# Build token maps
space_map = {}
radius_map = {}
shadow_map = {}

for v in sorted(px_candidates.keys(), key=lambda x: int(re.match(r'(\d+)', x).group(1)) ): 
    if v in radius_values:
        m = re.match(r"(\d+)", v)
        if m:
            name = f'--tr-branding-radius-{m.group(1)}'
            radius_map[v]=name
    else:
        m = re.match(r"(\d+)", v)
        if m:
            name = f'--tr-branding-space-{m.group(1)}'
            space_map[v]=name

for sh in shadow_candidates:
    name = '--tr-branding-shadow-' + str(abs(hash(sh)))[:6]
    shadow_map[sh]=name

# Now perform replacements but strictly:
# - only replace occurrences outside :root
# - do not replace font-size lines
# - do not create tokens for values with count<2

# remove :root block to avoid touching it; we will reinsert scoped tokens into .tr-branding-page
root_block_match = re.search(r'(:root\s*\{[\s\S]*?\})', text, flags=re.M)
root_block = root_block_match.group(1) if root_block_match else ''
text_wo_root = re.sub(r':root\s*\{[\s\S]*?\}','', text, flags=re.M)

# function to replace tokens in a line if allowed
def replace_line(line):
    # Skip actual font-size property lines and branding font-size variable declarations
    if 'font-size' in line:
        return line
    if 'var(' in line:
        return line
    # Skip branding font/size/color/logo variable declarations to avoid changing typography tokens
    if re.search(r"^\s*--tr-branding-(fs|font|color|logo)-", line):
        return line
    # replace shadows first
    for k,v in shadow_map.items():
        if k in line:
            line = line.replace(k, f'var({v})')
    # replace radii
    for k,v in radius_map.items():
        # only replace in border-radius or border-radius shorthand contexts or when used as single value for radius
        if 'border-radius' in line or 'border-radius' in line or 'border' in line or 'radius' in line:
            line = re.sub(r'(?<![\w-])'+re.escape(k)+r'(?![\w-])', f'var({v})', line)
    # replace spaces
    for k,v in space_map.items():
        line = re.sub(r'(?<![\w-])'+re.escape(k)+r'(?![\w-])', f'var({v})', line)
    # convert 0px to 0
    line = re.sub(r'(?<![\w-])0px(?![\w-])', '0', line)
    return line

# Apply replacements line by line, skipping :root
out_lines = []
inside_root = False
for line in text.splitlines():
    if re.match(r'\s*:root\b', line):
        inside_root = True
        out_lines.append(line)
        continue
    if inside_root:
        if '}' in line:
            inside_root = False
        out_lines.append(line)
        continue
    out_lines.append(replace_line(line))

out_text = '\n'.join(out_lines)

# Insert scoped tokens into .tr-branding-page block after its opening brace and after existing branding vars
# Find .tr-branding-page block
m = re.search(r'(\.tr-branding-page\s*\{)([\s\S]*?)(\n\})', out_text)
if not m:
    # more robust: find start and insert after first '{'
    m2 = re.search(r'(\.tr-branding-page\s*\{)', out_text)
    if m2:
        insert_pos = m2.end()
        # prepare token text
        token_lines = []
        for k,v in space_map.items():
            token_lines.append(f'    {v}: {k};')
        for k,v in radius_map.items():
            token_lines.append(f'    {v}: {k};')
        for k,v in shadow_map.items():
            token_lines.append(f'    {v}: {k};')
        insert_block = '\n' + '\n'.join(token_lines) + '\n'
        out_text = out_text[:insert_pos] + insert_block + out_text[insert_pos:]
else:
    start = m.start(2)
    end = m.end(2)
    existing_body = m.group(2)
    # prepare token lines but ensure we don't duplicate existing branding vars
    token_lines = []
    for k,v in space_map.items():
        token_lines.append(f'    {v}: {k};')
    for k,v in radius_map.items():
        token_lines.append(f'    {v}: {k};')
    for k,v in shadow_map.items():
        token_lines.append(f'    {v}: {k};')
    new_body = existing_body + '\n' + '\n'.join(token_lines) + '\n'
    out_text = out_text[:start] + new_body + out_text[end:]

    OUT.write_text(out_text, encoding='utf-8')
print('Wrote', OUT)
print('Backup at', BACKUP)
print('Space tokens:', space_map)
print('Radius tokens:', radius_map)
print('Shadow tokens:', shadow_map)
