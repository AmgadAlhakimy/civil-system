<?php

namespace App\Filament\Resources\Roles\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class RoleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Grid::make(2)
                    ->schema([

                        TextInput::make('name')
                            ->label('اسم الدور')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(100)
                            ->placeholder('مثال: admin'),

                        TextInput::make('name_ar')
                            ->label('اسم الدور بالعربي')
                            ->required()
                            ->maxLength(100)
                            ->placeholder('مثال: مدير النظام'),

                    ])
                    ->columnSpanFull(),

                Select::make('permissions')
                    ->label('الصلاحيات')
                    ->multiple()
                    ->relationship(
                        name: 'permissions',
                        titleAttribute: 'name_ar'
                    )
                    ->preload()
                    ->searchable()
                    ->columnSpanFull(),

            ]);
    }
}
