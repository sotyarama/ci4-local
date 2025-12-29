#!/usr/bin/env python3
from playwright.sync_api import sync_playwright
from pathlib import Path
import argparse

ap = argparse.ArgumentParser()
ap.add_argument('--base', default='http://localhost:8080')
ap.add_argument('--page', default='/index.php/pos/touch')
ap.add_argument('--out', default='screenshots/baseline')
ap.add_argument('--state', default='state.json')
ap.add_argument('--width', type=int, default=1920)
ap.add_argument('--height', type=int, default=1200)
ap.add_argument('--headful', action='store_true', help='Run browser in headed mode to match real display')
args = ap.parse_args()

BASE = args.base
PAGE = args.page
OUT = Path(args.out)
OUT.mkdir(parents=True, exist_ok=True)
STATE = Path(args.state)

with sync_playwright() as p:
    # launch with window args to match user's display; prefer headed mode for exact window sizing
    launch_args = [f"--window-size={args.width},{args.height}", '--window-position=0,0', '--start-maximized']
    # force device scale factor to 1 to avoid DPI scaling differences
    launch_args.append('--force-device-scale-factor=1')

    browser = p.chromium.launch(headless=not args.headful, args=launch_args)

    context_kwargs = {}
    if STATE.exists():
        context_kwargs['storage_state'] = str(STATE.resolve())

    # set viewport and device scale factor to align with the launched window
    context_kwargs['viewport'] = {'width': args.width, 'height': args.height}
    context_kwargs['device_scale_factor'] = 1

    context = browser.new_context(**context_kwargs)
    page = context.new_page()
    url = BASE.rstrip('/') + '/' + PAGE.lstrip('/')
    print('Loading', url)
    page.goto(url, wait_until='networkidle')

    out_file = OUT / (PAGE.strip('/').replace('/', '_') + '.png')
    # capture full page
    page.screenshot(path=str(out_file), full_page=True)
    print('Saved', out_file)
    context.close()
    browser.close()
