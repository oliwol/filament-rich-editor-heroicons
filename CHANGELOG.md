# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/), and this project adheres to [Semantic Versioning](https://semver.org/).

## [Unreleased]

## [1.0.0] - 2026-03-25

### Added

- Searchable icon picker with SVG previews in the dropdown (#26)
- Icon alignment options: inline, left, right, center (#27)
- Configurable icon sizes: sm (16px), md (24px), lg (32px), xl (48px) (#28)
- Developer API to customize sizes and default size via `sizes()` and `defaultSize()` (#28)
- Translations for French, Spanish, Portuguese (Brazil), Dutch, Italian, Turkish, Arabic, and Chinese Simplified (#30)
- Demo screenshot in README (#25)

### Changed

- Bumped `ramsey/composer-install` from 3 to 4 (#9)

## [0.0.2] - 2026-02-05

### Fixed

- Registered heroicons script as a module to resolve unexpected token error (#8)

### Changed

- Simplified CI workflow and updated README badges (#4, #5, #6, #7)

## [0.0.1] - 2026-01-22

### Added

- Initial release
- Heroicon picker modal for Filament RichEditor (TipTap)
- Inline SVG insertion of outline Heroicons
- Server-side TipTap extension for HTML rendering via `RichContentRenderer`
- Client-side TipTap node with `data-icon` and `data-svg` attributes
- English and German translations
- Unit tests with 100% code coverage

[Unreleased]: https://github.com/oliwol/filament-rich-editor-heroicons/compare/v0.0.2...HEAD
[0.0.2]: https://github.com/oliwol/filament-rich-editor-heroicons/compare/v0.0.1...v0.0.2
[0.0.1]: https://github.com/oliwol/filament-rich-editor-heroicons/releases/tag/v0.0.1
