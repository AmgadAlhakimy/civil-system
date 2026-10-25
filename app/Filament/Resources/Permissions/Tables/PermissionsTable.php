<?php

namespace App\Filament\Resources\Permissions\Tables;

use App\Models\Permission;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PermissionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name_ar')
                    ->label('اسم الصلاحية')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('name')
                    ->label('الاسم البرمجي')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                TextColumn::make('module')
                    ->label('الوحدة')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('action')
                    ->label('الإجراء')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('guard_name')
                    ->label('الحارس')
                    ->sortable(),

                TextColumn::make('description')
                    ->label('الوصف')
                    ->limit(50)
                    ->tooltip(
                        fn (Permission $record): ?string => $record->description
                    ),
            ])
            ->filters([
                SelectFilter::make('module')
                    ->label('الوحدة')
                    ->options(
                        fn (): array => Permission::query()
                            ->whereNotNull('module')
                            ->distinct()
                            ->orderBy('module')
                            ->pluck('module', 'module')
                            ->all()
                    ),

                SelectFilter::make('action')
                    ->label('الإجراء')
                    ->options(
                        fn (): array => Permission::query()
                            ->whereNotNull('action')
                            ->distinct()
                            ->orderBy('action')
                            ->pluck('action', 'action')
                            ->all()
                    ),
            ])
            ->recordActions([
                EditAction::make()
                    ->label('تعديل'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('حذف المحدد')
                        ->authorize(
                            fn (): bool => auth()->user()?->can('permissions.delete') ?? false
                        ),
                ]),
            ]);
    }
}
