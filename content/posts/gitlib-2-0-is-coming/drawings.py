"""Why libgit2 is slow on a long history: the commit-graph it ignores, and the renames its blame looks for."""
import math

from blueprint import CREAM, PALE, SOFT, arrow, dot, draw, file_icon, head, ring, stroke, text


def commit_graph(rng):
    left, right, top, bottom = 46, 300, 94, 400
    cx = (left + right) / 2
    fold = 30
    out = text('commit-graph', cx, 72, 38, 'middle')
    out += stroke([(left, top), (right - fold, top), (right, top + fold), (right, bottom), (left, bottom), (left, top)], rng)
    out += stroke([(right - fold, top), (right - fold, top + fold), (right, top + fold)], rng, PALE, 2.6, amp=.3)
    for y in (196, 296):
        out += stroke([(left + 16, y), (right - 16, y)], rng, SOFT, 2.4, dash='2 11', amp=.4)
    out += text('commit hashes', cx, 156, 34, 'middle')
    out += text('parents, dates', cx, 258, 34, 'middle')
    out += text('GDA2', cx, 360, 42, 'middle', CREAM, rot=0) + ring(cx, 346, rng, r=74, squash=.49)

    out += text('libgit2 1.9 doesn’t know GDA2,', 342, 116, 32, fill=CREAM)
    out += text('so it ignores the whole file…', 342, 156, 32, fill=CREAM)
    out += arrow(cx + 82, 334, 352, 180, rng, CREAM, bend=.22)

    for y in (226, 274, 322):
        xs = [404 + 46 * k for k in range(8)]
        out += stroke([(xs[0] - 22, y), (xs[-1] + 22, y)], rng, SOFT, 2.4, amp=.6)
        for x in xs:
            out += dot(x, y, PALE, 8)
    out += text('…and opens the 80,000 commits,', 566, 376, 32, 'middle')
    out += text('one by one', 566, 414, 32, 'middle')
    return out


def blame(rng):
    parent, commit = 300, 670
    out = text('back in time', (parent + commit) / 2, 46, 32, 'middle', SOFT, rot=0)
    out += stroke([(commit - 40, 62), (parent + 40, 62)], rng, SOFT, 2.4, amp=.4) + head(parent + 40, 62, math.pi, rng, SOFT)
    out += text('the parent', parent, 104, 30, 'middle', SOFT) + text('the commit', commit, 104, 30, 'middle', SOFT)

    y = 170
    out += text('git', 44, y + 12, 42)
    out += file_icon(parent, y, rng) + file_icon(commit, y, rng)
    out += text('Process.php', parent, y + 62, 26, 'middle', SOFT) + text('Process.php', commit, y + 62, 26, 'middle', SOFT)
    out += arrow(commit - 36, y, parent + 36, y, rng, CREAM, bend=.12, width=2.8)
    out += text('same name: found', (parent + commit) / 2, y + 58, 32, 'middle', CREAM)

    y = 312
    out += text('libgit2', 44, y + 12, 42)
    files = [(x, y + dy + 6 * (i % 2)) for dy, xs in ((-44, (212, 264, 316, 368)), (4, (230, 284, 338)), (52, (212, 266, 320, 374))) for i, x in enumerate(xs)]
    for x, file_y in files:
        out += stroke([(commit - 30, y), (x + 15, file_y)], rng, CREAM, 1.5, opacity=.5, amp=.4)
    for x, file_y in files:
        out += file_icon(x, file_y, rng, 26, 34, PALE, 2.2, lines=False)
    out += file_icon(commit, y, rng)
    out += text('compares every changed file,', 752, 378, 30, 'end', CREAM)
    out += text('looking for a rename', 752, 410, 30, 'end', CREAM)
    return out


draw(__file__, 'commit-graph', 21, commit_graph)
draw(__file__, 'blame', 22, blame)
