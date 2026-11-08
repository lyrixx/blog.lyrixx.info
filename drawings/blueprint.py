"""The pencil and the paper of the drawings, as docs/drawing-style.md describes them.

Text is converted to paths, because an SVG in an <img> can't use the fonts of the page.
"""
import math
import random
from pathlib import Path

from fontTools.pens.svgPathPen import SVGPathPen
from fontTools.pens.transformPen import TransformPen
from fontTools.ttLib import TTFont

REPOSITORY = Path(__file__).resolve().parent.parent
W, H = 780, 440
PALE, SOFT, CREAM = '#eef3f8', '#a9bed4', '#f6e7b4'

_font = TTFont(REPOSITORY / 'static/fonts/caveat-700-normal.woff2')
_glyphs, _cmap, _upm = _font.getGlyphSet(), _font.getBestCmap(), _font['head'].unitsPerEm


def draw(script, name, seed, content):
    """Writes <name>.svg next to the script, in the directory of its article."""
    directory = Path(script).resolve().parent
    (directory / f'{name}.svg').write_text(sheet(seed, content), encoding='utf-8')
    print(f'Drew {directory.name}/{name}.svg')


def sheet(seed, content, width=W, height=H):
    """The SVG of a sheet of paper, with the content drawn on it."""
    rng = random.Random(seed)
    m = 6

    def wobble(count):
        p = [rng.uniform(0, 6.28) for _ in range(3)]
        return [1.5 * math.sin(i * .21 + p[0]) + math.sin(i * .57 + p[1]) + .6 * math.sin(i * 1.4 + p[2]) + rng.uniform(-.4, .4) for i in range(count)]

    across, down = 130, 74
    pts = [(m + (width - 2 * m) * i / across, m + d) for i, d in enumerate(wobble(across))]
    pts += [(width - m + d, m + (height - 2 * m) * i / down) for i, d in enumerate(wobble(down))]
    pts += [(width - m - (width - 2 * m) * i / across, height - m + d) for i, d in enumerate(wobble(across))]
    pts += [(m + d, height - m - (height - 2 * m) * i / down) for i, d in enumerate(wobble(down))]
    edge = 'M' + 'L'.join(f'{n(x)} {n(y)}' for x, y in pts) + 'Z'
    grid = ''.join(f'M{x} 0V{height}' for x in range(55, width, 65)) + ''.join(f'M0 {y}H{width}' for y in range(60, height, 65))
    return (
        f'<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 {width} {height}" width="{width}" height="{height}"><defs>'
        '<radialGradient id="g" cx=".2" cy=".1" r="1.1"><stop offset="0" stop-color="#1f5592"/>'
        '<stop offset=".5" stop-color="#143f74"/><stop offset="1" stop-color="#0c2b54"/></radialGradient>'
        f'<clipPath id="c"><path d="{edge}"/></clipPath></defs>'
        f'<path d="{edge}" fill="url(#g)"/>'
        f'<g clip-path="url(#c)"><path d="{grid}" fill="none" stroke="{PALE}" stroke-width="1" opacity=".07"/>{content(rng)}</g></svg>\n'
    )


def n(v):
    return f'{v:.1f}'.rstrip('0').rstrip('.')


def text_width(s, size):
    # In this order, as the published drawings were computed: another one rounds some points differently.
    return sum(_glyphs[_cmap[ord(c)]].width for c in s) * (size / _upm)


def text(s, x, y, size=34, anchor='start', fill=PALE, rot=-1.5):
    """Handwritten text. Rotated by default, as a note in the margin; rot=0 for a label."""
    scale = size / _upm
    width = text_width(s, size)
    cx = x - {'start': 0, 'middle': width / 2, 'end': width}[anchor]
    if cx < 14 or cx + width > W - 14:
        print(f'Warning: "{s}" goes beyond the margin of 14 px ({n(cx)} to {n(cx + width)}).')
    d = ''
    for c in s:
        g = _glyphs[_cmap[ord(c)]]
        pen = SVGPathPen(_glyphs, ntos=n)
        g.draw(TransformPen(pen, (scale, 0, 0, -scale, cx, y)))
        d += pen.getCommands()
        cx += g.width * scale
    return f'<path d="{d}" fill="{fill}" transform="rotate({rot} {x} {y})"/>'


