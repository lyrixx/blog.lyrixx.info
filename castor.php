<?php

defined('CASTOR_USE_CHDIR') || define('CASTOR_USE_CHDIR', true);

use Blog\PlantPrinter;
use Castor\Attribute\AsArgument;
use Castor\Attribute\AsOption;
use Castor\Attribute\AsTask;
use Symfony\Component\Process\Process;

use function Castor\context;
use function Castor\finder;
use function Castor\fs;
use function Castor\import;
use function Castor\io;
use function Castor\run;
use function Castor\yaml_dump;
use function Castor\yaml_parse;

import(__DIR__.'/.castor/PlantPrinter.php');

// fontTools writes the paths of the handwritten text, and resvg renders the covers: another version could change what is published.
const DRAWING_PYTHON = ['mise', 'exec', '--', 'uv', 'run', '--quiet', '--no-project', '--with', 'brotli==1.2.1', '--with', 'fonttools==4.68.0', '--with', 'resvg-py==0.5.0', 'python3'];
const DRAWINGS_DIRECTORY = __DIR__.'/drawings';
const HUGO = ['mise', 'exec', '--', 'hugo'];
const POSTS_DIRECTORY = __DIR__.'/content/posts';

#[AsTask(name: 'new', namespace: 'post', description: 'Creates a draft article with its plant')]
function post_new(
    #[AsArgument(description: 'The title of the article')]
    string $title,
    #[AsArgument(description: 'The fake Latin name of the plant')]
    string $latin,
    #[AsOption(description: 'The style of the plant, the next one of the cycle by default', autocomplete: PlantPrinter::STYLES)]
    ?string $style = null,
    #[AsOption(description: 'The date of the article, today by default')]
    string $date = 'now',
): void {
    io()->title('Creating a draft article');

    $date = new DateTimeImmutable($date)->format('Y-m-d');
    $slug = trim(preg_replace('/[^a-z0-9]+/', '-', strtolower($title)), '-');
    $directory = POSTS_DIRECTORY.'/'.$slug;
    if (is_dir($directory)) {
        throw new RuntimeException(\sprintf('The article "%s" already exists.', $slug));
    }

    $style ??= PlantPrinter::STYLES[\count(iterator_to_array(post_files())) % \count(PlantPrinter::STYLES)];
    print_plant($directory, $slug, $style);

    $frontMatter = yaml_dump([
        'title' => $title,
        'date' => $date,
        'draft' => true,
        'latin' => $latin,
        'style' => $style,
    ]);
    fs()->dumpFile($directory.'/index.md', "---\n{$frontMatter}---\n\n");
    draw_cover($directory, $title, $latin);

    io()->success([
        \sprintf('Created content/posts/%s/index.md, with a plant of style "%s".', $slug, $style),
        'Run "castor site:preview" to see it.',
    ]);
}

#[AsTask(name: 'prints', namespace: 'post', description: 'Prints the plants that are missing')]
function post_prints(
    #[AsOption(description: 'Also reprints existing plants. A published plant should not change')]
    bool $force = false,
): void {
    io()->title('Printing the plants');

    $printed = print_plants($force);

    io()->success(0 === $printed ? 'All the plants are already printed.' : \sprintf('Printed %s.', plural($printed, 'plant')));
}

#[AsTask(name: 'covers', namespace: 'post', description: 'Draws the sharing images that are missing, and the ones of the drafts')]
function post_covers(
    #[AsOption(description: 'Also redraws the existing covers of published articles. They should not change')]
    bool $force = false,
): void {
    io()->title('Drawing the covers');

    $drawn = draw_published_covers($force) + draw_draft_covers();

    io()->success(0 === $drawn ? 'All the covers are already drawn.' : \sprintf('Drew %s.', plural($drawn, 'cover')));
}

