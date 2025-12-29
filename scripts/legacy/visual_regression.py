#!/usr/bin/env python3
"""
Capture screenshots of pages using Playwright and optionally diff against a baseline.

Usage examples:
  python scripts/visual_regression.py --base-url http://localhost:8080 --pages /index.php/pos/touch /index.php/transactions/sales --state state.json --out screenshots/current
  python scripts/visual_regression.py --compare screenshots/baseline --out screenshots/current

Requirements:
  python -m pip install playwright pillow
  python -m playwright install chromium

The script reads Playwright `storageState` from `--state` if provided (JSON from `login_save_state.py`).
"""
import argparse
import os
from pathlib import Path
import sys


def ensure_packages():
    try:
        import playwright  # noqa: F401
    except Exception as e:
        print("Playwright or dependencies are not installed:", e)
        print("Run: python -m pip install playwright pillow && python -m playwright install chromium")
        sys.exit(1)


def slug(path: str) -> str:
    s = path.strip('/').replace('/', '_').replace('?', '_').replace('&', '_')
    return s or 'root'


def capture_pages(base_url, pages, out_dir, state_file=None, headless=True, width=None, height=None, no_state_for_auth=False):
    from playwright.sync_api import sync_playwright
    out_dir = Path(out_dir)
    out_dir.mkdir(parents=True, exist_ok=True)

    storage = None
    if state_file:
        sf = Path(state_file)
        if sf.exists():
            storage = str(sf.resolve())
        else:
            print(f'Warning: state file {state_file} not found — continuing without storage state')

    def is_auth_page(path: str) -> bool:
        p = path.lower()
        # detect by URL segments or by filename references (e.g., app/Views/auth/login.php)
        return (
            ('/login' in p)
            or ('/auth/login' in p)
            or ('/auth/forgot' in p)
            or ('/auth/reset' in p)
            or ('login.php' in p)
            or ('forgot.php' in p)
            or ('reset.php' in p)
        )

    with sync_playwright() as p:
        browser = p.chromium.launch(headless=headless)

        results = []
        for pg in pages:
            # determine per-page context args
            context_kwargs = {}
            use_storage = bool(storage)
            if no_state_for_auth and is_auth_page(pg):
                use_storage = False
            if use_storage:
                context_kwargs['storage_state'] = storage
            if width and height:
                context_kwargs['viewport'] = {'width': int(width), 'height': int(height)}
            # create a fresh context per page to allow different storage usage
            context = browser.new_context(**context_kwargs)
            page = context.new_page()

            url = pg if pg.startswith('http') else base_url.rstrip('/') + '/' + pg.lstrip('/')
            print('Loading', url)
            try:
                page.goto(url, wait_until='networkidle')
            except Exception:
                # fallback to a simple goto
                page.goto(url)

            name = slug(pg)
            out_file = out_dir / f'{name}.png'
            # try to capture main content area if available
            try:
                el = page.query_selector('.pos-money')
                if el:
                    el.screenshot(path=str(out_file))
                else:
                    page.screenshot(path=str(out_file), full_page=True)
            except Exception:
                page.screenshot(path=str(out_file), full_page=True)

            results.append((url, str(out_file)))
            context.close()

        browser.close()

    return results


def diff_images(baseline_dir, current_dir, diff_dir):
    from PIL import Image, ImageChops
    baseline_dir = Path(baseline_dir)
    current_dir = Path(current_dir)
    diff_dir = Path(diff_dir)
    diff_dir.mkdir(parents=True, exist_ok=True)

    diffs = []
    for b in baseline_dir.glob('*.png'):
        c = current_dir / b.name
        if not c.exists():
            print('Missing current for', b.name)
            continue
        im1 = Image.open(b).convert('RGBA')
        im2 = Image.open(c).convert('RGBA')
        if im1.size != im2.size:
            print('Size mismatch for', b.name)
            continue
        d = ImageChops.difference(im1, im2)
        bbox = d.getbbox()
        out = diff_dir / b.name
        if bbox:
            d.save(out)
            diffs.append(str(out))
            print('Diff saved', out)
        else:
            print('No visual diff for', b.name)

    return diffs


def main():
    ensure_packages()

    ap = argparse.ArgumentParser()
    ap.add_argument('--base-url', default='http://localhost:8080')
    ap.add_argument('--pages', nargs='*', default=['/index.php/pos/touch'])
    ap.add_argument('--state', default='state.json')
    ap.add_argument('--out', default='screenshots/current')
    ap.add_argument('--compare', help='Path to baseline screenshots to diff against')
    ap.add_argument('--width', type=int, help='Viewport width to use for capture (px)')
    ap.add_argument('--height', type=int, help='Viewport height to use for capture (px)')
    ap.add_argument('--headless', action='store_true')
    ap.add_argument('--no-state-for-auth', action='store_true', default=True,
                    help='Do not use storage state for auth pages (login/forgot/reset)')
    args = ap.parse_args()

    print('Capturing pages to', args.out)
    res = capture_pages(
        args.base_url,
        args.pages,
        args.out,
        state_file=args.state,
        headless=args.headless,
        width=args.width,
        height=args.height,
        no_state_for_auth=args.no_state_for_auth,
    )
    for url, file in res:
        print('Captured', url, '->', file)

    if args.compare:
        diff_dir = Path(args.out).parent / 'diffs'
        diffs = diff_images(args.compare, args.out, diff_dir)
        if diffs:
            print('Diffs written to', diff_dir)
        else:
            print('No diffs found')


if __name__ == '__main__':
    main()
