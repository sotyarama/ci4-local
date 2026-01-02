from pathlib import Path
import re

SRC = Path('public/css/branding.scoped.css')
OUT = Path('public/css/branding.scoped.fixed.css')
text = SRC.read_text(encoding='utf-8')

# Remove entire :root block
text_no_root = re.sub(r"^:root\s*\{[\s\S]*?\n\}\n\n", "", text, flags=re.M)

# Extract .tr-branding-page block
m = re.search(r'(\.tr-branding-page\s*\{)([\s\S]*?)(\n\})', text_no_root)
if not m:
    raise SystemExit('.tr-branding-page block not found')
block_start = m.start(2)
block_end = m.end(2)
body = m.group(2)
pre = text_no_root[:m.start(1)]
post = text_no_root[m.end(3):]

# Find variable definitions in body (lines starting with --tr-branding-...)
var_defs = {}
other_lines = []
for line in body.splitlines():
    mm = re.match(r"\s*(--tr-branding-[a-z0-9-]+)\s*:\s*([^;]+);\s*$", line)
    if mm:
        name = mm.group(1)
        val = mm.group(2).strip()
        var_defs[name] = val
    else:
        other_lines.append(line)

# Count occurrences of var(--tr-branding-...) across entire file (excluding definitions)
text_no_defs = pre + m.group(1) + '\n' + '\n'.join(other_lines) + '\n}\n' + post
ref_counts = {}
for name in list(var_defs.keys()):
    pattern = re.escape(f"var({name})")
    c = len(re.findall(pattern, text_no_defs))
    ref_counts[name] = c

# Ensure --tr-branding-max-width moved into block even if not in var_defs
# Find if it existed in original root or elsewhere
maxwidth_val = None
# search original text for --tr-branding-max-width
m2 = re.search(r'--tr-branding-max-width\s*:\s*([^;\n]+);', text)
if m2:
    maxwidth_val = m2.group(1).strip()

# Decide which tokens to keep: only those with ref_counts >=2, plus max-width always
keep = {k:v for k,v in var_defs.items() if ref_counts.get(k,0) >= 2}
if maxwidth_val:
    keep['--tr-branding-max-width'] = maxwidth_val

# Build token block in desired order: spacing (space tokens start with --tr-branding-space-), radius, shadow, max-width
space_tokens = {k:v for k,v in keep.items() if k.startswith('--tr-branding-space-')}
radius_tokens = {k:v for k,v in keep.items() if k.startswith('--tr-branding-radius-')}
shadow_tokens = {k:v for k,v in keep.items() if k.startswith('--tr-branding-shadow-')}
other_tokens = {k:v for k,v in keep.items() if k not in space_tokens and k not in radius_tokens and k not in shadow_tokens}

# token lines
token_lines = []
for k in sorted(space_tokens.keys(), key=lambda x:int(re.search(r"(\d+)", x).group(1)) if re.search(r"(\d+)", x) else x):
    token_lines.append(f'    {k}: {space_tokens[k]};')
for k in sorted(radius_tokens.keys(), key=lambda x:int(re.search(r"(\d+)", x).group(1)) if re.search(r"(\d+)", x) else x):
    token_lines.append(f'    {k}: {radius_tokens[k]};')
for k in sorted(shadow_tokens.keys()):
    token_lines.append(f'    {k}: {shadow_tokens[k]};')
for k in sorted(other_tokens.keys()):
    # put max-width at end if present
    if k == '--tr-branding-max-width':
        token_lines.append(f'    {k}: {other_tokens[k]};')

# For tokens not kept, replace var(name) with literal value (their definition)
fixed_text = text_no_root
for name,val in var_defs.items():
    if name not in keep:
        # replace var(name) occurrences with literal value
        fixed_text = re.sub(re.escape(f'var({name})'), val, fixed_text)
        # also remove any remaining definition lines inside .tr-branding-page body
        fixed_text = re.sub(r'\n\s*'+re.escape(name)+r'\s*:\s*[^;]+;','', fixed_text)

# Now rebuild the .tr-branding-page block: insert token_lines at top of block
# Find the start of the block again
m3 = re.search(r'(\.tr-branding-page\s*\{)([\s\S]*?)(\n\})', fixed_text)
if not m3:
    raise SystemExit('branding page block missing after replacements')
start = m3.start(2)
end = m3.end(2)
body2 = m3.group(2)
# remove any of the kept token definitions from existing body to avoid duplicates
for k in list(keep.keys()):
    body2 = re.sub(r'\n\s*'+re.escape(k)+r'\s*:\s*[^;]+;','', body2)
# trim leading newlines
new_body = '\n'.join(token_lines) + '\n' + body2.strip('\n') + '\n'
new_text = fixed_text[:m3.start(1)] + '.tr-branding-page {' + new_body + '\n}\n' + fixed_text[m3.end(3):]

OUT.write_text(new_text, encoding='utf-8')
print('Wrote', OUT)
print('Kept tokens:', list(keep.keys()))
print('Removed tokens:', [k for k in var_defs.keys() if k not in keep])
