<?php

declare(strict_types=1);

namespace Oliwol\FilamentRichEditorHeroicons;

use Filament\Actions\Action;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\RichEditor\EditorCommand;
use Filament\Forms\Components\RichEditor\Plugins\Contracts\RichContentPlugin;
use Filament\Forms\Components\RichEditor\RichEditorTool;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\ToggleButtons;
use Filament\Support\Enums\IconSize;
use Filament\Support\Enums\Width;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use Tiptap\Core\Extension;

final class FilamentRichEditorHeroicons implements RichContentPlugin
{
    /** @var array<string, int> */
    private array $sizes = [
        'sm' => 16,
        'md' => 24,
        'lg' => 32,
        'xl' => 48,
    ];

    private string $defaultSize = 'md';

    public static function make(): self
    {
        return new self;
    }

    /**
     * @param  array<string, int>  $sizes
     */
    public function sizes(array $sizes): self
    {
        $this->sizes = $sizes;

        return $this;
    }

    public function defaultSize(string $defaultSize): self
    {
        $this->defaultSize = $defaultSize;

        return $this;
    }

    /**
     * @return array<string, int>
     */
    public function getSizes(): array
    {
        return $this->sizes;
    }

    public function getDefaultSize(): string
    {
        return $this->defaultSize;
    }

    /**
     * @return array<Extension>
     */
    public function getTipTapPhpExtensions(): array
    {
        return [
            new FilamentRichEditorHeroiconsTipTapExtension,
        ];
    }

    /**
     * @return array<string>
     */
    public function getTipTapJsExtensions(): array
    {
        return [
            FilamentAsset::getScriptSrc('filament-rich-editor-heroicons-scripts', 'oliwol/filament-rich-editor-heroicons'),
        ];
    }

    /**
     * @return array<RichEditorTool>
     */
    public function getEditorTools(): array
    {
        return [
            RichEditorTool::make('addHeroicon')
                ->label(__('filament-rich-editor-heroicons::rich-editor-heroicons.action_label'))
                ->action(
                    arguments: '{ icon: $getEditor().getAttributes(\'heroicon\')?.[\'data-icon\'], align: $getEditor().getAttributes(\'heroicon\')?.[\'data-align\'], size: $getEditor().getAttributes(\'heroicon\')?.[\'data-size\'] }',
                )
                ->icon(Heroicon::OutlinedFaceSmile),
        ];
    }

