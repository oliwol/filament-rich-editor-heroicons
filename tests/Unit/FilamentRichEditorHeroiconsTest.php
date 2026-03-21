<?php

declare(strict_types=1);

use Filament\Actions\Action;
use Filament\Forms\Components\RichEditor\Plugins\Contracts\RichContentPlugin;
use Filament\Forms\Components\RichEditor\RichEditorTool;
use Oliwol\FilamentRichEditorHeroicons\FilamentRichEditorHeroicons;
use Oliwol\FilamentRichEditorHeroicons\FilamentRichEditorHeroiconsTipTapExtension;
use Tiptap\Core\Extension;

it('can be instantiated via make', function (): void {
    $plugin = FilamentRichEditorHeroicons::make();

    expect($plugin)->toBeInstanceOf(FilamentRichEditorHeroicons::class);
});

it('implements RichContentPlugin', function (): void {
    expect(FilamentRichEditorHeroicons::make())
        ->toBeInstanceOf(RichContentPlugin::class);
});

it('returns TipTap PHP extensions', function (): void {
    $extensions = FilamentRichEditorHeroicons::make()->getTipTapPhpExtensions();

    expect($extensions)
        ->toBeArray()
        ->toHaveCount(1)
        ->and($extensions[0])
        ->toBeInstanceOf(Extension::class)
        ->toBeInstanceOf(FilamentRichEditorHeroiconsTipTapExtension::class);
});

it('returns TipTap JS extensions', function (): void {
    $extensions = FilamentRichEditorHeroicons::make()->getTipTapJsExtensions();

    expect($extensions)
        ->toBeArray()
        ->toHaveCount(1)
        ->and($extensions[0])
        ->toBeString();
});

it('returns editor tools with addHeroicon', function (): void {
    $tools = FilamentRichEditorHeroicons::make()->getEditorTools();

    expect($tools)
        ->toBeArray()
        ->toHaveCount(1)
        ->and($tools[0])
        ->toBeInstanceOf(RichEditorTool::class)
        ->and($tools[0]->getName())
        ->toBe('addHeroicon');
});

it('returns editor actions with addHeroicon', function (): void {
    $actions = FilamentRichEditorHeroicons::make()->getEditorActions();

    expect($actions)
        ->toBeArray()
        ->toHaveCount(1)
        ->and($actions[0])
        ->toBeInstanceOf(Action::class)
        ->and($actions[0]->getName())
        ->toBe('addHeroicon');
});

it('only includes outline heroicons in search results', function (): void {
    $results = FilamentRichEditorHeroicons::make()->searchIcons('ac');

    expect($results)
        ->toBeArray()
        ->not->toBeEmpty()
        ->and(array_keys($results))->each(
            fn ($key) => $key->not->toStartWith('o-')
                ->and($key)->not->toStartWith('s-')
                ->and($key)->not->toStartWith('m-')
        );
});

it('search results contain svg and icon name', function (): void {
    $results = FilamentRichEditorHeroicons::make()->searchIcons('academic-cap');

    expect($results)
        ->toHaveKey('academic-cap')
        ->and($results['academic-cap'])
        ->toContain('svg')
        ->toContain('academic-cap')
        ->toContain('style="display:flex;align-items:center;gap:0.5rem"');
});

it('search results are limited to 50 items', function (): void {
    $results = FilamentRichEditorHeroicons::make()->searchIcons('a');

    expect(count($results))->toBeLessThanOrEqual(50);
});

it('search results are empty for non-matching query', function (): void {
    $results = FilamentRichEditorHeroicons::make()->searchIcons('zzzznonexistent');

    expect($results)->toBeEmpty();
});

it('renders option label with svg for valid icon', function (): void {
    $label = FilamentRichEditorHeroicons::make()->renderOptionLabel('academic-cap');

    expect($label)
        ->toContain('svg')
        ->toContain('academic-cap')
        ->toContain('style="display:flex;align-items:center;gap:0.5rem"');
});

it('renders option label as plain text for invalid icon', function (): void {
    $label = FilamentRichEditorHeroicons::make()->renderOptionLabel('nonexistent-icon');

    expect($label)->toBe('nonexistent-icon');
});

