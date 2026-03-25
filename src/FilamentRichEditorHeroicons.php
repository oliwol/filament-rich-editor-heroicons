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
use Filament\Schemas\Components\FusedGroup;
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

    /** @var array<string> */
    private array $styles = ['outline', 'solid'];

    public static function make(): self
    {
        return new self;
    }

    public static function resolveHeroicon(string $slug, string $style = 'outline'): ?Heroicon
    {
        if ($style === 'solid') {
            return Heroicon::tryFrom($slug);
        }

        return Heroicon::tryFrom('o-'.$slug);
    }

    public static function bladeIconName(Heroicon $icon, string $style = 'outline'): string
    {
        if ($style === 'solid') {
            return $icon->getIconForSize(IconSize::Large);
        }

        return $icon->getIconForSize(IconSize::Medium);
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
     * @param  array<string>  $styles
     */
    public function styles(array $styles): self
    {
        $this->styles = $styles;

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
     * @return array<string>
     */
    public function getStyles(): array
    {
        return $this->styles;
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
                    arguments: '{ icon: $getEditor().getAttributes(\'heroicon\')?.icon, align: $getEditor().getAttributes(\'heroicon\')?.align, size: $getEditor().getAttributes(\'heroicon\')?.size, style: $getEditor().getAttributes(\'heroicon\')?.style }',
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
                ->modalHeading(fn (array $arguments): string => filled($arguments['icon'] ?? null)
                    ? __('filament-rich-editor-heroicons::rich-editor-heroicons.heading_edit')
                    : __('filament-rich-editor-heroicons::rich-editor-heroicons.heading'))
                ->modalWidth(Width::Large)
                ->fillForm(fn (array $arguments): array => [
                    'style' => $arguments['style'] ?? $this->styles[0],
                    'icon' => $arguments['icon'] ?? null,
                    'align' => $arguments['align'] ?? 'inline',
                    'size' => $arguments['size'] ?? $this->defaultSize,
                ])
                ->schema([
                    FusedGroup::make([
                        Select::make('icon')
                            ->getSearchResultsUsing(fn (string $search, callable $get): array => $this->searchIcons($search, $get('style') ?? $this->styles[0]))
                            ->getOptionLabelUsing(fn (string $value, callable $get): string => $this->renderOptionLabel($value, $get('style') ?? $this->styles[0]))
                            ->allowHtml()
                            ->searchable()
                            ->required()
                            ->live()
                            ->native(false)
                            ->columnSpan(2)
                            ->placeholder(__('filament-rich-editor-heroicons::rich-editor-heroicons.placeholder'))
                            ->belowContent(new HtmlString(__('filament-rich-editor-heroicons::rich-editor-heroicons.below_content', ['link-heroicon' => '<a href="https://heroicons.com/" style="text-decoration:underline" target="_blank" rel="noopener noreferrer">Heroicon</a>']))),
                        ...(count($this->styles) > 1
                            ? [
                                Select::make('style')
                                    ->options(fn (): array => collect($this->styles)->mapWithKeys(fn (string $style): array => [
                                        $style => $this->renderStyleLabel($style),
                                    ])->toArray())
                                    ->allowHtml()
                                    ->default($this->styles[0])
                                    ->native(false)
                                    ->selectablePlaceholder(false)
                                    ->live()
                                    ->afterStateUpdated(function (callable $set, callable $get): void {
                                        $currentIcon = $get('icon');

                                        if ($currentIcon === null) {
                                            return;
                                        }

                                        $newStyle = $get('style');
                                        $resolved = self::resolveHeroicon($currentIcon, $newStyle);

                                        if (! $resolved instanceof Heroicon) {
                                            $set('icon', null);
                                        }
                                    }),
                            ]
                            : []),
                    ])
                        ->label(__('filament-rich-editor-heroicons::rich-editor-heroicons.label'))
                        ->columns(count($this->styles) > 1 ? 3 : 1)
                        ->columnSpanFull(),
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
                            $key => new HtmlString($this->renderSizeLabel($key, $get('icon'), $get('style') ?? $this->styles[0])),
                        ])->toArray())
                        ->default($this->defaultSize)
                        ->inline()
                        ->grouped(),
                ])
                ->action(function (array $arguments, array $data, RichEditor $component): void {
                    $iconName = $data['icon'];
                    $style = $data['style'] ?? $this->styles[0];
                    $icon = self::resolveHeroicon($iconName ?? '', $style);

                    if (! $icon instanceof Heroicon) {
                        return;
                    }

                    $size = $data['size'] ?? $this->defaultSize;
                    $px = $this->sizes[$size] ?? 24;

                    $bladeIcon = self::bladeIconName($icon, $style);
                    $svg = Blade::render('<x-filament::icon icon="'.$bladeIcon.'" style="width:'.$px.'px;height:'.$px.'px;vertical-align:middle" />');

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
                                            'style' => $style,
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
    public function searchIcons(string $search, string $style = 'outline'): array
    {
        $isOutline = $style !== 'solid';

        return collect(Heroicon::cases())
            ->filter(fn (Heroicon $icon): bool => $isOutline
                ? str_starts_with($icon->value, 'o-')
                : ! str_starts_with($icon->value, 'o-'))
            ->filter(fn (Heroicon $icon): bool => str_contains(
                $isOutline ? str_replace('o-', '', $icon->value) : $icon->value,
                mb_strtolower($search),
            ))
            ->take(50)
            ->mapWithKeys(function (Heroicon $icon) use ($isOutline, $style): array {
                $slug = $isOutline ? str_replace('o-', '', $icon->value) : $icon->value;

                return [$slug => $this->renderIconLabel($slug, $icon, $style)];
            })
            ->toArray();
    }

    public function renderOptionLabel(string $value, string $style = 'outline'): string
    {
        $icon = self::resolveHeroicon($value, $style);

        if (! $icon instanceof Heroicon) {
            return $value;
        }

        return $this->renderIconLabel($value, $icon, $style);
    }

    public function renderStyleLabel(string $style): string
    {
        $fallback = $style === 'solid' ? Heroicon::FaceSmile : Heroicon::OutlinedFaceSmile;
        $bladeIcon = self::bladeIconName($fallback, $style);
        $label = __('filament-rich-editor-heroicons::rich-editor-heroicons.style_'.$style);

        $svg = Blade::render('<x-filament::icon :icon="$icon" style="width:1.25rem;height:1.25rem;display:inline-block;vertical-align:middle;flex-shrink:0" />', [
            'icon' => $bladeIcon,
        ]);

        return '<span style="display:flex;align-items:center;gap:0.5rem">'.$svg.'<span>'.$label.'</span></span>';
    }

    public function renderSizeLabel(string $value, ?string $iconSlug = null, string $style = 'outline'): string
    {
        $px = $this->sizes[$value] ?? 24;

        $heroicon = $iconSlug ? self::resolveHeroicon($iconSlug, $style) : null;
        $fallback = $style === 'solid' ? Heroicon::FaceSmile : Heroicon::OutlinedFaceSmile;
        $filamentIcon = self::bladeIconName($heroicon ?? $fallback, $style);

        return Blade::render('<x-filament::icon :icon="$icon" style="width:'.$px.'px;height:'.$px.'px;display:block" />', [
            'icon' => $filamentIcon,
        ]);
    }

    private function renderIconLabel(string $slug, Heroicon $icon, string $style = 'outline'): string
    {
        $bladeIcon = self::bladeIconName($icon, $style);

        $svg = Blade::render('<x-filament::icon :icon="$icon" style="width:1.25rem;height:1.25rem;display:inline-block;vertical-align:middle;flex-shrink:0" />', [
            'icon' => $bladeIcon,
        ]);

        return '<span style="display:flex;align-items:center;gap:0.5rem">'.$svg.'<span>'.$slug.'</span></span>';
    }
}
