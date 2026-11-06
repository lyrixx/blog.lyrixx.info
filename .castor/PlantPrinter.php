<?php

namespace Blog;

use Random\Engine\Mt19937;
use Random\Randomizer;

/**
 * Draws plants. The same style and the same seed always give the same drawing.
 */
final readonly class PlantPrinter
{
    // New articles cycle through this list: neighbours must look different, so it is not sorted.
    public const array STYLES = ['sprig', 'algae', 'branch', 'umbel', 'ginkgo', 'fern'];

    private const int HEIGHT = 380;
    private const int MARGIN = 7;
    private const string PALE = '#eaf1f8';
    // The plant stops above the bottom of the sheet, like a real specimen.
    private const int PLANT_HEIGHT = 334;
    private const int WIDTH = 300;

    /**
     * The whole cyanotype: the blue sheet and the plant.
     */
    public function print(string $style, string $seed): string
    {
        $number = crc32($seed);
        $edge = $this->raggedEdge(new Randomizer(new Mt19937($number + 1)));
        $width = self::WIDTH;
        $height = self::HEIGHT;
        $wash = $number % 997;
        $grain = $number % 991;
        $plant = $this->draw($style, $seed, 1);

        return <<<SVG
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 {$width} {$height}"><defs><radialGradient id="g" cx=".3" cy=".15" r="1.05"><stop offset="0" stop-color="#1f5592"/><stop offset=".5" stop-color="#143f74"/><stop offset="1" stop-color="#0c2b54"/></radialGradient><clipPath id="c"><path d="{$edge}"/></clipPath><filter id="wash" x="0" y="0" width="100%" height="100%"><feTurbulence type="fractalNoise" baseFrequency=".011 .019" numOctaves="3" seed="{$wash}"/><feColorMatrix values="0 0 0 0 .03 0 0 0 0 .11 0 0 0 0 .25 1.5 0 0 0 -.52"/></filter><filter id="grain" x="0" y="0" width="100%" height="100%"><feTurbulence type="fractalNoise" baseFrequency=".85" numOctaves="2" seed="{$grain}"/><feColorMatrix values="0 0 0 0 .82 0 0 0 0 .89 0 0 0 0 .96 .9 0 0 0 -.38"/></filter><filter id="soft"><feGaussianBlur stdDeviation=".22"/></filter></defs><path d="{$edge}" fill="url(#g)"/><g clip-path="url(#c)"><rect width="{$width}" height="{$height}" filter="url(#wash)"/><g opacity=".95" filter="url(#soft)">{$plant}</g><rect width="{$width}" height="{$height}" filter="url(#grain)" opacity=".16"/></g></svg>
            SVG;
    }

    /**
     * The same plant alone, with thinner lines because it is displayed much larger.
     */
    public function plant(string $style, string $seed): string
    {
        return \sprintf(
            '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 %d %d">%s</svg>',
            self::WIDTH,
            self::PLANT_HEIGHT,
            $this->draw($style, $seed, .5),
        );
    }

    private function draw(string $style, string $seed, float $line): string
    {
        $random = new Randomizer(new Mt19937(crc32($seed)));

        return match ($style) {
            'algae' => $this->algae($random, $line),
            'branch' => $this->branch($random, $line),
            'fern' => $this->fern($random, $line),
            'ginkgo' => $this->ginkgo($random, $line),
            'sprig' => $this->sprig($random),
            'umbel' => $this->umbel($random, $line),
            default => throw new \InvalidArgumentException(\sprintf('Unknown style "%s", expected one of: %s.', $style, implode(', ', self::STYLES))),
        };
    }

    private function algae(Randomizer $random, float $line): string
    {
        $width = self::WIDTH;
        $height = self::PLANT_HEIGHT;
        $layers = [];
        foreach ([-.3, .02, .32] as $offset) {
            $this->growAlgae($random, $layers, $width * .5 + $offset * 18, $height + 6, -M_PI / 2 + $offset, $height * $random->getFloat(.17, .21), 6);
        }
        ksort($layers);

        $paths = '';
        foreach ($layers as $depth => $path) {
            $paths .= \sprintf('<path d="%s" fill="none" stroke="%s" stroke-width="%.2F" stroke-linecap="round" stroke-linejoin="round"/>', $path, self::PALE, (.75 + $depth * .42) * $line);
        }

        return $paths;
    }

    /**
     * @param array<int, string> $layers
     */
    private function growAlgae(Randomizer $random, array &$layers, float $x, float $y, float $angle, float $length, int $depth): void
    {
        $path = 'M'.$this->point($x, $y);
        for ($i = 0; $i < 6; ++$i) {
            $angle += $random->getFloat(-.17, .17);
            $x += $length / 6 * cos($angle);
            $y += $length / 6 * sin($angle);
            $path .= 'L'.$this->point($x, $y);
        }
        $layers[$depth] = ($layers[$depth] ?? '').$path;
        if (0 === $depth) {
            return;
        }

        $count = $depth > 2 && $random->getFloat(0, 1) < .3 ? 3 : 2;
        for ($i = 0; $i < $count; ++$i) {
            $spread = ($i - ($count - 1) / 2) * $random->getFloat(.46, .7);
            $this->growAlgae($random, $layers, $x, $y, $angle + $spread, $length * $random->getFloat(.72, .88), $depth - 1);
        }
    }

    private function branch(Randomizer $random, float $line): string
    {
        $width = self::WIDTH;
        $height = self::PLANT_HEIGHT;
        $curve = [
            [$width * $random->getFloat(.25, .4), $height + 10],
            [$width * $random->getFloat(.1, .25), $height * .62],
            [$width * $random->getFloat(.75, .9), $height * .42],
            [$width * $random->getFloat(.52, .7), $height * .05],
        ];

        $leaves = '';
        $ribs = '';
        for ($i = 0; $i < 16; ++$i) {
            $t = .05 + .93 * $i / 15;
            [$x, $y] = $this->bezier($curve, $t);
            [$nextX, $nextY] = $this->bezier($curve, min(1, $t + .01));
            $side = $i % 2 ? 1 : -1;
            $length = (88 - 54 * $t) * $random->getFloat(.9, 1.1);
            $angle = atan2($nextY - $y, $nextX - $x) + $side * $random->getFloat(.6, .9);
            $leaves .= $this->leaf($x, $y, $angle, $length, $length * .32);
            $ribs .= 'M'.$this->point($x, $y).'L'.$this->point($x + $length * .86 * cos($angle), $y + $length * .86 * sin($angle));
        }

        return \sprintf(
            '<path d="%s" fill="%s"/><path d="%s" fill="none" stroke="#143f74" stroke-width="%.2F"/><path d="%s" fill="none" stroke="%2$s" stroke-width="%.2F" stroke-linecap="round"/>',
            $leaves,
            self::PALE,
            $ribs,
            1.1 * $line,
            $this->curvePath($curve),
            2.6 * $line,
        );
    }

    private function fern(Randomizer $random, float $line): string
    {
        $width = self::WIDTH;
        $height = self::PLANT_HEIGHT;
        $x = $width * $random->getFloat(.42, .6);
        $angle = -M_PI / 2 + $random->getFloat(-.2, .2);
        $curl = (0 === $random->getInt(0, 1) ? -1 : 1) * $random->getFloat(.5, 1);
        [$leaflets, $stem] = $this->frond($random, $x, $height + 10, $angle, $height * 1.02, $curl, 17);

        return \sprintf(
            '<path d="%s" fill="%s" stroke="%2$s" stroke-width="%.2F"/><path d="%s" fill="none" stroke="%2$s" stroke-width="%.2F" stroke-linecap="round"/>',
            $leaflets,
            self::PALE,
            1.5 * $line,
            $stem,
            2.6 * $line,
        );
    }

    /**
     * @return array{string, string} The leaflets and the stem
     */
    private function frond(Randomizer $random, float $x, float $y, float $angle, float $length, float $curl, int $pairs): array
    {
        $leaflets = '';
        $stem = 'M'.$this->point($x, $y);
        $step = $length / $pairs;
        for ($i = 0; $i < $pairs; ++$i) {
            $t = $i / ($pairs - 1);
            $angle += $curl * (.25 + 1.5 * $t * $t) / $pairs;
            $x += $step * cos($angle);
            $y += $step * sin($angle);
            $stem .= 'L'.$this->point($x, $y);
            $pinna = $length * .27 * sin(M_PI * min(1, .16 + $t * .9)) ** .75 * (1 - .35 * $t) * $random->getFloat(.94, 1.05);
            foreach ([-1, 1] as $side) {
                $pinnaAngle = $angle + $side * (1.22 - .5 * $t);
                $pinnaX = $x;
                $pinnaY = $y;
                $count = max(3, (int) ($pinna / 11));
                for ($j = 0; $j < $count; ++$j) {
                    $pinnaAngle -= $side * .016;
                    $pinnaX += $pinna / $count * cos($pinnaAngle);
                    $pinnaY += $pinna / $count * sin($pinnaAngle);
                    $size = $pinna * .115 * (1 - $j / $count * .7) + 2;
                    foreach ([-1, 1] as $leafletSide) {
                        $leaflets .= $this->leaf($pinnaX, $pinnaY, $pinnaAngle + $leafletSide * .85, $size, $size * .22);
                    }
                }
                $leaflets .= $this->leaf($x, $y, $pinnaAngle, $pinna, .9);
            }
        }

        return [$leaflets, $stem];
    }

    private function ginkgo(Randomizer $random, float $line): string
    {
        $width = self::WIDTH;
        $height = self::PLANT_HEIGHT;
        $curve = [
            [$width * $random->getFloat(.3, .4), $height + 8],
            [$width * .24, $height * .6],
            [$width * .8, $height * .44],
            [$width * $random->getFloat(.54, .62), $height * .2],
        ];

        $leaves = '';
        $veins = '';
        $stems = '';
        for ($i = 0; $i < 8; ++$i) {
            $t = .14 + .86 * $i / 7;
            [$x, $y] = $this->bezier($curve, $t);
            $side = $i % 2 ? 1 : -1;
            $angle = -M_PI / 2 + $side * $random->getFloat(.75, 1.2) * (1 - .45 * $t);
            $petiole = $random->getFloat(26, 42);
            $leafX = $x + $petiole * cos($angle);
            $leafY = $y + $petiole * sin($angle);
            $stems .= 'M'.$this->point($x, $y).'L'.$this->point($leafX, $leafY);
            [$leaf, $leafVeins] = $this->ginkgoLeaf($random, $leafX, $leafY, $angle, min($width, $height) * .205 * (1.08 - .42 * $t) * $random->getFloat(.92, 1.08));
            $leaves .= $leaf;
            $veins .= $leafVeins;
        }

        return \sprintf(
            '<path d="%s" fill="%s"/><path d="%s" fill="none" stroke="#143f74" stroke-width="%.2F" opacity=".55"/><path d="%s%s" fill="none" stroke="%2$s" stroke-width="%.2F" stroke-linecap="round"/>',
            $leaves,
            self::PALE,
            $veins,
            .7 * $line,
            $this->curvePath($curve),
            $stems,
            2.2 * $line,
        );
    }

    /**
     * @return array{string, string} The outline and the veins
     */
    private function ginkgoLeaf(Randomizer $random, float $x, float $y, float $angle, float $radius): array
    {
        $spread = $random->getFloat(.95, 1.2);
        $steps = 22;
        $outline = 'M'.$this->point($x, $y);
        $veins = '';
        for ($i = 0; $i <= $steps; ++$i) {
            $u = $i / $steps;
            $rayAngle = $angle - $spread + 2 * $spread * $u;
            $notch = 1 - .26 * exp(-((($u - .5) / .035) ** 2));
            $ripple = 1 + .025 * sin($u * 34 + $radius);
            $reach = $radius * (.8 + .2 * sin(M_PI * $u)) * $notch * $ripple;
            $outline .= 'L'.$this->point($x + $reach * cos($rayAngle), $y + $reach * sin($rayAngle));
            if ($i % 2 && $i < $steps) {
                $veins .= 'M'.$this->point($x + $reach * .16 * cos($rayAngle), $y + $reach * .16 * sin($rayAngle));
                $veins .= 'L'.$this->point($x + $reach * .93 * cos($rayAngle), $y + $reach * .93 * sin($rayAngle));
            }
        }

        return [$outline.'Z', $veins];
    }

    private function sprig(Randomizer $random): string
    {
        $width = self::WIDTH;
        $height = self::PLANT_HEIGHT;
        $baseX = $width * $random->getFloat(.44, .56);
        $baseY = $height + 8;
        $path = '';
        for ($i = 0; $i < 12; ++$i) {
            $angle = -M_PI / 2 + $random->getFloat(-.56, .56);
            $length = $height * $random->getFloat(.62, 1);
            $bend = $random->getFloat(-62, 62);
            $tipX = $baseX + $length * cos($angle) + $bend * .55;
            $tipY = $baseY + $length * sin($angle);
            $middleX = $baseX + $length * .55 * cos($angle) + $bend;
            $middleY = $baseY + $length * .55 * sin($angle);
            $thickness = $random->getFloat(2.6, 5);
            $path .= 'M'.$this->point($baseX, $baseY).'Q'.$this->point($middleX + $thickness, $middleY).' '.$this->point($tipX, $tipY);
            $path .= 'Q'.$this->point($middleX - $thickness, $middleY).' '.$this->point($baseX, $baseY).'Z';
            if ($random->getFloat(0, 1) >= .6) {
                continue;
            }
            for ($j = 0; $j < 10; ++$j) {
                $t = .74 + .26 * $j / 9;
                $x = (1 - $t) ** 2 * $baseX + 2 * (1 - $t) * $t * $middleX + $t * $t * $tipX;
                $y = (1 - $t) ** 2 * $baseY + 2 * (1 - $t) * $t * $middleY + $t * $t * $tipY;
                $direction = atan2($tipY - $middleY, $tipX - $middleX);
                foreach ([-1, 1] as $side) {
                    $path .= $this->leaf($x, $y, $direction + $side * .55, 13, 3);
                }
            }
        }

        return \sprintf('<path d="%s" fill="%s"/>', $path, self::PALE);
    }

    private function umbel(Randomizer $random, float $line): string
    {
        $width = self::WIDTH;
        $height = self::PLANT_HEIGHT;
        $x = $width * $random->getFloat(.45, .55);
        $top = $height * .47;
        $lines = \sprintf('M%s Q%s %s', $this->point($x + $random->getFloat(-30, 30), $height + 10), $this->point($x + $random->getFloat(-30, 30), $height * .72), $this->point($x, $top));
        $dots = '';
        $rays = 17;
        for ($i = 0; $i < $rays; ++$i) {
            $u = ($i + .5) / $rays;
            $angle = -M_PI * (.97 - .94 * $u);
            $length = $height * .35 * (.76 + .24 * sin(M_PI * $u)) * $random->getFloat(.95, 1.05);
            $endX = $x + $length * cos($angle);
            $endY = $top + $length * sin($angle);
            $lines .= 'M'.$this->point($x, $top).'L'.$this->point($endX, $endY);
            for ($j = 0; $j < 7; ++$j) {
                $spokeAngle = $angle - .7 + 1.4 * $j / 6;
                $spoke = $length * $random->getFloat(.13, .19);
                $spokeX = $endX + $spoke * cos($spokeAngle);
                $spokeY = $endY + $spoke * sin($spokeAngle);
                $lines .= 'M'.$this->point($endX, $endY).'L'.$this->point($spokeX, $spokeY);
                $dots .= \sprintf('<circle cx="%.1F" cy="%.1F" r="%.1F"/>', $spokeX, $spokeY, $random->getFloat(2, 3.3) * $line);
            }
        }

        return \sprintf(
            '<path d="%s" fill="none" stroke="%s" stroke-width="%.2F" stroke-linecap="round"/><g fill="%2$s">%s</g>',
            $lines,
            self::PALE,
            1.3 * $line,
            $dots,
        );
    }

    private function raggedEdge(Randomizer $random): string
    {
        $width = self::WIDTH;
        $height = self::HEIGHT;
        $margin = self::MARGIN;
        $across = 72;
        $down = 92;
        $points = [];
        foreach ($this->wobble($random, $across) as $i => $offset) {
            $points[] = $this->point($margin + ($width - 2 * $margin) * $i / $across, $margin + $offset);
        }
        foreach ($this->wobble($random, $down) as $i => $offset) {
            $points[] = $this->point($width - $margin + $offset, $margin + ($height - 2 * $margin) * $i / $down);
        }
        foreach ($this->wobble($random, $across) as $i => $offset) {
            $points[] = $this->point($width - $margin - ($width - 2 * $margin) * $i / $across, $height - $margin + $offset);
        }
        foreach ($this->wobble($random, $down) as $i => $offset) {
            $points[] = $this->point($margin + $offset, $height - $margin - ($height - 2 * $margin) * $i / $down);
        }

        return 'M'.implode('L', $points).'Z';
    }

    /**
     * @return list<float>
     */
    private function wobble(Randomizer $random, int $count): array
    {
        $phases = [$random->getFloat(0, 6.28), $random->getFloat(0, 6.28), $random->getFloat(0, 6.28)];
        $offsets = [];
        for ($i = 0; $i < $count; ++$i) {
            $offsets[] = 1.5 * sin($i * .21 + $phases[0]) + sin($i * .57 + $phases[1]) + .6 * sin($i * 1.4 + $phases[2]) + $random->getFloat(-.4, .4);
        }

        return $offsets;
    }

    /**
     * @param list<array{float, float}> $curve
     *
     * @return array{float, float}
     */
    private function bezier(array $curve, float $t): array
    {
        $u = 1 - $t;
        $at = static fn (int $axis): float => $u ** 3 * $curve[0][$axis] + 3 * $u * $u * $t * $curve[1][$axis] + 3 * $u * $t * $t * $curve[2][$axis] + $t ** 3 * $curve[3][$axis];

        return [$at(0), $at(1)];
    }

    /**
     * @param list<array{float, float}> $curve
     */
    private function curvePath(array $curve): string
    {
        return \sprintf('M%sC%s %s %s', $this->point(...$curve[0]), $this->point(...$curve[1]), $this->point(...$curve[2]), $this->point(...$curve[3]));
    }

    private function leaf(float $x, float $y, float $angle, float $length, float $width): string
    {
        $tipX = $x + $length * cos($angle);
        $tipY = $y + $length * sin($angle);
        $middleX = $x + $length * .5 * cos($angle);
        $middleY = $y + $length * .5 * sin($angle);
        $normalX = -sin($angle) * $width;
        $normalY = cos($angle) * $width;

        return 'M'.$this->point($x, $y)
            .'Q'.$this->point($middleX + $normalX, $middleY + $normalY).' '.$this->point($tipX, $tipY)
            .'Q'.$this->point($middleX - $normalX, $middleY - $normalY).' '.$this->point($x, $y).'Z';
    }

    private function point(float $x, float $y): string
    {
        return \sprintf('%.1F %.1F', $x, $y);
    }
}
