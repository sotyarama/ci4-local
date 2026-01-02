"""
Find and print unique class names in the branding page that contain 'tr-'.

Usage:
  python scripts/find_tr_classes.py --url http://localhost:8080/branding
"""
from __future__ import annotations

import argparse
from playwright.sync_api import sync_playwright


def parse_args():
    p = argparse.ArgumentParser()
    p.add_argument("--url", required=True)
    return p.parse_args()


def main():
    args = parse_args()
    with sync_playwright() as pw:
        browser = pw.chromium.launch(headless=True)
        ctx = browser.new_context(viewport={"width": 1920, "height": 1080})
        page = ctx.new_page()
        page.goto(args.url, wait_until="networkidle")

        classes = page.evaluate("() => Array.from(document.querySelectorAll('*')).flatMap(function(n){ if(!n.className) return []; if(typeof n.className === 'string') return n.className.split(/\s+/); return []; }).filter(Boolean)")
        tr_classes = sorted(set([c for c in classes if 'tr-' in c]))
        print(f"Found {len(tr_classes)} class names containing 'tr-':")
        for c in tr_classes[:200]:
            print(' -', c)

        ctx.close()
        browser.close()


if __name__ == '__main__':
    main()
