<?php

declare(strict_types=1);

namespace Oliwol\FilamentRichEditorHeroicons;

use Illuminate\Support\Facades\Blade;
use Tiptap\Core\Node;

final class FilamentRichEditorHeroiconsTipTapExtension extends Node
{
    public static $name = 'heroicon';

    /** @var array<string, int> */
    public static array $sizeMap = [
        'sm' => 16,
        'md' => 24,
        'lg' => 32,
        'xl' => 48,
    ];

    public static function alignmentStyle(string $align): string
    {
        return match ($align) {
            'left' => 'display:inline-block;vertical-align:middle;float:left;margin-right:0.5rem;',
            'right' => 'display:inline-block;vertical-align:middle;float:right;margin-left:0.5rem;',
            'center' => 'display:flex;justify-content:center;',
            default => '',
        };
    }

    public static function sizePixels(string $size): int
    {
        return self::$sizeMap[$size] ?? 24;
    }

    public static function ariaAttributes(?string $ariaLabel): string
    {
        return filled($ariaLabel)
            ? ' role="img" aria-label="'.e($ariaLabel).'"'
            : ' aria-hidden="true"';
    }

    public static function applyAriaAttributes(string $svg, ?string $ariaLabel): string
    {
        if (filled($ariaLabel)) {
            return str_replace(' aria-hidden="true"', '', $svg);
        }

        return $svg;
    }

    public function addAttributes(): array
    {
        return [
            'icon' => ['default' => null],
            'align' => ['default' => 'inline'],
            'size' => ['default' => 'md'],
            'style' => ['default' => 'outline'],
            'color' => ['default' => '#000000'],
            'ariaLabel' => ['default' => null],
        ];
    }

    public function renderHTML($node): array
    {
        $style = $node->attrs->style ?? 'outline';
        $icon = FilamentRichEditorHeroicons::resolveHeroicon($node->attrs->icon ?? '', $style);

        if (! $icon instanceof \Filament\Support\Icons\Heroicon) {
            return ['span', ['class' => 'inline-block'], ''];
        }

        $align = $node->attrs->align ?? 'inline';
        $size = $node->attrs->size ?? 'md';
        $px = self::sizePixels($size);

        $color = $node->attrs->color ?? '#000000';
        $colorStyle = $color !== '' ? 'color:'.$color.';' : '';

        $ariaLabel = $node->attrs->ariaLabel ?? null;
        $ariaAttrs = self::ariaAttributes($ariaLabel);

        $bladeIcon = FilamentRichEditorHeroicons::bladeIconName($icon, $style);
        $svg = Blade::render('<x-filament::icon icon="'.$bladeIcon.'"'.$ariaAttrs.' style="'.$colorStyle.'width:'.$px.'px;height:'.$px.'px;display:inline-block;vertical-align:middle" />');
        $svg = self::applyAriaAttributes($svg, $ariaLabel);

        $alignmentStyle = self::alignmentStyle($align);

        if ($alignmentStyle !== '') {
            $svg = '<span style="'.$alignmentStyle.'">'.$svg.'</span>';
        }

        return ['content' => $svg];
    }
}
