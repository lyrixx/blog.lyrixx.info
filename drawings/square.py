"""Draws the square image shown when a page is shared: square.py <plant.svg> <square.png> <title> <latin name>.

Slack shows it whole, as a square thumbnail. Bluesky keeps only its middle band, in the 1200/630
ratio of its cards: the title lives in this band, over the plant drawn as a watermark, and the latin
name and the author around it.
"""
import sys
import tomllib
from pathlib import Path

import resvg_py

from blueprint import CREAM, REPOSITORY, SOFT, sheet, text
from cover import TITLE_LEADING, fit, plant

# Keep in sync with the size of the site's square in layouts/_partials/link-preview.html.
SIZE = 800
BAND_HEIGHT = SIZE * 630 / 1200
BAND_TOP = (SIZE - BAND_HEIGHT) / 2
LEFT = 50
TITLE_MAX_HEIGHT = BAND_HEIGHT - 30
TITLE_MAX_WIDTH = SIZE - 2 * LEFT
TITLE_SIZES = (128, 112, 100, 88, 78, 70, 62)
WATERMARK_OPACITY = .2


def main(plant_path, square_path, title, latin):
    square_path = Path(square_path)
    author = tomllib.loads((REPOSITORY / 'hugo.toml').read_text(encoding='utf-8'))['title']
    size, lines = fit(title, TITLE_SIZES, TITLE_MAX_WIDTH, TITLE_MAX_HEIGHT)
    leading = size * TITLE_LEADING
    first = SIZE / 2 - (len(lines) - 1) * leading / 2 + size * .3

    def content(rng):
        # On the right, and larger than the sheet: the plant overflows it, as a watermark.
        out = f'<g opacity="{WATERMARK_OPACITY}">{plant(Path(plant_path), 300, 20, 560, 760)}</g>'
        out += text(latin, LEFT, BAND_TOP - 40, 40, fill=CREAM, rot=-1)
        for i, line in enumerate(lines):
            out += text(line, LEFT, first + i * leading, size, rot=-1)
        return out + text(author, LEFT, BAND_TOP + BAND_HEIGHT + 70, 32, fill=SOFT, rot=0)

    svg = sheet(square_path.parent.name, content, SIZE, SIZE)
    square_path.write_bytes(bytes(resvg_py.svg_to_bytes(svg_string=svg)))


main(*sys.argv[1:])
