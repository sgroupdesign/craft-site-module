# Site Module
A base module for all S. Group sites, featuring a few helpers and scaffolding.

## Offline switch
There's a hard-offline setting that's useful for when taking a staging site offline. It's more heavy-handed than Craft's offline behaviour, as we want to prevent anyone from logging into Craft (to save confusion between environments).

Control it in your `general.php` config.

```php
<?php

return [
    'staging' => [
        'isOffline' => true,
    ],
];
```

## Vite
Adds a `@resource` alias for use in templates. This will resolve to either `@webroot/../resources` if the dev server is running, or `@webroot/dist` if not. The We store our static asset files outside of the webroot.

We also provide a `resource()` Twig function that does a similar thing, but resolves to your Vite plugin settings. `devServerPublic` if the dev server is running, or `serverPublic` if not. In practice this resolves to `http://localhost:3000/` and `/dist` respectively.

## Base module
Yii's modules are pretty slim, and we loose a bunch of boilerplating that Craft plugins get for free. We add this in `base/Module`. Every Yii module for a project you create should extend this **not** Yii's module class.

```php
use sgroup\sitemodule\base\Module;

class SiteMigration extends Module
{
```

## Twig extensions
We provide a good bunch of Twig extensions for better template development.

### `svgPlaceholder($width, $height = null)`
Given a width and height, this will generate a transparent SVG. This is most commonly used for the `src` attribute of `<img>` tags when lazyloading. This creates the correct dimensions for the image, while the real image is being lazyloaded. Doing this prevent a noticable "jump" when going from an image on zero dimensions to the proper one.

### `getFormattedPhone($value)`
An opinionated, AU-based phone formatter. Throw it any phone number and it'll internationalize it, deal with spaces and area codes, ready for use in `<a href="tel:"`

### `getVideo($asset, $settings = [])`
Given an asset, this will render an `<iframe>` or `<video>`. This handles both Embedded Asset plugin videos, or real uploaded videos. For any YouTube-based embedded asset, it'll render an `<iframe>` and embed the video URL.

You can also pass in attributes to be used either in the URL (as URL-encoded params) or in `<video>` attributes. You can include any attribute, but some worth mentioning:

| Option | Description
| - | -
| `muted` | Whether the video should be muted.
| `autoplay` | Whether the video should autoplay.
| `controls` | Whether the video controls should be shown.

### `getImg($image, $transform = null, $lazyload = false, $attributes = [], $sizes = 'auto')`
This will return an `<img>` tag, pre-configured with a bunch of options.

| Option | Description
| - | -
| `image` | The asset.
| `transform` | Either an array (for dynamic transform) or string for the transform.
| `lazyload` | Whether the the image should be lazyloaded.
| `attributes` | A collection of attributes, added to the `<img>` element.
| `sizes` | An array of valid sizes, used for `srcset`.

By default, we use `srcset` to provide `['1x', '1.5x', '2x', '3x']` sizes.

```twig
{% set asset = craft.assets.id(46).one() %}

{# Renders a small image #}
{{ getImg(asset, 'small', false, [], false) }}

<img src=".../image.jpg" srcset=".../image.jpg, .../image.jpg 1.5x, .../image.jpg 2x, .../image.jpg 3x" width="400" height="400" alt="Title" />

{# Renders a small image, lazyloaded #}
{{ getImg(asset, 'small', true) }}

<img class="lazyload" src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAwIiBoZWlnaHQ9IjQwMCIgdmlld0JveD0iMCAwIDQwMCA0MDAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PHBhdGggZD0iTTAgMGg0MDB2NDAwSDB6IiBmaWxsPSJub25lIj48L3BhdGg+PC9zdmc+" width="400" height="400" alt="Title" data-src=".../image.jpg" data-srcset=".../image.jpg, .../image.jpg 1.5x, .../image.jpg 2x, .../image.jpg 3x" />

{# Renders a small image with extra classes and attributes #}
{{ getImg(asset, 'small', true, { class: ['testing', 'some'], 'data-attr': 'val' }) }}

<img class="lazyload testing some" data-attr="val" src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAwIiBoZWlnaHQ9IjQwMCIgdmlld0JveD0iMCAwIDQwMCA0MDAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PHBhdGggZD0iTTAgMGg0MDB2NDAwSDB6IiBmaWxsPSJub25lIj48L3BhdGg+PC9zdmc+" width="400" height="400" alt="Title" data-src=".../image.jpg" />

{# Renders a small image, lazyloaded, no `srcset` #}
{{ getImg(asset, 'small', true, [], false) }}

<img class="lazyload" src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAwIiBoZWlnaHQ9IjQwMCIgdmlld0JveD0iMCAwIDQwMCA0MDAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PHBhdGggZD0iTTAgMGg0MDB2NDAwSDB6IiBmaWxsPSJub25lIj48L3BhdGg+PC9zdmc+" width="400" height="400" alt="Title" data-src=".../image.jpg" />

{# Renders a small image - no `srcset` #}
{{ getImg(asset, 'small', false, [], false) }}

<img src=".../image.jpg" width="400" height="400" alt="Title" />
```