    /**
     * @return array<Action>
     */
    public function getEditorActions(): array
    {
        return [
            Action::make('addHeroicon')
                ->modalHeading(__('filament-rich-editor-heroicons::rich-editor-heroicons.heading'))
                ->modalWidth(Width::Large)
                ->fillForm(fn (array $arguments): array => [
                    'icon' => $arguments['icon'] ?? null,
                    'align' => $arguments['align'] ?? 'inline',
                    'size' => $arguments['size'] ?? $this->defaultSize,
                ])
                ->schema([
                    Select::make('icon')
                        ->getSearchResultsUsing(fn (string $search): array => $this->searchIcons($search))
                        ->getOptionLabelUsing(fn (string $value): string => $this->renderOptionLabel($value))
                        ->allowHtml()
                        ->label(__('filament-rich-editor-heroicons::rich-editor-heroicons.label'))
                        ->searchable()
                        ->required()
                        ->live()
                        ->native(false)
                        ->placeholder(__('filament-rich-editor-heroicons::rich-editor-heroicons.placeholder'))
                        ->belowContent(new HtmlString(__('filament-rich-editor-heroicons::rich-editor-heroicons.below_content', ['link-heroicon' => '<a href="https://heroicons.com/" style="text-decoration:underline" target="_blank" rel="noopener noreferrer">Heroicon</a>']))),
                    ToggleButtons::make('align')
                        ->label(__('filament-rich-editor-heroicons::rich-editor-heroicons.alignment_label'))
                        ->options([
                            'inline' => __('filament-rich-editor-heroicons::rich-editor-heroicons.alignment_inline'),
                            'left' => __('filament-rich-editor-heroicons::rich-editor-heroicons.alignment_left'),
                            'center' => __('filament-rich-editor-heroicons::rich-editor-heroicons.alignment_center'),
                            'right' => __('filament-rich-editor-heroicons::rich-editor-heroicons.alignment_right'),
                        ])
                        ->icons([
                            'inline' => Heroicon::OutlinedBars2,
                            'left' => Heroicon::OutlinedBars3BottomLeft,
                            'center' => Heroicon::OutlinedBars3,
                            'right' => Heroicon::OutlinedBars3BottomRight,
                        ])
                        ->default('inline')
                        ->inline()
                        ->grouped(),
                    ToggleButtons::make('size')
                        ->label(__('filament-rich-editor-heroicons::rich-editor-heroicons.size_label'))
                        ->options(fn (callable $get): array => collect($this->sizes)->mapWithKeys(fn (int $px, string $key): array => [
                            $key => new HtmlString($this->renderSizeLabel($key, $get('icon'))),
                        ])->toArray())
                        ->default($this->defaultSize)
                        ->inline()
                        ->grouped(),
                ])
                ->action(function (array $arguments, array $data, RichEditor $component): void {
                    $iconName = $data['icon'];
                    $icon = Heroicon::tryFrom('o-'.($iconName ?? ''));

                    if (! $icon) {
                        return;
                    }

                    $size = $data['size'] ?? $this->defaultSize;
                    $px = $this->sizes[$size] ?? 24;

                    $svg = Blade::render('<x-filament::icon icon="'.$icon->getIconForSize(IconSize::Medium).'" style="width:'.$px.'px;height:'.$px.'px;vertical-align:middle" />');

                    $component->runCommands(
                        commands: [
                            EditorCommand::make(
                                'insertContent',
                                arguments: [
                                    [
                                        'type' => 'heroicon',
                                        'attrs' => [
                                            'icon' => $iconName,
                                            'svg' => $svg,
                                            'align' => $data['align'] ?? 'inline',
                                            'size' => $size,
                                        ],
                                    ],
                                ],
                            ),
                        ],
                        editorSelection: $arguments['editorSelection'],
                    );
                }),
        ];
    }

    /**
     * @return array<string, string>
     */
    public function searchIcons(string $search): array
    {
        return collect(Heroicon::cases())
            ->filter(fn (Heroicon $icon): bool => str_starts_with($icon->value, 'o-'))
            ->filter(fn (Heroicon $icon): bool => str_contains(
                str_replace('o-', '', $icon->value),
                mb_strtolower($search),
            ))
            ->take(50)
            ->mapWithKeys(function (Heroicon $icon): array {
                $slug = str_replace('o-', '', $icon->value);

                return [$slug => $this->renderIconLabel($slug, $icon)];
            })
            ->toArray();
    }

    public function renderOptionLabel(string $value): string
    {
        $icon = Heroicon::tryFrom('o-'.$value);

        if (! $icon) {
            return $value;
        }

        return $this->renderIconLabel($value, $icon);
    }

    public function renderSizeLabel(string $value, ?string $iconSlug = null): string
    {
        $px = $this->sizes[$value] ?? 24;

        $heroicon = $iconSlug ? Heroicon::tryFrom('o-'.$iconSlug) : null;
        $filamentIcon = ($heroicon ?? Heroicon::OutlinedFaceSmile)->getIconForSize(IconSize::Medium);

        return Blade::render('<x-filament::icon :icon="$icon" style="width:'.$px.'px;height:'.$px.'px;display:block" />', [
            'icon' => $filamentIcon,
        ]);
    }

    private function renderIconLabel(string $slug, Heroicon $icon): string
    {
        $svg = Blade::render('<x-filament::icon :icon="$icon" style="width:1.25rem;height:1.25rem;display:inline-block;vertical-align:middle;flex-shrink:0" />', [
            'icon' => $icon->getIconForSize(IconSize::Small),
        ]);

        return '<span style="display:flex;align-items:center;gap:0.5rem">'.$svg.'<span>'.$slug.'</span></span>';
    }
}
