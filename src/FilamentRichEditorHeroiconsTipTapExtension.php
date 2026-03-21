<?php

declare(strict_types=1);

namespace Oliwol\FilamentRichEditorHeroicons;

use Filament\Support\Enums\IconSize;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Blade;
use Tiptap\Core\Node;

final class FilamentRichEditorHeroiconsTipTapExtension extends Node
{
    public static $name = 'heroicon';

    public static function alignmentStyle(string $align): string
    {
        return match ($align) {
            'left' => 'display:inline-block;float:left;margin-right:0.5rem;',
            'right' => 'display:inline-block;float:right;margin-left:0.5rem;',
            'center' => 'display:flex;justify-content:center;',
            default => '',
        };
    }

    public function addAttributes(): array
    {
        return [
            'icon' => ['default' => null],
            'align' => ['default' => 'inline'],
        ];
    }

    public function renderHTML($node): array
    {
        $icon = Heroicon::tryFrom('o-'.($node->attrs->icon ?? ''));

        if (! $icon) {
            return ['span', ['class' => 'inline-block'], ''];
        }

        $align = $node->attrs->align ?? 'inline';

        $svg = Blade::render('<x-filament::icon icon="'.$icon->getIconForSize(IconSize::Medium).'" class="inline-block size-6 align-middle" />');

        $alignmentStyle = self::alignmentStyle($align);

        if ($alignmentStyle !== '') {
            $svg = '<span style="'.$alignmentStyle.'">'.$svg.'</span>';
        }

        return ['content' => $svg];
    }
}