### `getImgAttr()`
This has the same options as `getImg()` except the `$attributes`. This will return an object of attributes for you to apply on your own. Ideally, you'd use the `{{ attr() }}` function.

```twig
{% set asset = craft.assets.id(47).one() %}

{# Renders a small image, lazyloaded #}
<img class="img-cover" {{ attr(getImgAttr(asset, 'small')) }} />

<img class="img-cover" src=".../image.jpg" srcset=".../image.jpg, .../image.jpg 1.5x, .../image.jpg 2x, .../image.jpg 3x" width="400" height="400" alt="Title" />

{# Renders a small image, lazyloaded #}
<img class="lazyload img-cover" {{ attr(getImgAttr(asset, 'small', true)) }} />

<img class="lazyload img-cover" src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAwIiBoZWlnaHQ9IjQwMCIgdmlld0JveD0iMCAwIDQwMCA0MDAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PHBhdGggZD0iTTAgMGg0MDB2NDAwSDB6IiBmaWxsPSJub25lIj48L3BhdGg+PC9zdmc+" width="400" height="400" alt="Title" data-src=".../image.jpg" data-srcset=".../image.jpg, .../image.jpg 1.5x, .../image.jpg 2x, .../image.jpg 3x" />

{# Renders a small image, lazyloaded, no `srcset` #}
<img class="lazyload img-cover" {{ attr(getImgAttr(asset, 'small', true, false)) }} />

<img class="lazyload img-cover" src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAwIiBoZWlnaHQ9IjQwMCIgdmlld0JveD0iMCAwIDQwMCA0MDAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PHBhdGggZD0iTTAgMGg0MDB2NDAwSDB6IiBmaWxsPSJub25lIj48L3BhdGg+PC9zdmc+" width="400" height="400" alt="Title" data-src=".../image.jpg" />

{# Renders a small image #}
<img class="img-cover" {{ attr(getImgAttr(asset, 'small', false)) }} />

<img class="img-cover" src=".../image.jpg" srcset=".../image.jpg, .../image.jpg 1.5x, .../image.jpg 2x, .../image.jpg 3x" width="400" height="400" alt="Title" />

{# Renders a small image, no `srcset` #}
<img class="img-cover" {{ attr(getImgAttr(asset, 'small', false, false)) }} />

<img class="img-cover" src=".../image.jpg" width="400" height="400" alt="Title" />
```

You'll notice on the instances were using `lazyloading` the need to manually include `lazyload` because we're rendering the classes on our own. Alternatively, you should do:

```twig
{% set attributes = getImgAttr(asset, 'small', false, false) | merge({
    class: 'img-cover',
}) %}

<img {{ attr(attributes) }} />
```

This would combine the `img-cover` class in the template and the `lazyload` class from the function.

### `getBg()`
This behaves in almost the exact same manner as `getImg()` but return a `<div>` element.

```twig
{# Renders a banner image #}
{{ getBg(asset, 'banner', false, { class: 'img-cover aspect aspect-21x9' }) }}

<div class="img-cover aspect aspect-21x9" style="background-image: url('../image.jpg');"></div>

{# Renders a banner image, lazyloaded #}
{{ getBg(asset, 'banner', true, { class: 'img-cover aspect aspect-21x9' }) }}

<div class="lazyload img-cover aspect aspect-21x9" data-bgset="../image.jpg, ../image.jpg 1.5x, ../image.jpg 2x, ../image.jpg 3x"></div>

{# Renders a banner image, no `srcset` #}
{{ getBg(asset, 'banner', false, { class: 'img-cover aspect aspect-21x9' }, false) }}

<div class="img-cover aspect aspect-21x9" style="background-image: url('../image.jpg');"></div>

{# Renders a banner image, lazyloaded, no `srcset` #}
{{ getBg(asset, 'banner', true, { class: 'img-cover aspect aspect-21x9' }, false) }}

<div class="lazyload img-cover aspect aspect-21x9" data-bg="../image.jpg"></div>

### `getBgAttr()`
This behaves in almost the exact same manner as `getImgAttr()`, just with different attributes.

```twig
{% set attributes = getBgAttr(asset, 'banner', false) %}
<div class="img-cover aspect aspect-21x9" {{ attr(attributes) }}></div>

{% set attributes = getBgAttr(asset, 'banner', true) %}
<div class="lazyload img-cover aspect aspect-21x9" {{ attr(attributes) }}></div>

{% set attributes = getBgAttr(asset, 'banner', false, false) %}
<div class="img-cover aspect aspect-21x9" {{ attr(attributes) }}></div>

{% set attributes = getBgAttr(asset, 'banner', true, false) %}
<div class="lazyload img-cover aspect aspect-21x9" {{ attr(attributes) }}></div>
```
