<?php

namespace App\Filament\Resources\Items\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ItemInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name')
            ->label('اسم المادة'),
                TextEntry::make('price')
                    ->label('السعر')
                    ->money(),
                TextEntry::make('quantity')
                    ->label('الكمية')
                    ->numeric(),
                TextEntry::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->label('تاريخ التعديل')
                    ->dateTime(),
            ]);
    }
}
