#!/usr/bin/env python3
from pathlib import Path
import argparse
from playwright.sync_api import sync_playwright

ap = argparse.ArgumentParser()
ap.add_argument('--base', default='http://localhost:8080')
ap.add_argument('--page', default='/index.php/login', help='login page path or full URL')
ap.add_argument('--out', default='state.json')
ap.add_argument('--width', type=int, default=1280)
ap.add_argument('--height', type=int, default=800)
args = ap.parse_args()

out = Path(args.out)
with sync_playwright() as p:
    browser = p.chromium.launch(headless=False, args=[f'--window-size={args.width},{args.height}'])
    context = browser.new_context(viewport={'width': args.width, 'height': args.height})
    page = context.new_page()
    page_url = args.page if args.page.startswith('http') else args.base.rstrip('/') + '/' + args.page.lstrip('/')
    print('Opening', page_url)
    page.goto(page_url)
    print('Please sign in in the opened browser window. When finished, return here and press ENTER to save storage state to', str(out))
    input()
    context.storage_state(path=str(out))
    print('Saved storage state to', str(out))
    context.close()
    browser.close()
