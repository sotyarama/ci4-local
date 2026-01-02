"""
Inspect branding page for common selectors to help debugging the screenshot script.

Usage:
  python scripts/inspect_branding.py --url http://localhost:8080/branding
"""
from __future__ import annotations

import argparse
import sys
from typing import List
from pathlib import Path
from playwright.sync_api import sync_playwright


def parse_args():
    p = argparse.ArgumentParser()
    p.add_argument("--url", required=True)
    return p.parse_args()


def main():
    args = parse_args()
    selectors = [
        "tr-section",
        ".tr-section",
        ".tr-slide",
        "tr-slide",
        ".tr-branding-page",
        ".tr-branding-container",
        "[data-slide]",
    ]

    with sync_playwright() as pw:
        browser = pw.chromium.launch(headless=True)
        ctx = browser.new_context(viewport={"width":1200, "height":800})
        page = ctx.new_page()
        page.goto(args.url, wait_until="networkidle")

        for sel in selectors:
            try:
                count = page.evaluate("(s) => document.querySelectorAll(s).length", sel)
            except Exception as e:
                count = f"error: {e}"
            print(f"Selector '{sel}': {count}")
            if isinstance(count, int) and count > 0:
                try:
                    outer = page.evaluate("(s) => document.querySelector(s).outerHTML", sel)
                    snippet = outer[:1000].replace('\n','')
                    print(f"  Sample: {snippet}")
                except Exception as e:
                    print(f"  Failed to get outerHTML: {e}")

        # also print total number of elements
        total_nodes = page.evaluate("() => document.getElementsByTagName('*').length")
        print(f"Total DOM nodes: {total_nodes}")

        # save full page HTML for inspection
        try:
            html = page.content()
            out_path = Path(__file__).resolve().parent.parent / 'branding_page_snapshot.html'
            out_path.write_text(html, encoding='utf-8')
            print(f"Saved page snapshot to: {out_path}")
        except Exception as e:
            print(f"Failed to save page snapshot: {e}")

        ctx.close()
        browser.close()


if __name__ == '__main__':
    main()
