<?php

declare(strict_types=1);

use Filament\Forms\Components\RichEditor\RichContentRenderer;
use Oliwol\FilamentRichEditorHeroicons\FilamentRichEditorHeroicons;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;

function heroiconDoc(array $attrs): string
{
    return json_encode([
        'type' => 'doc',
        'content' => [[
            'type' => 'paragraph',
            'content' => [[
                'type' => 'heroicon',
                'attrs' => array_merge([
                    'icon' => 'academic-cap',
                    'align' => 'inline',
                    'size' => 'md',
                    'style' => 'outline',
                    'color' => '#000000',
                    'svg' => null,
                    'ariaLabel' => null,
                ], $attrs),
            ]],
        ]],
    ]);
}

function renderer(string $content): RichContentRenderer
{
    return RichContentRenderer::make($content)
        ->plugins([FilamentRichEditorHeroicons::make()]);
}

// --- toUnsafeHtml ---

it('toUnsafeHtml contains svg for valid icon', function (): void {
    $html = renderer(heroiconDoc([]))->toUnsafeHtml();

    expect($html)->toContain('<svg');
});

it('toUnsafeHtml decorative icon has aria-hidden', function (): void {
    $html = renderer(heroiconDoc(['ariaLabel' => null]))->toUnsafeHtml();

    expect($html)
        ->toContain('aria-hidden="true"')
        ->not->toContain('role="img"');
});

it('toUnsafeHtml labeled icon has role and aria-label', function (): void {
    $html = renderer(heroiconDoc(['ariaLabel' => 'Abschluss']))->toUnsafeHtml();

    expect($html)
        ->toContain('role="img"')
        ->toContain('aria-label="Abschluss"')
        ->not->toContain('aria-hidden="true"');
});

it('toUnsafeHtml applies color style', function (): void {
    $html = renderer(heroiconDoc(['color' => '#ef4444']))->toUnsafeHtml();

    expect($html)->toContain('color:#ef4444');
});

it('toUnsafeHtml applies size', function (): void {
    $html = renderer(heroiconDoc(['size' => 'xl']))->toUnsafeHtml();

    expect($html)
        ->toContain('width:48px')
        ->toContain('height:48px');
});

it('toUnsafeHtml wraps left-aligned icon in span', function (): void {
    $html = renderer(heroiconDoc(['align' => 'left']))->toUnsafeHtml();

    expect($html)->toContain('float:left');
});

it('toUnsafeHtml wraps right-aligned icon in span', function (): void {
    $html = renderer(heroiconDoc(['align' => 'right']))->toUnsafeHtml();

    expect($html)->toContain('float:right');
});

it('toUnsafeHtml wraps center-aligned icon in span', function (): void {
    $html = renderer(heroiconDoc(['align' => 'center']))->toUnsafeHtml();

    expect($html)->toContain('justify-content:center');
});

// --- toHtml (via Str::sanitizeHtml) ---

it('toHtml contains svg for valid icon', function (): void {
    $html = renderer(heroiconDoc([]))->toHtml();

    expect($html)->toContain('<svg');
});

it('toHtml decorative icon has aria-hidden', function (): void {
    $html = renderer(heroiconDoc(['ariaLabel' => null]))->toHtml();

    expect($html)
        ->toContain('aria-hidden="true"')
        ->not->toContain('role="img"');
});

it('toHtml labeled icon has role and aria-label', function (): void {
    $html = renderer(heroiconDoc(['ariaLabel' => 'Abschluss']))->toHtml();

    expect($html)
        ->toContain('role="img"')
        ->toContain('aria-label="Abschluss"')
        ->not->toContain('aria-hidden="true"');
});

it('toHtml applies color style', function (): void {
    $html = renderer(heroiconDoc(['color' => '#ef4444']))->toHtml();

    expect($html)->toContain('color:#ef4444');
});

it('toHtml applies size', function (): void {
    $html = renderer(heroiconDoc(['size' => 'xl']))->toHtml();

    expect($html)
        ->toContain('width:48px')
        ->toContain('height:48px');
});

it('toHtml wraps left-aligned icon in span', function (): void {
    $html = renderer(heroiconDoc(['align' => 'left']))->toHtml();

    expect($html)->toContain('float:left');
});

it('toHtml wraps right-aligned icon in span', function (): void {
    $html = renderer(heroiconDoc(['align' => 'right']))->toHtml();

    expect($html)->toContain('float:right');
});

it('toHtml wraps center-aligned icon in span', function (): void {
    $html = renderer(heroiconDoc(['align' => 'center']))->toHtml();

    expect($html)->toContain('justify-content:center');
});

it('toHtml contains svg when HtmlSanitizerConfig is separately bound (Filament v5.6+)', function (): void {
    // Simulate Filament v5.6+ where HtmlSanitizerConfig is a separate scoped binding
    $this->app->scoped(
        HtmlSanitizerConfig::class,
        fn (): HtmlSanitizerConfig => (new HtmlSanitizerConfig)
            ->allowSafeElements()
            ->allowRelativeLinks()
            ->allowRelativeMedias()
            ->allowAttribute('class', allowedElements: '*')
            ->allowAttribute('style', allowedElements: '*')
            ->withMaxInputLength(500000)
    );

    $html = renderer(heroiconDoc([]))->toHtml();

    expect($html)->toContain('<svg');
});

it('toHtml contains svg when HtmlSanitizerConfig is not separately bound (Filament v5.1.x compat)', function (): void {
    // Remove the binding if present (e.g. Filament v5.6+) to force the filamentBaseConfig() fallback
    $this->app->offsetUnset(HtmlSanitizerConfig::class);

    $html = renderer(heroiconDoc([]))->toHtml();

    expect($html)->toContain('<svg');
});
