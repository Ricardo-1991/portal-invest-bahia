<?php

namespace App\Filament\Resources\PageContents;

use App\Filament\Resources\PageContents\Pages\EditPageContent;
use App\Filament\Resources\PageContents\Pages\ListPageContents;
use App\Filament\Resources\PageContents\Schemas\PageContentForm;
use App\Filament\Resources\PageContents\Tables\PageContentsTable;
use App\Models\PageContent;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PageContentResource extends Resource
{
    protected static ?string $model = PageContent::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static ?string $modelLabel = 'Página';

    protected static ?string $pluralModelLabel = 'Páginas';

    protected static ?string $recordTitleAttribute = 'key';

    /** Somente o administrador gerencia o conteúdo das páginas fixas. */
    public static function canAccess(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    /** O painel expõe somente a Home; as demais páginas ficam no código. */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('key', 'home');
    }

    public static function form(Schema $schema): Schema
    {
        return PageContentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PageContentsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPageContents::route('/'),
            'edit' => EditPageContent::route('/{record}/edit'),
        ];
    }
}