#[AsTask(name: 'generate', namespace: 'plant', description: 'Writes the SVG of a plant on STDOUT')]
function plant_generate(
    #[AsArgument(description: 'The style of the plant', autocomplete: PlantPrinter::STYLES)]
    string $style,
    #[AsArgument(description: 'The same seed gives the same plant, random by default')]
    ?string $seed = null,
    #[AsOption(description: 'Draws the plant alone, without the blue sheet')]
    bool $alone = false,
): void {
    // Everything but the SVG goes to STDERR, so that STDOUT can be redirected to a file.
    $io = io()->getErrorStyle();
    $io->title('Generating a plant');

    $seed ??= bin2hex(random_bytes(4));
    $printer = new PlantPrinter();
    $svg = $alone ? $printer->plant($style, $seed) : $printer->print($style, $seed);

    $io->writeln(\sprintf('Style <info>%s</info>, seed <info>%s</info>', $style, $seed));
    echo $svg, \PHP_EOL;
}

#[AsTask(name: 'build', namespace: 'drawing', description: 'Draws the SVG drawings of an article, from its drawings.py')]
function drawing_build(
    #[AsArgument(description: 'The slug of the article')]
    string $slug,
): void {
    io()->title('Drawing the figures of an article');

    $script = POSTS_DIRECTORY.'/'.$slug.'/drawings.py';
    if (!is_file($script)) {
        throw new RuntimeException(\sprintf('The article "%s" has no drawings.py.', $slug));
    }

    run_drawing_python($script);

    io()->success(\sprintf('Drew the figures of "%s". A published drawing should not change: check "git diff".', $slug));
}

#[AsTask(name: 'build', namespace: 'site', description: 'Builds the site in public/, without the drafts')]
function site_build(): int
{
    io()->title('Building the site');

    print_plants();
    draw_published_covers();

    io()->writeln('Running Hugo...');
    // Not "hugo --quiet", which also hides the errors.
    $process = run([...HUGO, '--cleanDestinationDir', '--gc', '--minify'], context()->withAllowFailure()->withPty(false)->withQuiet());
    if (!$process->isSuccessful()) {
        io()->write($process->getErrorOutput());
        io()->error('Hugo could not build the site.');

        return 1;
    }

    $articles = \count(glob(__DIR__.'/public/posts/*/index.html'));
    io()->success(\sprintf('Built %s in public/, %s left out.', plural($articles, 'article'), plural(count_drafts(), 'draft')));

    return 0;
}

#[AsTask(name: 'preview', namespace: 'site', description: 'Builds the site with its drafts and serves it with live reload')]
function site_preview(
    #[AsOption(description: 'The port to listen on')]
    int $port = 1313,
): int {
    io()->title('Previewing the site');

    print_plants();
    draw_published_covers();
    draw_draft_covers();

    if (!is_port_free($port)) {
        $freePort = $port + 1;
        while (!is_port_free($freePort)) {
            ++$freePort;
        }

        if (!io()->confirm(\sprintf('Port %d is already taken. Use port %d instead?', $port, $freePort))) {
            return 0;
        }
        $port = $freePort;
    }

    $url = 'http://localhost:'.$port;
    io()->writeln(\sprintf('Starting the server on <info>%s</info>...', $url));

    // Hugo's startup logs are hidden, except its errors. Once the server is up, its logs show the rebuilds.
    $isReady = false;
    $showHugoOutput = static function (string $type, string $output) use (&$isReady, $url): void {
        if ($isReady || Process::ERR === $type) {
            io()->write($output);

            return;
        }

        if (str_contains($output, 'Web Server is available')) {
            $isReady = true;
            $articles = \count(iterator_to_array(post_files()));
            io()->success([
                \sprintf('The site is ready on %s', $url),
                \sprintf('%s, including %s.', plural($articles, 'article'), plural(count_drafts(), 'draft')),
            ]);
        }
    };

    // Rendered in memory, so that a draft can never end up in public/.
    run(
        [...HUGO, 'server', '--buildDrafts', '--port', (string) $port, '--renderToMemory'],
        context()->withAllowFailure()->withPty(false)->withTimeout(null),
        $showHugoOutput,
    );

    if (!$isReady) {
        io()->error('Hugo could not start the server.');

        return 1;
    }

    return 0;
}

