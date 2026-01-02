"""
Simple README:

Requirements:
  pip install playwright
  python -m playwright install chromium

Usage example:
  python scripts/branding_screenshots.py --url http://localhost:8080/branding --out branding_slides

This script uses Playwright (Chromium) headless to capture each slide's card
into separate PNG files named 001.png, 002.png, ... in the output folder.

"""
from __future__ import annotations

import argparse
import sys
import time
from pathlib import Path
from typing import Optional

from playwright.sync_api import Playwright, sync_playwright, TimeoutError as PWTimeoutError


def parse_args() -> argparse.Namespace:
    parser = argparse.ArgumentParser(description="Screenshot each slide's card from a branding page")
    parser.add_argument("--url", required=True, help="URL halaman branding (contoh http://localhost:8080/branding)")
    parser.add_argument("--out", default="branding_slides", help="Folder output (default: branding_slides)")
    parser.add_argument("--card", default=".tr-branding-page", help="Selector elemen card (default: .tr-branding-page)")
    parser.add_argument("--slide", default=".tr-slide", help="Selector elemen slide (default: .tr-slide)")
    parser.add_argument("--container", default=".tr-branding-container", help="Selector container (default: .tr-branding-container)")
    parser.add_argument("--activate", choices=("js","click"), help='"js" to add class, "click" to click toggle')
    parser.add_argument("--toggle", help="Selector tombol slide mode jika activate=click")
    parser.add_argument("--wait", type=int, default=250, help="Delay ms setelah pindah slide sebelum screenshot (default: 250)")
    parser.add_argument("--headed", action="store_true", help="Run browser headed (visible) for debugging")
    parser.add_argument("--interactive", action="store_true", help="When headed, wait for user to press Enter before capturing (useful to login)")
    parser.add_argument("--capture-keys", action="store_true", help="When headed, accept numeric inputs to capture slides interactively (type number then Enter; empty line to finish)")
    return parser.parse_args()


def normalize_selector(sel: str) -> str:
    if not sel:
        return sel
    s = sel.strip()
    if s.startswith(('.', '#', '[', '*')):
        return s
    # If selector contains combinators or whitespace, assume it's a complex selector and return as-is
    if any(ch in s for ch in (' ', '>', '+', '~', ',')):
        return s
    # if selector looks like a single hyphenated token (e.g., tr-section), treat as class
    if '-' in s:
        return '.' + s
    return s


def error(msg: str, code: int = 1) -> None:
    print(f"Error: {msg}", file=sys.stderr)
    sys.exit(code)


def ensure_output_dir(path: str) -> Path:
    p = Path(path)
    p.mkdir(parents=True, exist_ok=True)
    return p


def activate_slide_mode_js(page, container_selector: str) -> None:
    script = """(containerSel) => {
        const c = document.querySelector(containerSel);
        if (c) c.classList.add('tr-slide-mode');
        document.body.classList.add('tr-slide-mode');
        return !!c;
    }"""
    try:
        page.evaluate(script, container_selector)
    except Exception:
        # Non-fatal: continue — the class may not be necessary
        pass


def set_active_slide_by_index(page, slide_selector: str, idx: int) -> bool:
    script = """(sel, index) => {
        const slides = Array.from(document.querySelectorAll(sel));
        if (!slides.length || !slides[index]) return false;
        slides.forEach(s => s.classList.remove('is-active'));
        slides[index].classList.add('is-active');
        // reset scroll position if scrollable
        try { slides[index].scrollTo ? slides[index].scrollTo({top:0}) : slides[index].scrollTop = 0; } catch(e) {}
        return true;
    }"""
    return page.evaluate(script, slide_selector, idx)


