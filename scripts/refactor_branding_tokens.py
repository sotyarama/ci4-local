from pathlib import Path
import re

SRC = Path('public/css/branding.css')
BACKUP = Path('public/css/branding.backup-before-tokens.css')
OUT = Path('public/css/branding.refactored.css')

text = SRC.read_text(encoding='utf-8')
BACKUP.write_text(text, encoding='utf-8')

# token block to insert into the existing :root (at top)
token_block = '''    --tr-space-2: 2px;
    --tr-space-4: 4px;
    --tr-space-6: 6px;
    --tr-space-8: 8px;
    --tr-space-10: 10px;
    --tr-space-12: 12px;
    --tr-space-14: 14px;
    --tr-space-16: 16px;
    --tr-space-18: 18px;
    --tr-space-20: 20px;
    --tr-space-24: 24px;
    --tr-space-28: 28px;
    --tr-space-32: 32px;
    --tr-space-36: 36px;
    --tr-space-48: 48px;
    --tr-space-56: 56px;
    --tr-space-64: 64px;
    --tr-space-72: 72px;
    --tr-space-80: 80px;
    --tr-space-120: 120px;
    --tr-space-160: 160px;
    --tr-space-520: 520px;
    --tr-space-650: 650px;
    --tr-space-720: 720px;

    --tr-radius-6: 6px;
    --tr-radius-8: 8px;
    --tr-radius-10: 10px;
    --tr-radius-12: 12px;
    --tr-radius-14: 14px;
    --tr-radius-16: 16px;
    --tr-radius-18: 18px;
    --tr-radius-pill: 999px;

    --tr-shadow-soft: 0 1px 2px rgba(0, 0, 0, 0.05);
    --tr-shadow-raised: 0 6px 16px rgba(0, 0, 0, 0.06);
    --tr-shadow-swatch: 0 2px 6px rgba(0, 0, 0, 0.08);
'''

# Insert token_block into first :root block just after opening brace
m = re.search(r"^:root\s*\{", text, flags=re.M)
if m:
    # find end of that :root opening line
    insert_pos = m.end()
    # insert with newline
    text = text[:insert_pos] + "\n" + token_block + text[insert_pos:]
else:
    # prepend new :root
    text = ":root {\n" + token_block + "}\n\n" + text

# Now replace px values outside of font-size properties and not inside variable declarations
# Build replacement map of values to vars
replacements = {
    '2px': '--tr-space-2',
    '4px': '--tr-space-4',
    '6px': '--tr-space-6',
    '8px': '--tr-space-8',
    '10px': '--tr-space-10',
    '12px': '--tr-space-12',
    '14px': '--tr-space-14',
    '16px': '--tr-space-16',
    '18px': '--tr-space-18',
    '20px': '--tr-space-20',
    '24px': '--tr-space-24',
    '28px': '--tr-space-28',
    '32px': '--tr-space-32',
    '36px': '--tr-space-36',
    '48px': '--tr-space-48',
    '56px': '--tr-space-56',
    '64px': '--tr-space-64',
    '72px': '--tr-space-72',
    '80px': '--tr-space-80',
    '120px': '--tr-space-120',
    '160px': '--tr-space-160',
    '520px': '--tr-space-520',
    '650px': '--tr-space-650',
    '720px': '--tr-space-720',
    '999px': '--tr-radius-pill'
}

# radius specific map
radius_map = {
    '6px': '--tr-radius-6',
    '8px': '--tr-radius-8',
    '10px': '--tr-radius-10',
    '12px': '--tr-radius-12',
    '14px': '--tr-radius-14',
    '16px': '--tr-radius-16',
    '18px': '--tr-radius-18',
}

# shadow map
shadow_map = {
    '0 1px 2px rgba(0, 0, 0, 0.05)': '--tr-shadow-soft',
    '0 6px 16px rgba(0, 0, 0, 0.06)': '--tr-shadow-raised',
    '0 2px 6px rgba(0, 0, 0, 0.08)': '--tr-shadow-swatch',
}

# function to replace px tokens in property values but skip font-size and skip inside :root and skip inside var( )

def replace_values(css_text):
    # process line by line for simplicity
    out_lines = []
    in_root = False
    for line in css_text.splitlines():
        stripped = line.strip()
        # track if inside :root block - avoid replacing variable definitions' RHS
        if re.match(r"^:root\b", stripped):
            in_root = True
            out_lines.append(line)
            continue
        if in_root and stripped == '}':
            in_root = False
            out_lines.append(line)
            continue
        if in_root:
            out_lines.append(line)
            continue

        # skip replacements in lines that define font-size (property contains 'font-size:')
        if re.search(r"font-size\s*:\s*", line):
            out_lines.append(line)
            continue

        # skip lines that define CSS variables (--something:)
        if re.search(r"^\s*--[a-zA-Z0-9-]+\s*:\s*", line):
            out_lines.append(line)
            continue

        # skip lines containing 'var(' to avoid double-wrapping
        if 'var(' in line:
            out_lines.append(line)
            continue

        # replace full shadow strings first
        for k,v in shadow_map.items():
            if k in line:
                line = line.replace(k, f"var({v})")

        # replace radius occurrences in border-radius or border-radius shorthand
        if 'border-radius' in line or 'border-radius' in stripped or 'border-radius' in line:
            for k,v in radius_map.items():
                # replace only exact tokens
                line = re.sub(r'(?<![\w-])'+re.escape(k)+r'(?![\w-])', f"var({v})", line)
            # also replace 999px with pill
            line = line.replace('999px', 'var(--tr-radius-pill)')
            out_lines.append(line)
            continue

        # general px replacements for the selected set
        # sort by length desc to avoid partial replacements
        for px, var in sorted(replacements.items(), key=lambda x: -len(x[0])):
            # replace only when px appears as whole token (not in comments)
            line = re.sub(r'(?<![\w-])'+re.escape(px)+r'(?![\w-])', f"var({var})", line)

        # convert 0px to 0
        line = re.sub(r'(?<![\w-])0px(?![\w-])', '0', line)

        out_lines.append(line)
    return '\n'.join(out_lines)

new_text = replace_values(text)
OUT.write_text(new_text, encoding='utf-8')
print('Wrote', OUT)
print('Backup at', BACKUP)
