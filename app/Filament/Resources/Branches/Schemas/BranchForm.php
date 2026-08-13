<?php

namespace App\Filament\Resources\Branches\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class BranchForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                TextInput::make('code')
                    ->label('رمز الفرع')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(20),

                TextInput::make('name')
                    ->label('اسم الفرع')
                    ->required()
                    ->maxLength(100),

                Textarea::make('address')
                    ->label('عنوان الفرع')
                    ->required()
                    ->rows(3)
                    ->columnSpanFull(),

                TextInput::make('phone')
                    ->label('رقم الهاتف')
                    ->tel()
                    ->required()
                    ->maxLength(20),

                TextInput::make('email')
                    ->label('البريد الإلكتروني')
                    ->email()
                    ->nullable()
                    ->maxLength(100),

                TextInput::make('manager_name')
                    ->label('اسم مدير الفرع')
                    ->nullable()
                    ->columnSpanFull()
                    ->maxLength(100),
                Grid::make(2)
                    ->schema([
                        Toggle::make('is_active')
                            ->label('الفرع نشط')
                            ->default(true)
                            ->required()
                            ->inline(false)
                            ->columnStart(2),
                    ])
                    ->columnSpanFull(),

            ]);
    }
}
