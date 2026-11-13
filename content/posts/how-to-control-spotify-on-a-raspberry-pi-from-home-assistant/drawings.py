"""Spotify in Home Assistant: the state goes out through MQTT, the commands come back through MPRIS."""
from blueprint import CREAM, PALE, SOFT, arrow, draw, ring, stroke, text


def box(left, top, right, bottom, rng, colour=PALE, width=3.4):
    return stroke([(left, top), (right, top), (right, bottom), (left, bottom), (left, top)], rng, colour, width)


def loop(rng):
    out = box(40, 160, 216, 290, rng) + text('librespot', 128, 236, 38, 'middle', rot=0)
    out += box(574, 160, 740, 290, rng) + text('Home', 657, 220, 36, 'middle', rot=0) + text('Assistant', 657, 258, 36, 'middle', rot=0)
    out += box(346, 192, 444, 258, rng, PALE, 2.8) + text('MQTT', 395, 237, 34, 'middle', rot=0)

    out += text('what is playing', 395, 60, 34, 'middle', SOFT)
    out += arrow(160, 150, 372, 182, rng, PALE, bend=.3)
    out += arrow(418, 182, 630, 150, rng, PALE, bend=.3)
    out += text('--onevent script', 236, 104, 30, 'middle')

    out += arrow(630, 300, 418, 268, rng, PALE, bend=.3)
    out += arrow(372, 268, 160, 300, rng, CREAM, bend=.3, width=2.8)
    out += text('play, pause, volume', 560, 372, 34, 'middle', SOFT)
    out += text('MPRIS', 250, 358, 34, 'middle', CREAM, rot=0) + ring(250, 347, rng, r=62, squash=.5)
    out += text('over D-Bus: the part', 250, 402, 28, 'middle', CREAM)
    out += text('librespot didn’t have', 250, 426, 28, 'middle', CREAM)
    return out


draw(__file__, 'loop', 31, loop)
