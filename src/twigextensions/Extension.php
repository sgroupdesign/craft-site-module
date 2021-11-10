<?php
namespace sgroup\sitemodule\twigextensions;

use modules\sitemodule\SiteModule;

use craft\helpers\Html;
use craft\helpers\Template;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

use spicyweb\embeddedassets\Plugin as EmbeddedAssets;

class Extension extends AbstractExtension
{
    // Public Methods
    // =========================================================================

    public function getName(): string
    {
        return 'Site Module Twig Extension';
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('resource', [SiteModule::$plugin->getService(), 'getResourceUrl']),
            new TwigFunction('svgPlaceholder', [$this, 'svgPlaceholder']),
            new TwigFunction('getFormattedPhone', [$this, 'getFormattedPhone']),
            new TwigFunction('getVideo', [$this, 'getVideo']),
            new TwigFunction('getImg', [$this, 'getImg']),
            new TwigFunction('getImgAttr', [$this, 'getImgAttr']),
            new TwigFunction('getBg', [$this, 'getBg']),
            new TwigFunction('getBgAttr', [$this, 'getBgAttr']),
        ];
    }

    public function svgPlaceholder($width, $height = null)
    {
        if ($height === null) {
            $dimensions = $width;
            $width = $dimensions['width'];
            $height = $dimensions['height'] ?? round($dimensions['width'] * $dimensions['ratio']);
        }

        $src = Html::tag('svg', Html::tag('path', null, [
            'd' => "M0 0h{$width}v{$height}H0z",
            'fill' => 'none'
        ]), [
            'width' => $width,
            'height' => $height,
            'viewBox' => "0 0 $width $height",
            'xmlns' => 'http://www.w3.org/2000/svg'
        ]);

        return 'data:image/svg+xml;base64,' . base64_encode($src);
    }

    public function getFormattedPhone($phone)
    {
        // Strip out any non-numbers
        $formattedPhone = preg_replace('/[^0-9]/', '', $phone);

        // Format area code, and prepend '+61'
        $formattedPhone = preg_replace('/^0/', '+61', $formattedPhone);

        return $formattedPhone;
    }

    public function getVideo($element, $settings = [])
    {
        $embed = EmbeddedAssets::$plugin->methods->getEmbeddedAsset($element);

        if ($embed) {
            if (strtolower($embed->providerName) === 'youtube') {
                // If we want to provide params to control the video, its a little trickier

                // Default settings for videos
                $videoSettings = [
                    'showinfo' => 0,
                    'rel' => 0,
                    'mute' => ($settings['muted'] ?? false) ? 1 : 0,
                    'autoplay' => ($settings['autoplay'] ?? false) ? 1 : 0,
                    'showinfo' => ($settings['controls'] ?? false) ? 1 : 0,
                    'controls' => ($settings['controls'] ?? false) ? 1 : 0,
                ];

                $params = [];

                foreach ($videoSettings as $name => $value) {
                    $params[] = $name . '=' . $value;
                }

                $videoId = preg_replace('/.+watch\\?v=(.+)/', '$1', $embed->url);
                $url = '//youtube.com/embed/' . $videoId . '?' . implode('&', $params);

                return Template::raw('<iframe src="' . $url . '" frameborder="0" allowfullscreen></iframe>');
            } else {
                if ($embed->isSafe) {
                    return Template::raw($embed->html);
                }
            }
        } else {
            return Template::raw(Html::tag('video', Html::tag('source', null, [
                'src' => $element->url,
                'type' => 'video/mp4',
            ]), $settings));
        }
    }

    public function getImgAttr($image, $transform = null, $lazyload = false, $sizes = 'default')
    {
        // Set some sane defaults for sizes
        if ($sizes === 'default') {
            $sizes = ['1x', '1.5x', '2x', '3x'];
        }

        // Apply the transform on the image before doing anything.
        // This ensures the base sizes are correct. Will never upscale.
        if ($transform) {
            $image->setTransform($transform);
        }

        // Handle lazyloading classes a little differently
        if ($lazyload) {
            $attr = [
                'class' => 'lazyload',
                'src' => $this->svgPlaceholder($image->getWidth(), $image->getHeight()),
                'data-src' => $image->getUrl(),
                'data-srcset' => $sizes ? $image->getSrcset($sizes) : false,
            ];
        } else {
            $attr = [
                'src' => $image->getUrl(),
                'srcset' => $sizes ? $image->getSrcset($sizes) : false,
            ];
        }

        // Get focal point values
        $focalPoint = $image->focalPoint ?? [];

        // Return the defaults + settings above + attributes
        return array_merge_recursive([
            'width' => $image->getWidth(),
            'height' => $image->getHeight(),
            'alt' => $image->title,
            'style' => ($focalPoint ? [
                'object-fit' => 'cover',
                'object-position' => ($focalPoint['x'] * 100) . '% ' . ($focalPoint['y'] * 100) . '%',
            ] : null),
        ], $attr);
    }

    public function getImg($image, $transform = null, $lazyload = false, $attributes = [], $sizes = 'default')
    {
        // Get all the generated attributes to make a correct tag
        $attr = $this->getImgAttr($image, $transform, $lazyload, $sizes);

        // Return the defaults + settings above + attributes
        $settings = array_merge_recursive($attr, $attributes);

        $html = Html::tag('img', '', $settings);

        return Template::raw($html);
    }

    public function getBgAttr($image, $transform = null, $lazyload = false, $sizes = 'default')
    {
        // Set some sane defaults for sizes
        if ($sizes === 'default') {
            $sizes = ['1x', '1.5x', '2x', '3x'];
        }

        // Apply the transform on the image before doing anything.
        // This ensures the base sizes are correct. Will never upscale.
        if ($transform) {
            $image->setTransform($transform);
        }

        // Handle lazyloading classes a little differently
        if ($lazyload) {
            $attr = [
                'class' => 'lazyload',
                'data-bg' => $sizes ? false : $image->getUrl(),
                'data-bgset' => $sizes ? $image->getSrcset($sizes) : false,
            ];
        } else {
            $attr = [
                'style' => [
                    'background-image' => 'url("' . $image->getUrl() . '")',
                ],
            ];
        }

        // Get focal point values
        $focalPoint = $image->focalPoint ?? [];

        // Return the defaults + settings above + attributes
        return array_merge_recursive([
            'style' => ($focalPoint ? [
                'background-position' => ($focalPoint['x'] * 100) . '% ' . ($focalPoint['y'] * 100) . '%',
            ] : null),
        ], $attr);
    }

    public function getBg($image, $transform = null, $lazyload = false, $attributes = [], $sizes = 'default')
    {
        // Get all the generated attributes to make a correct tag
        $attr = $this->getBgAttr($image, $transform, $lazyload, $sizes);

        // Return the defaults + settings above + attributes
        $settings = array_merge_recursive($attr, $attributes);

        $html = Html::tag('div', '', $settings);

        return Template::raw($html);
    }
}
