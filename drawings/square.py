"""Draws the square image shown when a page is shared: square.py <plant.svg> <square.png> <title>.

Some apps (WhatsApp, Slack…) show the link with a square thumbnail: a centred crop of cover.png would cut the title.
"""
import sys
from pathlib import Path

import resvg_py

from blueprint import sheet, text
from cover import TITLE_LEADING, fit, plant

# Keep in sync with the size of the site's square in layouts/_partials/square-image.html.
SIZE = 800
TITLE_MAX_HEIGHT = 280
TITLE_MAX_WIDTH = 680
TITLE_SIZES = (84, 72, 62, 54, 48)
TITLE_TOP = 470


def main(plant_path, square_path, title):
    square_path = Path(square_path)
    size, lines = fit(title, TITLE_SIZES, TITLE_MAX_WIDTH, TITLE_MAX_HEIGHT)
    leading = size * TITLE_LEADING
    first = TITLE_TOP + size * .8

    def content(rng):
        out = plant(Path(plant_path), 200, 40, 400, 400)
        for i, line in enumerate(lines):
            out += text(line, SIZE / 2, first + i * leading, size, anchor='middle', rot=-1)
        return out

    svg = sheet(square_path.parent.name, content, SIZE, SIZE)
    square_path.write_bytes(bytes(resvg_py.svg_to_bytes(svg_string=svg)))


main(*sys.argv[1:])
