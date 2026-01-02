from pathlib import Path
import difflib

A = Path('public/css/branding.css').read_text(encoding='utf-8').splitlines()
B = Path('public/css/branding.consolidated.css').read_text(encoding='utf-8').splitlines()

d = list(difflib.unified_diff(A, B, fromfile='public/css/branding.css', tofile='public/css/branding.consolidated.css', lineterm=''))

out = Path('public/css/branding.diff')
out.write_text('\n'.join(d), encoding='utf-8')

MAX = 400
if not d:
    print('No differences found.')
else:
    if len(d) <= MAX:
        print('\n'.join(d))
    else:
        print('\n'.join(d[:MAX]))
        print('\n... (diff truncated)')
        print(f'Full diff written to {out} (length {len(d)} lines)')
