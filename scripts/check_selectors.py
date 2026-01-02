"""
Check presence of card/container selectors on the page.

Usage:
  python scripts/check_selectors.py --url <url> --card "<card selector>" --container "<container selector>"
"""
from __future__ import annotations

import argparse
from playwright.sync_api import sync_playwright


def parse_args():
    p = argparse.ArgumentParser()
    p.add_argument("--url", required=True)
    p.add_argument("--card", required=True)
    p.add_argument("--container", required=True)
    return p.parse_args()


def main():
    args = parse_args()
    with sync_playwright() as pw:
        browser = pw.chromium.launch(headless=True)
        ctx = browser.new_context(viewport={"width": 1200, "height": 800})
        page = ctx.new_page()
        page.goto(args.url, wait_until="networkidle")

        card_exists = page.evaluate("(s) => !!document.querySelector(s)", args.card)
        container_exists = page.evaluate("(s) => !!document.querySelector(s)", args.container)
        container_in_card = page.evaluate("(arg) => { const cardSel = arg.cardSel; const containerSel = arg.containerSel; const c = document.querySelector(cardSel); if(!c) return false; return !!c.querySelector(containerSel); }", {"cardSel": args.card, "containerSel": args.container})

        print(f"card_exists: {card_exists}")
        print(f"container_exists: {container_exists}")
        print(f"container_in_card: {container_in_card}")

        ctx.close()
        browser.close()


if __name__ == '__main__':
    main()
