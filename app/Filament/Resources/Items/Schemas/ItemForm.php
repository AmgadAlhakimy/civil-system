<?php

namespace App\Filament\Resources\Items\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use function Laravel\Prompts\table;

class ItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')

                    ->required()
            ->label('اسم المادة'),
                Textarea::make('description')
                    ->default(null)
                    ->columnSpanFull()
                    ->label('وصف المادة'),
                TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->default(0.0)
                    ->prefix('$')
                    ->label('سعر المادة'),
                TextInput::make('quantity')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->label('عدد المواد'),
            ]);
    }
}