it('action does nothing for invalid icon',
    /**
     * @throws ReflectionException
     */
    function (): void {
        $actions = FilamentRichEditorHeroicons::make()->getEditorActions();
        $action = $actions[0];

        $reflection = new ReflectionClass($action);
        $property = $reflection->getProperty('action');
        $closure = $property->getValue($action);

        $component = Mockery::mock(Filament\Forms\Components\RichEditor::class);
        $component->shouldNotReceive('runCommands');

        $closure(
            ['editorSelection' => null],
            ['icon' => 'nonexistent-icon-that-does-not-exist'],
            $component,
        );
    });

it('action inserts heroicon for valid icon',
    /**
     * @throws ReflectionException
     */
    function (): void {
        $actions = FilamentRichEditorHeroicons::make()->getEditorActions();
        $action = $actions[0];

        $reflection = new ReflectionClass($action);
        $property = $reflection->getProperty('action');
        $closure = $property->getValue($action);

        $component = Mockery::mock(Filament\Forms\Components\RichEditor::class);
        $component->shouldReceive('runCommands')
            ->once()
            ->withArgs(fn (array $commands, mixed $editorSelection): bool => count($commands) === 1
                && $editorSelection === ['start' => 0, 'end' => 0]);

        $closure(
            ['editorSelection' => ['start' => 0, 'end' => 0]],
            ['icon' => 'academic-cap'],
            $component,
        );
    });

it('tiptap extension has correct name', function (): void {
    expect(FilamentRichEditorHeroiconsTipTapExtension::$name)
        ->toBe('heroicon');
});

it('tiptap extension defines icon attribute', function (): void {
    $extension = new FilamentRichEditorHeroiconsTipTapExtension;
    $attributes = $extension->addAttributes();

    expect($attributes)
        ->toBeArray()
        ->toHaveKey('icon')
        ->and($attributes['icon']['default'])
        ->toBeNull();
});

it('tiptap extension renders empty span for invalid icon', function (): void {
    $extension = new FilamentRichEditorHeroiconsTipTapExtension;

    $node = new stdClass;
    $node->attrs = new stdClass;
    $node->attrs->icon = 'nonexistent-icon';

    $result = $extension->renderHTML($node);

    expect($result)
        ->toBeArray()
        ->toBe(['span', ['class' => 'inline-block'], '']);
});

it('tiptap extension renders empty span when icon is null', function (): void {
    $extension = new FilamentRichEditorHeroiconsTipTapExtension;

    $node = new stdClass;
    $node->attrs = new stdClass;
    $node->attrs->icon = null;

    $result = $extension->renderHTML($node);

    expect($result)
        ->toBe(['span', ['class' => 'inline-block'], '']);
});

it('tiptap extension renders svg for valid icon', function (): void {
    $extension = new FilamentRichEditorHeroiconsTipTapExtension;

    $node = new stdClass;
    $node->attrs = new stdClass;
    $node->attrs->icon = 'academic-cap';

    $result = $extension->renderHTML($node);

    expect($result)
        ->toBeArray()
        ->toHaveKey('content')
        ->and($result['content'])
        ->toBeString()
        ->toContain('svg');
});

it('loads translations', function (): void {
    expect(__('filament-rich-editor-heroicons::rich-editor-heroicons.action_label'))
        ->not->toBe('filament-rich-editor-heroicons::rich-editor-heroicons.action_label')
        ->and(__('filament-rich-editor-heroicons::rich-editor-heroicons.heading'))
        ->not->toBe('filament-rich-editor-heroicons::rich-editor-heroicons.heading')
        ->and(__('filament-rich-editor-heroicons::rich-editor-heroicons.label'))
        ->not->toBe('filament-rich-editor-heroicons::rich-editor-heroicons.label')
        ->and(__('filament-rich-editor-heroicons::rich-editor-heroicons.below_content', ['link-heroicon' => 'test']))
        ->not->toBe('filament-rich-editor-heroicons::rich-editor-heroicons.below_content');
});
