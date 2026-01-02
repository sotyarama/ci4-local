"""
Open a headed Chromium browser to inspect the branding page interactively.

Usage:
  python scripts/inspect_headful.py --url http://localhost:8080/branding

The script will open a visible Chromium window and wait for you to press Enter
in the terminal to close the browser.
"""
from __future__ import annotations

import argparse
import sys
from playwright.sync_api import sync_playwright


def parse_args():
    p = argparse.ArgumentParser()
    p.add_argument("--url", required=True)
    return p.parse_args()


def main():
    args = parse_args()
    with sync_playwright() as pw:
        try:
            browser = pw.chromium.launch(headless=False)
            ctx = browser.new_context(viewport={"width": 1280, "height": 900})
            page = ctx.new_page()
            page.goto(args.url, wait_until="networkidle")
            print(f"Opened headed Chromium to {args.url} - inspect the page now.")
            print("When finished, return here and press Enter to close the browser.")
            try:
                input()
            except KeyboardInterrupt:
                pass
        except Exception as e:
            print(f"Failed to open headed browser: {e}", file=sys.stderr)
        finally:
            try:
                ctx.close()
            except Exception:
                pass
            try:
                browser.close()
            except Exception:
                pass


if __name__ == '__main__':
    main()
