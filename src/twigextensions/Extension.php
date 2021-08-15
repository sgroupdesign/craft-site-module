<?php
namespace sgroup\sitemodule\twigextensions;

use modules\sitemodule\SiteModule;

use craft\helpers\Html;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

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
        ];
    }

    public function svgPlaceholder($width, $height = null)
    {
        if ($height === null) {
            $dimensions = $width;
            $width = $dimensions['width'];
            $height = $dimensions['height'] ?? round($dimensions['width'] * $dimensions['ratio']);
        }

        $src = Html::tag(
            'svg',
            Html::tag('path', null, [
                'd' => "M0 0h{$width}v{$height}H0z",
                'fill' => 'none'
            ]),
            [
                'width' => $width,
                'height' => $height,
                'viewBox' => "0 0 $width $height",
                'xmlns' => 'http://www.w3.org/2000/svg'
            ]
        );

        return 'data:image/svg+xml;base64,' . base64_encode($src);
    }
}
