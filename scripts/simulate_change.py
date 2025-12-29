#!/usr/bin/env python3
"""
Copy baseline images to current and apply a small visual change to one image
so visual-diff tooling can be exercised without touching views or CSS.

Usage:
  python scripts/simulate_change.py --baseline screenshots/baseline --current screenshots/current
"""
from pathlib import Path
from PIL import Image, ImageDraw
import shutil
import argparse


def main():
    ap = argparse.ArgumentParser()
    ap.add_argument('--baseline', default='screenshots/baseline')
    ap.add_argument('--current', default='screenshots/current')
    args = ap.parse_args()

    base = Path(args.baseline)
    cur = Path(args.current)
    cur.mkdir(parents=True, exist_ok=True)

    files = sorted(base.glob('*.png'))
    if not files:
        print('No baseline images found in', base)
        return

    # copy all baseline files to current
    for f in files:
        shutil.copy2(f, cur / f.name)

    # apply a small change to the first image to simulate a UI change
    target = cur / files[0].name
    with Image.open(target) as im:
        draw = ImageDraw.Draw(im)
        w, h = im.size
        # draw a small filled rectangle in the corner
        rect = (w - 200, 20, w - 20, 80)
        draw.rectangle(rect, fill=(255, 0, 0, 128))
        im.save(target)

    print('Simulated change applied to', target)


if __name__ == '__main__':
    main()
