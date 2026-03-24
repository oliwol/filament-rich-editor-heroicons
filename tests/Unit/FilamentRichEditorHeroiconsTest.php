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

it('action inserts heroicon for valid icon with default alignment',
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

it('action inserts heroicon with specified alignment',
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
            ['icon' => 'academic-cap', 'align' => 'left'],
            $component,
        );
    });

it('action inserts heroicon with size attribute in command attrs',
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
            ->withArgs(function (array $commands): bool {
                $attrs = $commands[0]->arguments[0]['attrs'];

                return $attrs['size'] === 'lg'
                    && $attrs['icon'] === 'academic-cap';
            });

        $closure(
            ['editorSelection' => ['start' => 0, 'end' => 0]],
            ['icon' => 'academic-cap', 'size' => 'lg'],
            $component,
        );
    });

it('action renders svg with correct pixel size for selected size',
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
            ->withArgs(function (array $commands): bool {
                $svg = $commands[0]->arguments[0]['attrs']['svg'];

                return str_contains($svg, 'width:48px')
                    && str_contains($svg, 'height:48px');
            });

        $closure(
            ['editorSelection' => ['start' => 0, 'end' => 0]],
            ['icon' => 'academic-cap', 'size' => 'xl'],
            $component,
        );
    });

it('action uses default size when size is not provided',
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
            ->withArgs(function (array $commands): bool {
                $attrs = $commands[0]->arguments[0]['attrs'];
                $svg = $attrs['svg'];

                return $attrs['size'] === 'md'
                    && str_contains($svg, 'width:24px')
                    && str_contains($svg, 'height:24px');
            });

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

it('tiptap extension defines icon, align, and size attributes', function (): void {
    $extension = new FilamentRichEditorHeroiconsTipTapExtension;
    $attributes = $extension->addAttributes();

    expect($attributes)
        ->toBeArray()
        ->toHaveKey('icon')
        ->toHaveKey('align')
        ->toHaveKey('size')
        ->and($attributes['icon']['default'])
        ->toBeNull()
        ->and($attributes['align']['default'])
        ->toBe('inline')
        ->and($attributes['size']['default'])
        ->toBe('md');
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

it('tiptap extension renders svg for valid icon with inline alignment by default', function (): void {
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
        ->toContain('svg')
        ->not->toContain('float')
        ->not->toContain('display:block');
});

it('tiptap extension renders with left alignment', function (): void {
    $extension = new FilamentRichEditorHeroiconsTipTapExtension;

    $node = new stdClass;
    $node->attrs = new stdClass;
    $node->attrs->icon = 'academic-cap';
    $node->attrs->align = 'left';

    $result = $extension->renderHTML($node);

    expect($result['content'])
        ->toContain('float:left')
        ->toContain('margin-right:0.5rem');
});

it('tiptap extension renders with right alignment', function (): void {
    $extension = new FilamentRichEditorHeroiconsTipTapExtension;

    $node = new stdClass;
    $node->attrs = new stdClass;
    $node->attrs->icon = 'academic-cap';
    $node->attrs->align = 'right';

    $result = $extension->renderHTML($node);

    expect($result['content'])
        ->toContain('float:right')
        ->toContain('margin-left:0.5rem');
});

it('tiptap extension renders with center alignment', function (): void {
    $extension = new FilamentRichEditorHeroiconsTipTapExtension;

    $node = new stdClass;
    $node->attrs = new stdClass;
    $node->attrs->icon = 'academic-cap';
    $node->attrs->align = 'center';

    $result = $extension->renderHTML($node);

    expect($result['content'])
        ->toContain('display:flex')
        ->toContain('justify-content:center');
});

it('tiptap extension renders with default size (md = 24px)', function (): void {
    $extension = new FilamentRichEditorHeroiconsTipTapExtension;

    $node = new stdClass;
    $node->attrs = new stdClass;
    $node->attrs->icon = 'academic-cap';

    $result = $extension->renderHTML($node);

    expect($result['content'])
        ->toContain('width:24px')
        ->toContain('height:24px');
});

it('tiptap extension renders with small size', function (): void {
    $extension = new FilamentRichEditorHeroiconsTipTapExtension;

    $node = new stdClass;
    $node->attrs = new stdClass;
    $node->attrs->icon = 'academic-cap';
    $node->attrs->size = 'sm';

    $result = $extension->renderHTML($node);

    expect($result['content'])
        ->toContain('width:16px')
        ->toContain('height:16px');
});

it('tiptap extension renders with large size', function (): void {
    $extension = new FilamentRichEditorHeroiconsTipTapExtension;

    $node = new stdClass;
    $node->attrs = new stdClass;
    $node->attrs->icon = 'academic-cap';
    $node->attrs->size = 'lg';

    $result = $extension->renderHTML($node);

    expect($result['content'])
        ->toContain('width:32px')
        ->toContain('height:32px');
});

it('tiptap extension renders with xl size', function (): void {
    $extension = new FilamentRichEditorHeroiconsTipTapExtension;

    $node = new stdClass;
    $node->attrs = new stdClass;
    $node->attrs->icon = 'academic-cap';
    $node->attrs->size = 'xl';

    $result = $extension->renderHTML($node);

    expect($result['content'])
        ->toContain('width:48px')
        ->toContain('height:48px');
});

it('tiptap extension falls back to 24px for unknown size', function (): void {
    $extension = new FilamentRichEditorHeroiconsTipTapExtension;

    $node = new stdClass;
    $node->attrs = new stdClass;
    $node->attrs->icon = 'academic-cap';
    $node->attrs->size = 'unknown';

    $result = $extension->renderHTML($node);

    expect($result['content'])
        ->toContain('width:24px')
        ->toContain('height:24px');
});

it('plugin has default sizes', function (): void {
    $plugin = FilamentRichEditorHeroicons::make();

    expect($plugin->getSizes())
        ->toBe(['sm' => 16, 'md' => 24, 'lg' => 32, 'xl' => 48])
        ->and($plugin->getDefaultSize())
        ->toBe('md');
});

it('renders size label with smiley icon at correct size', function (): void {
    $plugin = FilamentRichEditorHeroicons::make();

    $label = $plugin->renderSizeLabel('lg');

    expect($label)
        ->toContain('svg')
        ->toContain('width:32px')
        ->toContain('height:32px');
});

it('renders size label with fallback for unknown size', function (): void {
    $plugin = FilamentRichEditorHeroicons::make();

    $label = $plugin->renderSizeLabel('unknown');

    expect($label)
        ->toContain('width:24px')
        ->toContain('height:24px');
});

it('renders size label with selected icon instead of fallback', function (): void {
    $plugin = FilamentRichEditorHeroicons::make();

    $label = $plugin->renderSizeLabel('md', 'academic-cap');

    expect($label)
        ->toContain('svg')
        ->toContain('width:24px')
        ->toContain('height:24px');
});

it('sizePixels returns correct pixels for each preset', function (): void {
    expect(FilamentRichEditorHeroiconsTipTapExtension::sizePixels('sm'))->toBe(16)
        ->and(FilamentRichEditorHeroiconsTipTapExtension::sizePixels('md'))->toBe(24)
        ->and(FilamentRichEditorHeroiconsTipTapExtension::sizePixels('lg'))->toBe(32)
        ->and(FilamentRichEditorHeroiconsTipTapExtension::sizePixels('xl'))->toBe(48);
});

it('sizePixels falls back to 24 for unknown size', function (): void {
    expect(FilamentRichEditorHeroiconsTipTapExtension::sizePixels('unknown'))->toBe(24);
});

it('plugin allows custom sizes', function (): void {
    $plugin = FilamentRichEditorHeroicons::make()
        ->sizes(['s' => 12, 'm' => 20, 'l' => 40])
        ->defaultSize('m');

    expect($plugin->getSizes())
        ->toBe(['s' => 12, 'm' => 20, 'l' => 40])
        ->and($plugin->getDefaultSize())
        ->toBe('m');
});

it('loads translations', function (): void {
    expect(__('filament-rich-editor-heroicons::rich-editor-heroicons.action_label'))
        ->not->toBe('filament-rich-editor-heroicons::rich-editor-heroicons.action_label')
        ->and(__('filament-rich-editor-heroicons::rich-editor-heroicons.heading'))
        ->not->toBe('filament-rich-editor-heroicons::rich-editor-heroicons.heading')
        ->and(__('filament-rich-editor-heroicons::rich-editor-heroicons.label'))
        ->not->toBe('filament-rich-editor-heroicons::rich-editor-heroicons.label')
        ->and(__('filament-rich-editor-heroicons::rich-editor-heroicons.below_content', ['link-heroicon' => 'test']))
        ->not->toBe('filament-rich-editor-heroicons::rich-editor-heroicons.below_content')
        ->and(__('filament-rich-editor-heroicons::rich-editor-heroicons.alignment_label'))
        ->not->toBe('filament-rich-editor-heroicons::rich-editor-heroicons.alignment_label')
        ->and(__('filament-rich-editor-heroicons::rich-editor-heroicons.alignment_inline'))
        ->not->toBe('filament-rich-editor-heroicons::rich-editor-heroicons.alignment_inline')
        ->and(__('filament-rich-editor-heroicons::rich-editor-heroicons.size_label'))
        ->not->toBe('filament-rich-editor-heroicons::rich-editor-heroicons.size_label')
        ->and(__('filament-rich-editor-heroicons::rich-editor-heroicons.size_md', ['px' => 24]))
        ->not->toBe('filament-rich-editor-heroicons::rich-editor-heroicons.size_md');
});
