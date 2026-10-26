<?php

namespace App\Filament\Resources\Permissions\Schemas;

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
                            ->label('الاسم البرمجي للصلاحية')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(100)
                            ->regex('/^[a-z][a-z0-9_]*\.[a-z][a-z0-9_]*$/')
                            ->validationMessages([
                                'regex' => 'يجب أن يكون الاسم بصيغة مثل citizens.view أو passports.approve.',
                            ])
                            ->placeholder('مثال: citizens.view')
                            ->helperText('يتكون من اسم الوحدة ثم نقطة ثم الإجراء.'),

                        TextInput::make('name_ar')
                            ->label('اسم الصلاحية بالعربية')
                            ->required()
                            ->maxLength(100)
                            ->placeholder('مثال: عرض المواطنين'),

                        TextInput::make('module')
                            ->label('الوحدة')
                            ->required()
                            ->maxLength(50)
                            ->regex('/^[a-z][a-z0-9_]*$/')
                            ->placeholder('مثال: citizens'),

                        TextInput::make('action')
                            ->label('الإجراء')
                            ->required()
                            ->maxLength(50)
                            ->regex('/^[a-z][a-z0-9_]*$/')
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
                            ->placeholder('وصف الصلاحية واستخدامها'),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
