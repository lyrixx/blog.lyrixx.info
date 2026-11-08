"""Draws the image shown when an article is shared: cover.py <directory of the article> <title> <latin name>."""
import re
import sys
import tomllib
from pathlib import Path

import resvg_py

from blueprint import CREAM, REPOSITORY, SOFT, sheet, text, text_width

WIDTH, HEIGHT = 1200, 630
LEFT = 70
TITLE_CENTER = 318
TITLE_LEADING = 1.04
TITLE_MAX_HEIGHT = 340
TITLE_MAX_WIDTH = 640
TITLE_SIZES = (104, 92, 80, 70, 62)


def main(directory, title, latin):
    directory = Path(directory)
    author = tomllib.loads((REPOSITORY / 'hugo.toml').read_text(encoding='utf-8'))['title']
    size, lines = fit(title)
    leading = size * TITLE_LEADING
    first = TITLE_CENTER - (len(lines) - 1) * leading / 2 + size * .3

    def content(rng):
        out = plant(directory / 'plant.svg')
        out += text(latin, LEFT, 104, 44, fill=CREAM, rot=-1)
        for i, line in enumerate(lines):
            out += text(line, LEFT, first + i * leading, size, rot=-1)
        return out + text(author, LEFT, 572, 38, fill=SOFT, rot=0)

    svg = sheet(directory.name, content, WIDTH, HEIGHT)
    (directory / 'cover.png').write_bytes(bytes(resvg_py.svg_to_bytes(svg_string=svg)))


def fit(title):
    """The largest size at which the title fits on the left of the sheet, and its lines at this size."""
    for size in TITLE_SIZES:
        lines = wrap(title, size)
        if len(lines) * size * TITLE_LEADING <= TITLE_MAX_HEIGHT:
            return size, lines
    raise ValueError(f'The title "{title}" is too long for a cover.')


def wrap(title, size):
    lines, line = [], ''
    for word in title.split():
        candidate = f'{line} {word}'.strip()
        if line and text_width(candidate, size) > TITLE_MAX_WIDTH:
            lines.append(line)
            line = word
        else:
            line = candidate
    return [*lines, line]


def plant(path):
    svg = path.read_text(encoding='utf-8')
    view_box = re.search(r'viewBox="([^"]+)"', svg)[1]
    drawing = svg[svg.index('>') + 1:svg.rindex('</svg>')]
    return f'<svg x="730" y="58" width="450" height="514" viewBox="{view_box}" preserveAspectRatio="xMidYMid meet" opacity=".92">{drawing}</svg>'


main(*sys.argv[1:])
