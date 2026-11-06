---
title: 'gitlib 2.0 is coming'
date: '2026-11-06'
draft: true
latin: 'Historia ramosa'
style: algae
signoff: "Et voilà!"
---

In November 2019, I wrote [an article about FFI in PHP 7.4](https://jolicode.com/blog/php-7-4-ffi-what-you-need-to-know). There was this sentence in the middle:

> This is the case for gitlib that could leverage libgit2 with FFI.

{{< note >}}Seven years. I take my time.{{< /note >}}

Well, it does now 😅. gitlib 2.0 is coming, and it can talk to libgit2 through FFI. It's also a big rewrite: since 1.6.0, `src` and `tests` got 15,276 new lines, and lost 3,865.

Why now? Because I had a bit of time. And Claude Code (yes, I can say it).

Note: 2.0 is not tagged yet. If you want to try it today, everything is on the `main` branch.

## What is gitlib?

It's a PHP library to access git repositories: commits, logs, diffs, blames and branches, as PHP objects. Alexandre Salomé and Julien Didier started it in 2012, and Packagist counts more than 22 million installs. If you use GrumPHP, you already have it in your `vendor`.

Here is what it looks like:

```php
use Gitonomy\Git\Repository;

$repository = new Repository('/path/to/repository');

// The last 5 commits of main that changed src/
foreach ($repository->getLog('main', 'src', limit: 5) as $commit) {
    echo $commit->getShortHash().' '.$commit->getSubjectMessage().PHP_EOL;
}
```

## What is a driver?

In 1.x, the objects started `git` processes and parsed their output themselves. In 2.0, every operation goes through a driver, and gitlib comes with two of them:

- `CliDriver`, the default one, runs the `git` binary, like before
- `LibGit2Driver`, experimental, runs libgit2 1.9 inside your PHP process, through FFI

And switching is just one argument:

```php
use Gitonomy\Git\Driver\Ffi\LibGit2Driver;
use Gitonomy\Git\Repository;

$repository = new Repository('/path/to/repository', driver: new LibGit2Driver());
```

Why would you do that? Because `CliDriver` starts a process for every operation, and it costs a few milliseconds each time. With libgit2, there is no process to start. On the gitlib repository (350 commits), reading a commit goes from 3.1 ms to 0.3 ms, and reading a file from 18.5 ms to 0.7 ms.

Easy, isn't it? So `LibGit2Driver` should be the default driver…

## But wait

On symfony/symfony (80,000 commits), it's another story. Counting the commits of `HEAD` goes from 43 ms to 1,332 ms. Listing the branches that include a commit goes from 16 ms to 912 ms. And blaming `Process.php` goes from 0.6 s to… about 10 minutes.

{{< note >}}Yes, minutes.{{< /note >}}

WTF! This makes no sense! There must be a mistake somewhere.

That's what I said the first time I saw it. But there is no mistake, and there are two reasons. libgit2 doesn't use git's commit-graph, so walking a long history is slow. And its blame diffs trees for every commit it goes through, where git only compares the hash of the file ([libgit2#3027](https://github.com/libgit2/libgit2/issues/3027)).

So `CliDriver` stays the default. Use `LibGit2Driver` only if your application runs many small reads in a long-running process. And don't trust my numbers, try it on your own repository:

```bash
git clone https://github.com/gitonomy/gitlib.git
cd gitlib
composer install
php benchmark.php /path/to/repository path/to/a/file
```

Note: with php-fpm, you have to call `LibGit2Driver::preload()` from your `opcache.preload` script. Otherwise, every request loads libgit2 again (about 10 ms), and leaks about 700 KB.

## What else changed?

The API is typed now: hashes, enums, value objects. Dates are `\DateTimeImmutable`, and a failing operation always throws.

The array of options is deprecated, so you configure the driver instead:

```php
// Before
$repository = new Repository($path, [
    'command' => '/usr/bin/git',
    'logger' => $logger,
    'process_timeout' => 60,
]);

// After
$repository = new Repository($path, driver: new CliDriver(
    command: '/usr/bin/git',
    processTimeout: 60,
    logger: $logger,
));
```

`Admin` is deprecated too. Can you tell what the `false` means here?

```php
// Before
Admin::cloneTo($path, $url, false);

// After
Repository::cloneFrom($url, $path);
```

It was the `$bare` argument. And the default was a bare repository…

## What about security?

What happens if you give a user input to `getRevision()`? With 1.x, git could read it as an option. So `--output=/path` could write a file on your server.

In 2.0, it throws:

```php
$repository->getRevision('--output=/path')->getCommit();
// InvalidArgumentException: "--output=/path" starts with a dash, which git would read as an option.
```

**These fixes are only in 2.0**, not in 1.7. There are others: `ext::` URLs are refused, the credentials of URLs don't leak in exceptions and logs anymore, and a hook name can't leave the hooks directory. The full list is in the `CHANGELOG.md`.

==[TODO Greg: backport to a 1.7.1, or keep this sentence? Today, these fixes are not in 1.7.]==

## How to upgrade?

gitlib 2.0 requires PHP 8.4 and git 1.8.4. But most of the 1.x API keeps working, deprecated until 3.0. So you can do it in two steps:

1. Upgrade to 1.7, released in October. It requires PHP 8.1, it has most of the new API, and it triggers the deprecations
2. Fix them, then require `^1.7 || ^2.0`

If your library supports PHP 8.1 to 8.3, Composer picks 1.7 there, and 2.0 from PHP 8.4, with the same code.

Some changes can't be deprecated. Dates now use the UTC offset of their author, like `git log`, instead of UTC. And most classes are `final`, so you can't mock them anymore. Everything is in `UPGRADE-2.0.md`, with before and after examples.

## Conclusion

If you only read commits from time to time, `CliDriver` does the job, like it always did. If you do thousands of small reads in a worker, try `LibGit2Driver`, and measure. It's up to you.

Thanks to Alexandre and Julien for starting this library, and to everyone who contributed since.