def wobbly(points, rng, amp=1.1):
    """A pencil stroke through the points."""
    out = []
    p = [rng.uniform(0, 6.28) for _ in range(2)]
    length = 0
    for (x0, y0), (x1, y1) in zip(points, points[1:]):
        dist = math.hypot(x1 - x0, y1 - y0)
        steps = max(1, int(dist / 12))
        for i in range(steps):
            t = i / steps
            s = length + dist * t
            out.append((x0 + (x1 - x0) * t + amp * math.sin(s * .021 + p[0]), y0 + (y1 - y0) * t + amp * math.sin(s * .017 + p[1])))
        length += dist
    out.append(points[-1])
    return 'M' + 'L'.join(f'{n(x)} {n(y)}' for x, y in out)


def stroke(points, rng, colour=PALE, width=3.4, dash='', opacity=1, amp=1.1):
    extra = f' stroke-dasharray="{dash}"' if dash else ''
    return f'<path d="{wobbly(points, rng, amp)}" fill="none" stroke="{colour}" stroke-width="{width}" stroke-linecap="round" stroke-linejoin="round" opacity="{opacity}"{extra}/>'


def dot(x, y, colour=PALE, r=9):
    # Not n(): the published drawings of Shamir were written with the floats as they are.
    return f'<circle cx="{x}" cy="{y}" r="{r}" fill="{colour}"/>'


def ring(x, y, rng, colour=CREAM, r=21, squash=.94):
    """A ring drawn by hand around the thing to look at: it goes past where it starts."""
    pts = []
    start = rng.uniform(0, 6.28)
    for i in range(34):
        a = start + i / 30 * 2 * math.pi
        rr = r * (1 + .05 * math.sin(a * 2)) + i * .12
        pts.append((x + rr * math.cos(a), y + rr * squash * math.sin(a)))
    return stroke(pts, rng, colour, 2.6, amp=.3)


def head(x, y, angle, rng, colour=PALE, size=15, width=2.4):
    """The head of an arrow pointing at x, y in the direction of the angle."""
    tips = [(x + size * math.cos(angle + s), y + size * math.sin(angle + s)) for s in (2.6, -2.6)]
    return stroke([tips[0], (x, y), tips[1]], rng, colour, width, amp=.1)


def arrow(x0, y0, x1, y1, rng, colour=PALE, bend=.18, width=2.4):
    """A hand-drawn arrow, slightly curved."""
    mx, my = (x0 + x1) / 2 + (y1 - y0) * bend, (y0 + y1) / 2 - (x1 - x0) * bend
    pts = [((1 - t) ** 2 * x0 + 2 * (1 - t) * t * mx + t * t * x1, (1 - t) ** 2 * y0 + 2 * (1 - t) * t * my + t * t * y1) for t in [i / 16 for i in range(17)]]
    angle = math.atan2(y1 - pts[-3][1], x1 - pts[-3][0])
    return stroke(pts, rng, colour, width, amp=.3) + head(x1, y1, angle, rng, colour, width=width)


def file_icon(x, y, rng, w=46, h=58, colour=PALE, width=3, lines=True):
    """A sheet with a folded corner, centred on x, y."""
    f = w * .32
    left, right, top, bottom = x - w / 2, x + w / 2, y - h / 2, y + h / 2
    out = stroke([(left, top), (right - f, top), (right, top + f), (right, bottom), (left, bottom), (left, top)], rng, colour, width, amp=.4)
    out += stroke([(right - f, top), (right - f, top + f), (right, top + f)], rng, colour, width * .75, amp=.2)
    if lines:
        for i, line_y in enumerate((top + f + 8, top + f + 17, top + f + 26)):
            if line_y < bottom - 6:
                out += stroke([(left + 8, line_y), (right - 8 - (10 if i == 2 else 0), line_y)], rng, colour, 1.6, opacity=.6, amp=.2)
    return out
