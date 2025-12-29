#!/usr/bin/env python3
"""
Simple visual-diff script: compares images in a baseline folder with a current folder,
creates annotated side-by-side PNGs highlighting pixel differences, and prints a summary.

Usage:
  python scripts/visual_diff.py --baseline screenshots/baseline --current screenshots/trial --out screenshots/diff

Requires: Pillow
"""
from pathlib import Path
from PIL import Image, ImageChops, ImageStat, ImageOps
import argparse
import sys


def ensure_dir(p: Path):
    p.mkdir(parents=True, exist_ok=True)


def compare_images(bpath: Path, cpath: Path, outpath: Path, threshold=10):
    a = Image.open(bpath).convert('RGBA')
    b = Image.open(cpath).convert('RGBA')

    # normalize sizes: resize 'b' to match 'a' if different
    if a.size != b.size:
        b = ImageOps.fit(b, a.size, Image.LANCZOS)

    diff = ImageChops.difference(a, b)
    stat = ImageStat.Stat(diff)
    # prefer total-sum check to avoid false-negative bbox on small float means
    total_sum = sum(stat.sum)
    if total_sum == 0:
        return True, 0.0

    # create a mask where differences exceed threshold
    gray = diff.convert('L')
    mask = gray.point(lambda p: 255 if p > threshold else 0)

    # highlight differences in red overlay
    highlight = Image.new('RGBA', a.size, (255, 0, 0, 128))
    annotated = b.copy()
    annotated.paste(highlight, mask=mask)

    # compose side-by-side: baseline | current-with-annotations | diff-heat
    diff_vis = Image.merge('RGB', (gray, Image.new('L', a.size, 0), Image.new('L', a.size, 0))).convert('RGBA')
    w, h = a.size
    canvas = Image.new('RGBA', (w * 3 + 20, h), (255, 255, 255, 255))
    canvas.paste(a, (0, 0))
    canvas.paste(annotated, (w + 10, 0))
    canvas.paste(diff_vis, (2 * (w + 10), 0))

    canvas.save(outpath)

    # percentage of non-zero pixels in mask
    nonzero = ImageStat.Stat(mask).sum[0]
    pct = nonzero / (w * h * 255.0) * 100.0
    return False, pct


def main():
    ap = argparse.ArgumentParser()
    ap.add_argument('--baseline', default='screenshots/baseline')
    ap.add_argument('--current', default='screenshots/current')
    ap.add_argument('--out', default='screenshots/diff')
    ap.add_argument('--threshold', type=int, default=10, help='pixel difference threshold (0-255)')
    args = ap.parse_args()

    base_dir = Path(args.baseline)
    cur_dir = Path(args.current)
    out_dir = Path(args.out)

    if not base_dir.exists():
        print('Baseline folder not found:', base_dir)
        sys.exit(2)
    if not cur_dir.exists():
        print('Current folder not found:', cur_dir)
        sys.exit(2)

    ensure_dir(out_dir)

    files = sorted([p for p in base_dir.glob('*.png')])
    if not files:
        print('No PNG files found in baseline:', base_dir)
        sys.exit(0)

    total = len(files)
    identical = 0
    diffs = []

    for f in files:
        name = f.name
        cur = cur_dir / name
        out = out_dir / name
        if not cur.exists():
            print('MISSING current:', name)
            diffs.append((name, 'missing', None))
            continue

        same, pct = compare_images(f, cur, out, threshold=args.threshold)
        if same:
            identical += 1
            print('IDENTICAL', name)
        else:
            print(f'DIFF {name} — changed {pct:.4f}%')
            diffs.append((name, 'diff', pct))

    print('Summary: total={}, identical={}, diffs={}'.format(total, identical, len(diffs)))
    if diffs:
        sys.exit(2)
    sys.exit(0)


if __name__ == '__main__':
    main()
