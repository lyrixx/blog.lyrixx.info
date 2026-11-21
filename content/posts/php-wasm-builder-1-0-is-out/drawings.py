"""PHP in the browser: what runs where, and how Symfony gets its requests."""
from blueprint import CREAM, PALE, SOFT, arrow, draw, ring, stroke, text


def box(left, top, right, bottom, rng, colour=PALE, width=3.4):
    return stroke([(left, top), (right, top), (right, bottom), (left, bottom), (left, top)], rng, colour, width)


def layers(rng):
    out = box(30, 150, 180, 290, rng) + text('your page', 105, 230, 34, 'middle', rot=0)

    out += box(260, 150, 430, 290, rng) + text('php-web.mjs', 345, 212, 30, 'middle', rot=0)
    out += text('the glue', 345, 254, 32, 'middle', CREAM, rot=0) + ring(345, 243, rng, r=62, squash=.42)
    out += text('a fake OS: stdout,', 345, 344, 28, 'middle', CREAM)
    out += text('files in memory', 345, 376, 28, 'middle', CREAM)

    out += text('php-web.wasm, a sandbox', 630, 50, 30, 'middle', SOFT, rot=0)
    out += box(510, 70, 750, 390, rng)
    rows = ('run.c', 'PHP, embed SAPI', 'extensions', 'C libraries, libc')
    for i, label in enumerate(rows):
        out += text(label, 630, 124 + 80 * i, 30, 'middle', rot=0)
        if i:
            out += stroke([(526, 70 + 80 * i), (734, 70 + 80 * i)], rng, SOFT, 2.4, dash='2 11', amp=.4)

    for x0, x1 in ((180, 260), (430, 510)):
        out += arrow(x0 + 8, 186, x1 - 8, 186, rng, PALE, bend=.1)
        out += arrow(x1 - 8, 254, x0 + 8, 254, rng, PALE, bend=.1)
    out += text('code', 220, 140, 28, 'middle', SOFT)
    out += text('stdout', 220, 322, 28, 'middle', SOFT)
    return out


def symfony(rng):
    out = text('serveInFrame()', 30, 52, 32, rot=0)
    y0, y1 = 80, 190
    out += box(30, y0, 190, y1, rng) + text('the frame', 110, 146, 32, 'middle', rot=0)
    out += box(290, y0, 490, y1, rng) + text('service worker', 390, 146, 32, 'middle', rot=0)
    out += box(590, y0, 750, y1, rng) + text('the page', 670, 126, 32, 'middle', rot=0)
    out += text('PHP', 670, 172, 30, 'middle', CREAM, rot=0) + ring(670, 162, rng, r=30, squash=.6)
    out += text('in a Web Worker', 670, 222, 26, 'middle', SOFT, rot=0)

    out += text('serveInServiceWorker()', 30, 262, 32, rot=0)
    y0, y1 = 290, 400
    out += box(30, y0, 190, y1, rng) + text('the page', 110, 356, 32, 'middle', rot=0)
    out += box(290, y0, 490, y1, rng) + text('service worker', 390, 336, 32, 'middle', rot=0)
    out += text('PHP', 390, 382, 30, 'middle', CREAM, rot=0) + ring(390, 372, rng, r=30, squash=.6)
    out += text('Chrome stops it', 530, 340, 28, fill=CREAM)
    out += text('after 30 s of nothing', 530, 372, 28, fill=CREAM)

    for (x0, x1), y in (((190, 290), 135), ((490, 590), 135), ((190, 290), 345)):
        out += arrow(x0 + 8, y - 18, x1 - 8, y - 18, rng, PALE, bend=.1)
        out += arrow(x1 - 8, y + 18, x0 + 8, y + 18, rng, PALE, bend=.1)
    return out


draw(__file__, 'layers', 41, layers)
draw(__file__, 'symfony', 42, symfony)
