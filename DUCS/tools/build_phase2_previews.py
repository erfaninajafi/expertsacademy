#!/usr/bin/env python3
"""Build gallery WebPs and review contact sheets from verified v1 Playwright PNGs."""
import argparse
import hashlib
import json
from pathlib import Path
from PIL import Image, ImageDraw, ImageFont, ImageOps

ROOT = Path(__file__).resolve().parents[2]
parser = argparse.ArgumentParser()
parser.add_argument('--visual-root', required=True, type=Path)
args = parser.parse_args()
catalog = json.loads((ROOT / 'include_site/studio/catalog/components.v1.json').read_text())
definitions = catalog['components']
target = ROOT / 'asset_site/studio/previews'
target.mkdir(parents=True, exist_ok=True)
sheets = args.visual_root / 'contact-sheets'
sheets.mkdir(parents=True, exist_ok=True)
font = ImageFont.truetype('/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf', 12)
manifest = []
for category in catalog['categories']:
    types = [d for d in definitions if d['category'] == category['id']]
    sheet = Image.new('RGB', (1280, 3 * 246 + 36), '#e9e7e1')
    draw = ImageDraw.Draw(sheet)
    draw.text((12, 10), category['label'], fill='#142e28', font=font)
    for row, definition in enumerate(types):
        for col, variant in enumerate(definition['variants']):
            source = args.visual_root / 'baseline/chromium' / f"{definition['type']}--v1--{variant['id']}--desktop-en-light.png"
            with Image.open(source) as original:
                thumb = Image.new('RGB', (640, 400), '#f5f3ed')
                contained = ImageOps.contain(original.convert('RGB'), (640, 400), Image.Resampling.LANCZOS)
                thumb.paste(contained, ((640-contained.width)//2, (400-contained.height)//2))
            output = target / f"{definition['type']}-{variant['id']}.webp"
            thumb.save(output, 'WEBP', quality=82, method=6)
            tile = thumb.resize((320, 200), Image.Resampling.LANCZOS)
            y = 36 + row * 246
            sheet.paste(tile, (col*320, y))
            draw.text((col*320+8, y+206), definition['type'], fill='#142e28', font=font)
            draw.text((col*320+8, y+224), variant['id'], fill='#465851', font=font)
            manifest.append({'file': str(output.relative_to(ROOT)), 'source': source.name, 'sha256': hashlib.sha256(output.read_bytes()).hexdigest()})
    sheet.save(sheets / f"{category['id']}.jpg", quality=90)
assert len(manifest) == 240
(args.visual_root/'gallery-manifest.json').write_text(json.dumps(manifest, indent=2)+'\n')
print(json.dumps({'gallery_previews': len(manifest), 'contact_sheets': len(catalog['categories'])}))
