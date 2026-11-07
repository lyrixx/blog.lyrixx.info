"""Shamir's secret sharing: a secret on the vertical axis, and the pieces on a curve."""
import math

from blueprint import CREAM, PALE, SOFT, W, arrow, dot, draw, head, ring, stroke, text

AX, AY = 120, 385  # the origin


def axes(rng):
    return (
        stroke([(AX, AY + 22), (AX, 28)], rng, SOFT, 2.6, amp=.5) + head(AX, 28, -math.pi / 2, rng, SOFT, 13, 2.6)
        + stroke([(AX - 22, AY), (W - 34, AY)], rng, SOFT, 2.6, amp=.5) + head(W - 34, AY, 0, rng, SOFT, 13, 2.6)
    )


def line_y(x, x0, y0, slope):
    return y0 + slope * (x - x0)


def line(rng):
    y = lambda x: line_y(x, AX, 275, -.31)
    people = [(300, 'John'), (470, 'Jane'), (640, 'Bob')]
    out = axes(rng) + stroke([(AX, y(AX)), (735, y(735))], rng)
    for x, name in people:
        out += dot(x, y(x)) + text(name, x + 2, y(x) + 44, 36, 'middle')
    out += dot(AX, y(AX), CREAM, 10) + ring(AX, y(AX), rng)
    out += text('the secret', 150, 128, 40, fill=CREAM) + arrow(186, 142, AX + 16, y(AX) - 26, rng, CREAM)
    out += text('any two points give the line', 745, 350, 32, 'end', SOFT)
    return out


def one_point(rng):
    px, py = 470, 190
    crossings = [62, 128, 196, 262, 330]
    out = axes(rng)
    for cy in crossings:
        slope = (py - cy) / (px - AX)
        out += stroke([(AX, cy), (745, line_y(745, px, py, slope))], rng, PALE, 2.4, opacity=.72)
        out += text('?', AX - 34, cy + 12, 40, fill=CREAM, rot=0)
    out += dot(px, py, PALE, 11) + text('Jane’s piece', px, py - 52, 36, 'middle')
    out += text('one point, an infinity of lines', 745, 368, 32, 'end', SOFT)
    return out


def parabola(rng):
    k = 215 / (310 * 310)
    y = lambda x: 330 - k * (x - AX) * (740 - x) * 1.12
    out = axes(rng)
    out += stroke([(x, y(x)) for x in range(AX, 741, 10)], rng)
    out += dot(260, y(260)) + text('John', 248, y(260) - 16, 36, 'end')
    out += dot(430, y(430)) + text('Jane', 430, y(430) + 46, 36, 'middle')
    out += dot(600, y(600)) + text('Bob', 614, y(600) - 16, 36)
    out += dot(AX, y(AX), CREAM, 10) + ring(AX, y(AX), rng)
    out += text('the secret', 196, 344, 38, fill=CREAM) + arrow(188, 332, AX + 28, y(AX) + 3, rng, CREAM)
    out += text('a threshold of 3: three points give the curve', 752, 372, 30, 'end', SOFT, rot=0)
    return out


def fake_piece(rng):
    px, py = 470, 175
    real, fake = 300, 112
    slope_real = (py - real) / (px - AX)
    slope_fake = (py - fake) / (px - AX)
    out = axes(rng)
    out += stroke([(AX, real), (735, line_y(735, px, py, slope_real))], rng, SOFT, 2.6, dash='4 12', opacity=.9)
    out += stroke([(AX, fake), (735, line_y(735, px, py, slope_fake))], rng, CREAM, 3.4)
    out += dot(AX, real, SOFT, 9) + text('the real secret, ignored', 150, 345, 34, fill=SOFT)
    out += dot(AX, fake, CREAM, 10) + ring(AX, fake, rng) + text('a fake piece, at x = 0:', 160, 54, 36, fill=CREAM, rot=0)
    out += text('it is also the result', 160, 92, 36, fill=CREAM, rot=0)
    out += dot(px, py, PALE, 11) + text('Jane’s real piece', px + 10, py + 74, 36, 'middle')
    return out


draw(__file__, 'shamir-line', 11, line)
draw(__file__, 'shamir-one-point', 12, one_point)
draw(__file__, 'shamir-parabola', 13, parabola)
draw(__file__, 'shamir-fake-piece', 14, fake_piece)
