# blog.lyrixx.info

The source of [my blog](https://blog.lyrixx.info/), built with [Hugo](https://gohugo.io/).

## Usage

You need [mise](https://mise.jdx.dev/) and [Castor](https://castor.jolicode.com/):

```bash
mise install
castor site:preview
```

`castor list` shows the other tasks: creating an article, drawing its figures, building the site.

## Deployment

Each push on `main` builds the site and publishes it on GitHub Pages. An article goes online when
it loses its `draft: true`.