function is_port_free(int $port): bool
{
    $socket = @stream_socket_server('tcp://127.0.0.1:'.$port);
    if (false === $socket) {
        return false;
    }
    fclose($socket);

    return true;
}

function print_plants(bool $force = false): int
{
    $printed = 0;
    foreach (post_files() as $file) {
        $directory = $file->getPath();
        if (!$force && is_file($directory.'/print.svg') && is_file($directory.'/plant.svg')) {
            continue;
        }

        $frontMatter = read_front_matter($file->getPathname());
        print_plant($directory, basename($directory), $frontMatter['style']);
        io()->writeln(\sprintf('Printed %s', basename($directory)));
        ++$printed;
    }

    return $printed;
}

function print_plant(string $directory, string $slug, string $style): void
{
    $printer = new PlantPrinter();

    fs()->dumpFile($directory.'/print.svg', $printer->print($style, $slug));
    fs()->dumpFile($directory.'/plant.svg', $printer->plant($style, $slug));
}

function draw_published_covers(bool $force = false): int
{
    $drawn = 0;
    foreach (post_files() as $file) {
        $frontMatter = read_front_matter($file->getPathname());
        if (($frontMatter['draft'] ?? false) || (!$force && is_file($file->getPath().'/cover.png'))) {
            continue;
        }

        draw_cover($file->getPath(), $frontMatter['title'], $frontMatter['latin']);
        ++$drawn;
    }

    return $drawn;
}

// A draft can still change its title, so its cover is always redrawn.
function draw_draft_covers(): int
{
    $drawn = 0;
    foreach (post_files() as $file) {
        $frontMatter = read_front_matter($file->getPathname());
        if ($frontMatter['draft'] ?? false) {
            draw_cover($file->getPath(), $frontMatter['title'], $frontMatter['latin']);
            ++$drawn;
        }
    }

    return $drawn;
}

function draw_cover(string $directory, string $title, string $latin): void
{
    run_drawing_python(DRAWINGS_DIRECTORY.'/cover.py', $directory, $title, $latin);
    io()->writeln(\sprintf('Drew the cover of %s', basename($directory)));
}

function run_drawing_python(string ...$arguments): void
{
    run([...DRAWING_PYTHON, ...$arguments], context()->withEnvironment([
        // Keeps drawings/ free of __pycache__.
        'PYTHONDONTWRITEBYTECODE' => '1',
        'PYTHONPATH' => DRAWINGS_DIRECTORY,
    ]));
}

function count_drafts(): int
{
    $drafts = 0;
    foreach (post_files() as $file) {
        if (read_front_matter($file->getPathname())['draft'] ?? false) {
            ++$drafts;
        }
    }

    return $drafts;
}

/**
 * @return iterable<SplFileInfo>
 */
function post_files(): iterable
{
    return is_dir(POSTS_DIRECTORY) ? finder()->files()->in(POSTS_DIRECTORY)->name('index.md') : [];
}

function plural(int $count, string $noun): string
{
    return \sprintf('%d %s%s', $count, $noun, 1 === $count ? '' : 's');
}

/**
 * @return array{draft?: bool, latin: string, style: string, title: string}
 */
function read_front_matter(string $path): array
{
    if (!preg_match('/\A---\n(.*?)\n---\n/s', file_get_contents($path), $matches)) {
        throw new RuntimeException(\sprintf('"%s" has no YAML front matter.', $path));
    }

    $frontMatter = yaml_parse($matches[1]);
    foreach (['latin', 'style', 'title'] as $field) {
        if (!isset($frontMatter[$field])) {
            throw new RuntimeException(\sprintf('"%s" has no "%s" in its front matter.', $path, $field));
        }
    }

    return $frontMatter;
}
