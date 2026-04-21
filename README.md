# 🦸 Filament Rich Editor Heroicons

[![Latest Version on Packagist](https://img.shields.io/packagist/v/oliwol/filament-rich-editor-heroicons.svg?style=flat-square)](https://packagist.org/packages/oliwol/filament-rich-editor-heroicons)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/oliwol/filament-rich-editor-heroicons/tests.yml?label=tests&style=flat-square)](https://github.com/oliwol/filament-rich-editor-heroicons/actions)
[![License](https://img.shields.io/packagist/l/oliwol/filament-rich-editor-heroicons.svg?style=flat-square)](https://github.com/oliwol/filament-rich-editor-heroicons/blob/0.x/LICENSE.md)

A Filament v4/v5 plugin that adds a Heroicon picker to the RichEditor (TipTap). 

![Demo](art/demo.gif)

Search and insert Heroicons as inline SVGs directly into the editor content. Supports outline, solid, and mini styles with customizable size, alignment, and color.

---

## 🚀 Installation

Install the package via Composer:

```bash
composer require oliwol/filament-rich-editor-heroicons
```

## ⚡️ Quick Start

```php
use Filament\Forms\Components\RichEditor;
use Oliwol\FilamentRichEditorHeroicons\FilamentRichEditorHeroicons;

RichEditor::make('content')
    ->toolbarButtons([
        'bold',
        'italic',
        'link',
        'addHeroicon',
    ])
    ->plugins([
        FilamentRichEditorHeroicons::make(),
    ])
```

## 🛠️ Usage

### Editor Integration

Add ```FilamentRichEditorHeroicons::make()``` to the ```plugins()``` array of your ```RichEditor``` component and include ```addHeroicon``` in the ```toolbarButtons()```:

```php
use Filament\Forms\Components\RichEditor;
use Oliwol\FilamentRichEditorHeroicons\FilamentRichEditorHeroicons;

RichEditor::make('content')
    ->toolbarButtons([
        'bold',
        'italic',
        'underline',
        'link',
        'addHeroicon',
        // ... other buttons
    ])
    ->plugins([
        FilamentRichEditorHeroicons::make(),
    ])
```

### Rendering Stored Content

When rendering stored content outside the editor (e.g. in a Blade view or model accessor), register the plugin with ```RichContentRenderer```:

```php
use Filament\Forms\Components\RichEditor\RichContentRenderer;
use Oliwol\FilamentRichEditorHeroicons\FilamentRichEditorHeroicons;

RichContentRenderer::make($this->html)
    ->plugins([
        FilamentRichEditorHeroicons::make(),
    ])
```

### Styles

By default, **outline**, **solid**, and **mini** styles are available. Mini icons use a 20x20 viewport, designed for smaller UI elements.

To restrict to specific styles:

```php
FilamentRichEditorHeroicons::make()
    ->styles(['outline', 'solid'])
```

### Alignment & Size

The picker modal lets users choose alignment and size for each icon. You can customize the available sizes and default size via the plugin API:

```php
FilamentRichEditorHeroicons::make()
    ->defaultSize('lg')
    ->sizes([
        'sm' => 16,
        'md' => 24,
        'lg' => 32,
        'xl' => 48,
    ])
```

**Alignment options:** Inline (default), Left, Center, Right

**Size presets:** S (16px), M (24px, default), L (32px), XL (48px)

### Color

The picker modal includes a color picker to set the icon color. The default color is `#000000` (black).

### Accessibility

The picker modal includes an optional **Accessibility label** field. Screen readers use this label to describe the icon.

- **Leave empty** for decorative icons → the SVG gets `aria-hidden="true"` (invisible to screen readers)
- **Fill in a label** (e.g. `"warning"`) → the SVG gets `role="img"` and `aria-label="warning"`

This follows the [WAI-ARIA authoring practices](https://www.w3.org/WAI/tutorials/images/decorative/) for decorative vs. informative images.

### Editing Inserted Icons

Click on any inserted icon in the editor to re-open the picker modal pre-filled with the current settings. Changes update the icon in place.

All settings are persisted in the editor content and applied consistently when rendering via `RichContentRenderer`.

## ⚙️ How it works

1. Clicking the toolbar button opens a modal with a searchable dropdown of [Heroicons](https://heroicons.com/) with SVG previews.
2. Select a style (outline/solid/mini), alignment, size, color, and an optional accessibility label.
3. The icon is rendered as an inline SVG element and inserted into the editor content.
4. Clicking an existing icon re-opens the modal for editing.
5. All properties are stored as data attributes and applied on render.

## 🌍 Translations

The package ships with translations for the following languages:

| Language | Code |
|---|---|
| English | `en` |
| German | `de` |
| French | `fr` |
| Spanish | `es` |
| Portuguese (Brazil) | `pt_BR` |
| Dutch | `nl` |
| Italian | `it` |
| Turkish | `tr` |
| Arabic | `ar` |
| Chinese (Simplified) | `zh_CN` |

You can publish and customize them:

```bash
php artisan vendor:publish --tag="filament-rich-editor-heroicons-translations"
```

## ✅ Testing

```bash
composer test
```

## 📝 Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## 📄 License

This package is open-sourced software licensed under the [MIT license](LICENSE.md).
