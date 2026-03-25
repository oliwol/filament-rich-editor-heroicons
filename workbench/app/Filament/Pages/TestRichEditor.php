<?php

declare(strict_types=1);

namespace Workbench\App\Filament\Pages;

use BackedEnum;
use Filament\Forms\Components\RichEditor;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Oliwol\FilamentRichEditorHeroicons\FilamentRichEditorHeroicons;

final class TestRichEditor extends Page
{
    public ?array $data = [];

    protected static BackedEnum|string|null $navigationIcon = Heroicon::OutlinedPencilSquare;

    protected static ?string $title = 'Rich Editor Heroicons';

    public function content(Schema $schema): Schema
    {
        return $schema
            ->schema([
                RichEditor::make('content')
                    ->label('Content')
                    ->toolbarButtons([
                        'bold',
                        'italic',
                        'underline',
                        'link',
                        'addHeroicon',
                        'bulletList',
                        'orderedList',
                        'blockquote',
                        'h2',
                        'h3',
                    ])
                    ->plugins([
                        FilamentRichEditorHeroicons::make(),
                    ])
                    ->columnSpanFull(),
            ])
            ->statePath('data');
    }
}
