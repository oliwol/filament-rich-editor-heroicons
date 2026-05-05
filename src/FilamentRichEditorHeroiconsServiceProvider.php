<?php

declare(strict_types=1);

namespace Oliwol\FilamentRichEditorHeroicons;

use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Contracts\Foundation\Application;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Symfony\Component\HtmlSanitizer\HtmlSanitizer;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerInterface;

final class FilamentRichEditorHeroiconsServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-rich-editor-heroicons';

    public function configurePackage(Package $package): void
    {
        $package->name(self::$name);
        $package->shortName();

        if (file_exists($package->basePath('/../resources/lang'))) {
            $package->hasTranslations();
        }
    }

    public function packageRegistered(): void
    {
        $this->app->extend(
            HtmlSanitizerInterface::class,
            function (HtmlSanitizerInterface $sanitizer, Application $app): HtmlSanitizerInterface {
                $config = $app->bound(HtmlSanitizerConfig::class)
                    ? $app->make(HtmlSanitizerConfig::class)
                    : $this->filamentBaseConfig();

                return new HtmlSanitizer($this->addSvgElements($config));
            }
        );
    }

    public function packageBooted(): void
    {
        FilamentAsset::register(
            [
                Js::make('filament-rich-editor-heroicons-scripts', __DIR__.'/../resources/dist/filament-rich-editor-heroicons.js')->module(),
            ],
            'oliwol/filament-rich-editor-heroicons'
        );
    }

    private function addSvgElements(HtmlSanitizerConfig $config): HtmlSanitizerConfig
    {
        // Symfony normalizes attribute names to lowercase, so 'viewBox' → 'viewbox'.
        // Global allowAttribute('style'/'class', '*') does not apply to elements added
        // after the call, so we include them explicitly on each SVG element.
        $shared = ['class', 'style', 'aria-hidden', 'aria-label', 'role', 'id'];

        return $config
            ->allowElement('svg', [...$shared, 'xmlns', 'viewbox', 'fill', 'stroke', 'stroke-width', 'data-slot'])
            ->allowElement('path', [...$shared, 'd', 'fill', 'stroke', 'stroke-linecap', 'stroke-linejoin', 'fill-rule', 'clip-rule'])
            ->allowElement('circle', [...$shared, 'cx', 'cy', 'r', 'fill', 'stroke'])
            ->allowElement('rect', [...$shared, 'x', 'y', 'width', 'height', 'rx', 'ry', 'fill', 'stroke'])
            ->allowElement('g', [...$shared, 'fill', 'stroke', 'transform'])
            ->allowElement('defs', $shared)
            ->allowElement('clipPath', [...$shared, 'id'])
            ->allowElement('polyline', [...$shared, 'points', 'fill', 'stroke'])
            ->allowElement('polygon', [...$shared, 'points', 'fill', 'stroke'])
            ->allowElement('line', [...$shared, 'x1', 'y1', 'x2', 'y2', 'stroke'])
            ->allowElement('ellipse', [...$shared, 'cx', 'cy', 'rx', 'ry', 'fill', 'stroke']);
    }

    private function filamentBaseConfig(): HtmlSanitizerConfig
    {
        return (new HtmlSanitizerConfig)
            ->allowSafeElements()
            ->allowRelativeLinks()
            ->allowRelativeMedias()
            ->allowAttribute('class', allowedElements: '*')
            ->allowAttribute('data-color', allowedElements: '*')
            ->allowAttribute('data-from-breakpoint', allowedElements: '*')
            ->allowAttribute('data-type', allowedElements: '*')
            ->allowAttribute('style', allowedElements: '*')
            ->allowAttribute('width', allowedElements: 'img')
            ->allowAttribute('height', allowedElements: 'img')
            ->withMaxInputLength(500000);
    }
}
