<?php

namespace App\Filament\Resources\Backups;

use App\Filament\Resources\Backups\Pages\CreateBackup;
use App\Filament\Resources\Backups\Pages\ListBackups;
use App\Filament\Resources\Backups\Pages\ViewBackup;
use App\Filament\Resources\Backups\Schemas\BackupInfolist;
use App\Filament\Resources\Backups\Tables\BackupsTable;
use App\Models\Backup;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class BackupResource extends Resource
{
    protected static ?string $model = Backup::class;

    protected static string|BackedEnum|null $navigationIcon =
        Heroicon::OutlinedCircleStack;

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationLabel = 'النسخ الاحتياطي';

    protected static ?string $modelLabel = 'نسخة احتياطية';

    protected static ?string $pluralModelLabel = 'النسخ الاحتياطية';

    public static function getNavigationGroup(): ?string
    {
        return 'التقارير والإدارة';
    }

    public static function infolist(Schema $schema): Schema
    {
        return BackupInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BackupsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBackups::route('/'),
            'create' => CreateBackup::route('/create'),
            'view' => ViewBackup::route('/{record}'),
        ];
    }
}
