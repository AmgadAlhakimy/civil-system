<?php

namespace App\Filament\Resources\Permissions\Schemas;

use Illuminate\Support\Str;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class PermissionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Grid::make(2)
                    ->schema([

                        TextInput::make('name')
                            ->label('اسم الصلاحية')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(100)
                            ->placeholder('مثال: citizens.view')
                            ->helperText('الاسم البرمجي للصلاحية'),

                        TextInput::make('name_ar')
                            ->label('اسم الصلاحية بالعربي')
                            ->required()
                            ->maxLength(100)
                            ->placeholder('مثال: عرض المواطنين'),

                        TextInput::make('module')
                            ->label('الوحدة')
                            ->required()
                            ->maxLength(50)
                            ->placeholder('مثال: citizens'),

                        TextInput::make('action')
                            ->label('الإجراء')
                            ->required()
                            ->maxLength(50)
                            ->placeholder('مثال: view'),

                        TextInput::make('guard_name')
                            ->label('الحارس')
                            ->default('web')
                            ->required()
                            ->maxLength(100)
                            ->disabled()
                            ->dehydrated(),

                        Textarea::make('description')
                            ->label('الوصف')
                            ->rows(3)
                            ->maxLength(500)
                            ->placeholder('وصف الصلاحية'),

                    ])
                    ->columnSpanFull(),
            ]);
    }
}