def run(playwright: Playwright, args: argparse.Namespace) -> None:
    browser = playwright.chromium.launch(headless=not args.headed)
    context = browser.new_context(viewport={"width": 1920, "height": 1080})
    page = context.new_page()

    try:
        try:
            page.goto(args.url, wait_until="networkidle")
        except PWTimeoutError:
            error(f"Navigation to {args.url} timed out or failed.")
        except Exception as e:
            error(f"Failed to navigate to {args.url}: {e}")

        # Activate slide mode if requested
        if args.activate == "js":
            activate_slide_mode_js(page, args.container)
            time.sleep(0.1)
        elif args.activate == "click":
            if not args.toggle:
                error("--toggle is required when --activate=click")
            try:
                page.click(args.toggle)
            except Exception as e:
                error(f"Failed to click toggle selector {args.toggle}: {e}")
            time.sleep(0.1)

        # normalize selectors (auto-add leading dot for hyphenated names)
        slide_sel = normalize_selector(args.slide)
        card_sel = normalize_selector(args.card)

        # interactive pause: when headed and requested, allow user to prepare/login
        if args.headed and getattr(args, 'interactive', False):
            print("Headed mode interactive: prepare the page (login, enable slide mode), then press Enter to continue...")
            try:
                input()
            except KeyboardInterrupt:
                pass

        # interactive numeric capture: when headed and requested, accept numbers to save screenshots
        if args.headed and getattr(args, 'capture_keys', False):
            card_sel = normalize_selector(args.card)
            # ensure card is present
            try:
                page.wait_for_selector(card_sel, timeout=15000)
            except Exception:
                print(f"Warning: card element not found immediately using selector: {args.card}. You can still prepare the page; screenshots will be attempted when you press numbers.")

            print("Interactive capture mode:")
            print(" - Make the desired slide visible in the browser window.")
            print(" - Type the slide number (e.g. 1) and press Enter to capture.")
            print(" - Repeat for each slide. Submit an empty line to finish.")

            while True:
                try:
                    s = input("Capture slide #: ").strip()
                except (KeyboardInterrupt, EOFError):
                    print()
                    break
                if s == "":
                    break
                if not s.isdigit():
                    print("Please enter a number or empty line to finish.")
                    continue
                idx = int(s)
                if idx <= 0:
                    print("Please enter a positive number.")
                    continue

                # short delay to allow any visual change
                time.sleep(max(0.05, args.wait / 1000.0))

                out_dir = ensure_output_dir(args.out)
                filename = f"{idx:03d}.png"
                out_path = out_dir / filename
                try:
                    page.locator(card_sel).screenshot(path=str(out_path))
                except Exception as e:
                    print(f"Failed to screenshot card: {e}")
                    continue

                print(f"Saved: {out_path}")

            # finished interactive capture
            try:
                context.close()
                browser.close()
            except Exception:
                pass
            return

        # Wait/poll for card and container to be available (useful when page builds UI asynchronously)
        card_sel = normalize_selector(args.card)
        container_sel = normalize_selector(args.container)
        def try_wait(sel: str, timeout_ms: int = 5000) -> bool:
            try:
                page.wait_for_selector(sel, timeout=timeout_ms)
                return True
            except Exception:
                return False

        # wait for card first
        if not try_wait(card_sel, timeout_ms=5000):
            # try a shorter wait and continue — error later if not found
            pass

        # try wait for container inside card
        combined = f"{card_sel} {container_sel}" if container_sel else container_sel
        if container_sel:
            _ok = try_wait(combined, timeout_ms=3000)

        # discover slide groups based on container children (mirrors branding.js grouping)
        container_sel = normalize_selector(args.container)
        # if container not found, we'll fall back to using the card as container
        try:
            found_container = page.evaluate("(s) => !!document.querySelector(s)", container_sel)
        except Exception:
            found_container = False
        if not found_container:
            container_sel = card_sel

        groups_count = page.evaluate(
            "(arg) => {\n"
            "  const containerSel = arg.containerSel; const cardSel = arg.cardSel;\n"
            "  let container = document.querySelector(containerSel);\n"
            "  if (!container) { try { const card = document.querySelector(cardSel); if (card) container = card.querySelector(containerSel); } catch(e) {} }\n"
             "  if (!container) return -1;\n"
            "  const children = Array.from(container.children);\n"
            "  const groups = [];\n"
            "  let current = [];\n"
            "  children.forEach(function(node){\n"
            "    try {\n"
            "      if (node.matches && node.matches('section.tr-section')) {\n"
            "        var mode = node.getAttribute('data-slide') || '';\n"
            "        if (mode !== 'continue' && current.length) { groups.push(current.slice()); current = []; }\n"
            "      }\n"
            "    } catch(e) {}\n"
            "    current.push(node);\n"
            "  });\n"
            "  if (current.length) groups.push(current.slice());\n"
            "  groups.forEach(function(grp, idx){ grp.forEach(function(n){ try { n.setAttribute('data-snap-group', String(idx)); } catch(e){} }); });\n"
            "  return groups.length;\n"
            "}",
            { "containerSel": container_sel, "cardSel": card_sel },
        )

        if groups_count == -1:
            error(f"Container element not found with selector: {args.container}")
        if groups_count == 0:
            error(f"No slide groups discovered inside container: {args.container}")

        out_dir = ensure_output_dir(args.out)

        # verify card exists at least once
        card_locator = page.locator(card_sel)
        if card_locator.count() == 0:
            # sometimes card appears after activating; try a short wait
            time.sleep(0.2)
            if page.locator(card_sel).count() == 0:
                error(f"Card element not found using selector: {args.card}")

        wait_s = max(0, args.wait) / 1000.0

        for i in range(groups_count):
            # hide all children, show only those in group i
            try:
                page.evaluate(
                    "(containerSel, idx) => {\n"
                    "  const container = document.querySelector(containerSel); if (!container) return false;\n"
                    "  Array.from(container.children).forEach(n => { try{ n.style.display = 'none'; }catch(e){} });\n"
                    "  Array.from(container.querySelectorAll('[data-snap-group="'+idx+'"]')).forEach(n => { try{ n.style.display = ''; if(n.scrollTo) n.scrollTo({top:0}); else n.scrollTop=0; }catch(e){} });\n"
                    "  // ensure slide-mode class present\n"
                    "  try { container.classList.add('tr-slide-mode'); document.body.classList.add('tr-slide-mode'); } catch(e){}\n"
                    "  return true;\n"
                    "}",
                    container_sel,
                    i,
                )
            except Exception:
                pass

            # ensure card is at top of its internal scroll
            try:
                page.evaluate("(cardSel) => { const c = document.querySelector(cardSel); if (c) { try { c.scrollTo ? c.scrollTo({top:0}) : c.scrollTop = 0; } catch(e) {} } }", card_sel)
            except Exception:
                pass

            time.sleep(wait_s)

            filename = f"{i+1:03d}.png"
            out_path = out_dir / filename

            try:
                page.locator(card_sel).screenshot(path=str(out_path))
            except Exception as e:
                error(f"Failed to screenshot card for slide {i+1}: {e}")

            print(f"Saved: {out_path}")

    finally:
        try:
            context.close()
            browser.close()
        except Exception:
            pass


def main() -> None:
    args = parse_args()
    try:
        with sync_playwright() as playwright:
            run(playwright, args)
    except Exception as e:
        error(f"Unexpected error: {e}")


if __name__ == "__main__":
    main()
