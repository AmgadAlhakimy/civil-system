<?php

namespace App\Filament\Resources\Roles\Tables;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RolesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('اسم الدور')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('permissions_count')
                    ->label('عدد الصلاحيات')
                    ->counts('permissions')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->label('آخر تحديث')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->recordActionsColumnLabel('العمليات')
            ->recordActions([
                ViewAction::make()
                    ->label('عرض')
                    ->visible(
                        fn (): bool =>
                            auth()->user()?->can('roles.view') ?? false
                    ),

                EditAction::make()
                    ->label('تعديل')
                    ->visible(
                        fn (): bool =>
                            auth()->user()?->can('roles.update') ?? false
                    ),
            ]);
    }
}
