<?php

namespace App\Filament\Resources\Backups\Schemas;

use Filament\Forms\Components\Hidden;
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
                    ->description(
                        'سيتم إنشاء نسخة احتياطية يدوية لقاعدة البيانات.'
                    )
                    ->icon('heroicon-o-archive-box')
                    ->schema([
                        Hidden::make('type')
                            ->default('manual'),

                        Textarea::make('notes')
                            ->label('ملاحظات')
                            ->placeholder(
                                'أدخل أي ملاحظات مرتبطة بهذه النسخة...'
                            )
                            ->rows(4)
                            ->maxLength(1000)
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
