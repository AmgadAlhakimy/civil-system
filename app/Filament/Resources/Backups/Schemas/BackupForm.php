<?php

namespace App\Filament\Resources\Backups\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BackupForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('إنشاء نسخة احتياطية')
                    ->description('اختر نوع النسخة وأضف ملاحظات اختيارية قبل بدء عملية النسخ الاحتياطي.')
                    ->icon('heroicon-o-archive-box')
                    ->schema([
                        Select::make('type')
                            ->label('نوع النسخة')
                            ->options([
                                'manual' => 'يدوية',
                                'automatic' => 'تلقائية',
                            ])
                            ->default('manual')
                            ->required()
                            ->native(false)
                            ->validationMessages([
                                'required' => 'يرجى اختيار نوع النسخة.',
                            ]),

                        Textarea::make('notes')
                            ->label('ملاحظات')
                            ->placeholder('أدخل أي ملاحظات مرتبطة بهذه النسخة...')
                            ->rows(4)
                            ->maxLength(1000)
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
