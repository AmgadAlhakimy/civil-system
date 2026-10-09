<?php

namespace App\Filament\Resources\Branches\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Support\Collection;

class BranchesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('code')
                    ->label('رمز الفرع')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('تم نسخ رمز الفرع')
                    ->weight('bold'),

                TextColumn::make('name')
                    ->label('اسم الفرع')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('address')
                    ->label('عنوان الفرع')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('phone')
                    ->label('رقم الهاتف')
                    ->searchable()
                    ->copyable()
                    ->sortable()
                    ->copyMessage('تم نسخ رقم الهاتف'),

                TextColumn::make('email')
                    ->label('البريد الإلكتروني')
                    ->searchable()
                    ->copyable()
                    ->sortable()
                    ->copyMessage('تم نسخ البريد الإلكتروني'),

                TextColumn::make('manager.full_name')
                    ->label('مدير الفرع')
                    ->searchable()
                    ->sortable()
                    ->placeholder('غير محدد'),

                IconColumn::make('is_active')
                    ->label('الحالة')
                    ->boolean()
                    ->sortable()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle'),

                TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('آخر تحديث')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

            ])

            ->filters([

                SelectFilter::make('is_active')
                    ->label('حالة الفرع')
                    ->options([
                        1 => 'نشط',
                        0 => 'غير نشط',
                    ]),

                TrashedFilter::make()
                    ->label('سلة المحذوفات'),

            ])

            ->recordActions([

                ViewAction::make()
                    ->label('عرض')
                    ->icon('heroicon-o-eye')
                    ->color('primary'),

                EditAction::make()
                    ->label('تعديل')
                    ->icon('heroicon-o-pencil-square'),

            ])

            ->toolbarActions([

                BulkActionGroup::make([

                    DeleteBulkAction::make()
                        ->label('حذف المحدد')
                        ->requiresConfirmation()
                        ->successNotification(null)
                        ->modalHeading('حذف الفروع المحددة')
                        ->modalDescription(
                            'سيتم حذف الفروع المسموح بحذفها، وسيتم تجاوز أي فرع مرتبط بمستخدمين.'
                        )
                        ->modalSubmitActionLabel('نعم، حذف')
                        ->action(function (Collection $records): void {
                            $deletedCount = 0;
                            $skippedCount = 0;

                            foreach ($records as $record) {
                                if ($record->users()->exists()) {
                                    $skippedCount++;

                                    continue;
                                }

                                $record->delete();
                                $deletedCount++;
                            }

                            if ($skippedCount > 0) {
                                Notification::make()
                                    ->title('تمت عملية الحذف')
                                    ->body(
                                        "تم حذف {$deletedCount} فرع، بينما تم تجاوز {$skippedCount} فرع لأن لديها مستخدمين مرتبطين بها."
                                    )
                                    ->warning()
                                    ->send();

                                return;
                            }

                            Notification::make()
                                ->title('تم حذف الفروع')
                                ->body(
                                    "تم حذف {$deletedCount} فرع بنجاح."
                                )
                                ->success()
                                ->send();
                        }),

                    RestoreBulkAction::make()
                        ->label('استعادة المحدد')
                        ->requiresConfirmation()
                        ->modalHeading('استعادة الفروع')
                        ->modalDescription(
                            'هل أنت متأكد من استعادة الفروع المحددة؟'
                        )
                        ->modalSubmitActionLabel('نعم، استعادة'),

                    ForceDeleteBulkAction::make()
                        ->label('حذف نهائي')
                        ->requiresConfirmation()
                        ->successNotification(null)
                        ->modalHeading('حذف نهائي')
                        ->modalDescription(
                            'سيتم حذف الفروع المسموح بحذفها نهائيًا، وسيتم تجاوز أي فرع مرتبط بمستخدمين.'
                        )
                        ->modalSubmitActionLabel('نعم، حذف نهائي')
                        ->action(function (Collection $records): void {
                            $deletedCount = 0;
                            $skippedCount = 0;

                            foreach ($records as $record) {
                                if ($record->users()->exists()) {
                                    $skippedCount++;

                                    continue;
                                }

                                $record->forceDelete();
                                $deletedCount++;
                            }

                            if ($skippedCount > 0) {
                                Notification::make()
                                    ->title('تمت عملية الحذف النهائي')
                                    ->body(
                                        "تم حذف {$deletedCount} فرع نهائيًا، بينما تم تجاوز {$skippedCount} فرع لأن لديها مستخدمين مرتبطين بها."
                                    )
                                    ->warning()
                                    ->send();

                                return;
                            }

                            Notification::make()
                                ->title('تم الحذف النهائي')
                                ->body(
                                    "تم حذف {$deletedCount} فرع نهائيًا بنجاح."
                                )
                                ->success()
                                ->send();
                        }),

                ])
                    ->label('إجراءات جماعية'),

            ])

            ->defaultSort('created_at', 'desc');
    }
}
