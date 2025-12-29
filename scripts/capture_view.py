#!/usr/bin/env python3
"""
Capture views by filename or by Views subfolder and produce full-page PNGs.

Features:
- Capture a single view: `--view login.php` (heuristic mapping to public route)
- Capture all views under a Views subfolder: `--folder auth` (captures all .php under app/Views/auth)
- Skips `state.json` for auth views (login/forgot/reset) so captures are unauthenticated
- Special-case mapping for known mismatches (e.g., touchscreen)

Usage:
  python scripts/capture_view.py --view login.php --out screenshots/trial
  python scripts/capture_view.py --folder auth --out screenshots/baseline

Requires: playwright
"""
from pathlib import Path
import argparse
import sys


def find_view(view_name: str, views_root: Path):
    return list(views_root.rglob(view_name))


def slug(path: str) -> str:
    s = path.strip('/').replace('/', '_').replace('?', '_').replace('&', '_')
    return s or 'root'


def main():
    ap = argparse.ArgumentParser()
    ap.add_argument('--view', help='View filename to find under app/Views (e.g. login.php)')
    ap.add_argument('--folder', help='Folder under app/Views to capture all views (e.g. auth or pos)')
    ap.add_argument('--page', help='Full page path or URL to capture (overrides auto mapping)')
    ap.add_argument('--base', default='http://localhost:8080')
    ap.add_argument('--out', default='screenshots/baseline')
    ap.add_argument('--state', default='state.json')
    ap.add_argument('--width', type=int, default=1920)
    ap.add_argument('--height', type=int, default=1200)
    ap.add_argument('--headful', action='store_true')
    args = ap.parse_args()

    views_root = Path('app/Views')
    if not views_root.exists():
        print('Views folder not found at app/Views — cannot continue')
        sys.exit(2)

    targets = []
    if args.folder:
        f = Path(args.folder)
        if not f.is_absolute() and not str(f).startswith('app/Views') and not str(f).startswith('Views'):
            f = views_root / f
        if not f.exists():
            print(f'Folder {f} not found — aborting')
            sys.exit(2)
        for p in f.rglob('*.php'):
            targets.append(p)
    elif args.view:
        matches = find_view(args.view, views_root)
        if not matches:
            print(f'View "{args.view}" not found under app/Views — aborting')
            sys.exit(2)
        if len(matches) > 1:
            print('Multiple matches found; using the first one. Matches:')
            for m in matches:
                print(' -', m)
        targets.append(matches[0])
    else:
        ap.print_help()
        sys.exit(1)

    # special-case mappings for views whose public route differs from view path
    special_map = {
        'auth/login': '/index.php/login',
        'auth/forgot': '/index.php/auth/forgot',
        'auth/reset': '/index.php/auth/reset',
        # touchscreen view file is named touchscreen.php but route is /pos/touch
        'pos/touchscreen': '/index.php/pos/touch',
        # Master menu options view maps to 'menu-options' route
        'master/options': '/index.php/master/menu-options',
    }

    def rel_to_page(rel_path: Path) -> str:
        key = str(rel_path).replace('\\', '/')
        # normalize first path segment (folder) to lowercase to match public routes
        parts = key.split('/')
        if parts:
            parts[0] = parts[0].lower()

        # heuristic mapping for common view filename patterns
        # e.g., products_index.php -> /index.php/master/products
        #       products_form.php -> /index.php/master/products/create
        filename = parts[-1]
        folder = parts[0] if parts else ''
        if filename.endswith('_index'):
            resource = filename[: -len('_index')]
            # strip common prefixes like 'menu_' to match public routes (menu_categories -> categories)
            if resource.startswith('menu_'):
                resource = resource[len('menu_'):]
            # convert underscore_case to dash-case for public routes
            resource = resource.replace('_', '-')
            page = f'/index.php/{folder}/{resource}'
            return special_map.get(f'{folder}/{resource}', page)
        if filename.endswith('_form'):
            resource = filename[: -len('_form')]
            if resource.startswith('menu_'):
                resource = resource[len('menu_'):]
            resource = resource.replace('_', '-')
            page = f'/index.php/{folder}/{resource}/create'
            return special_map.get(f'{folder}/{resource}', page)

        key_norm = '/'.join(parts)
        # normalize underscores to hyphens so view filenames like raw_materials -> raw-materials
        key_norm = key_norm.replace('_', '-')
        rel_url = '/' + key_norm.lstrip('/')
        return special_map.get(key_norm, '/index.php' + rel_url)

    out_dir = Path(args.out)
    out_dir.mkdir(parents=True, exist_ok=True)

    try:
        from playwright.sync_api import sync_playwright
    except Exception as e:
        print('Playwright not installed or import failed:', e)
        print('Install with: python -m pip install playwright && python -m playwright install chromium')
        sys.exit(1)

    st = Path(args.state)

    def is_auth_rel(rel: Path) -> bool:
        s = str(rel).lower()
        if 'auth' in s:
            return True
        return any(x in s for x in ('login', 'forgot', 'reset'))

    pages_to_capture = []
    for p in targets:
        rel = p.relative_to(views_root).with_suffix('')
        page_url = args.page or rel_to_page(rel)
        pages_to_capture.append((p, rel, page_url))

    print('Capturing', len(pages_to_capture), 'views to', out_dir)

    with sync_playwright() as p:
        launch_args = []
        if args.headful:
            launch_args = [f"--window-size={args.width},{args.height}", '--window-position=0,0', '--start-maximized', '--force-device-scale-factor=1']

        browser = p.chromium.launch(headless=not args.headful, args=launch_args)

        for view_path, rel, page_url in pages_to_capture:
            print('Processing view:', view_path)
            context_kwargs = {}
            if st.exists() and not is_auth_rel(rel):
                context_kwargs['storage_state'] = str(st.resolve())
                print('Applying storage_state from', str(st.resolve()))
            if not args.headful:
                context_kwargs['viewport'] = {'width': args.width, 'height': args.height}
                context_kwargs['device_scale_factor'] = 1

            context = browser.new_context(**context_kwargs)
            page_obj = context.new_page()

            url = page_url if page_url.startswith('http') else args.base.rstrip('/') + '/' + page_url.lstrip('/')
            print('Loading', url)
            resp = None
            try:
                resp = page_obj.goto(url, wait_until='networkidle')
            except Exception:
                try:
                    resp = page_obj.goto(url)
                except Exception as e:
                    print('Navigation failed for', url, e)
                    context.close()
                    continue

            # debug: print final URL and status to detect redirects to login
            try:
                status = resp.status if resp else None
                print('Navigation status:', status, 'final URL:', page_obj.url)
                if status == 404:
                    print('Page returned 404 for', url)
                    context.close()
                    continue
            except Exception:
                pass

            out_file = out_dir / (slug(page_url) + '.png')
            try:
                page_obj.screenshot(path=str(out_file), full_page=True)
                print('Saved', out_file)
            except Exception as e:
                print('Screenshot failed for', url, e)

            context.close()

        browser.close()


if __name__ == '__main__':
    main()

