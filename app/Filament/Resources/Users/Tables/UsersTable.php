<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Support\Collection;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                ImageColumn::make('profile_photo')
                    ->label('الصورة')
                    ->getStateUsing(
                        fn ($record) => $record->profile_photo
                            ? route(
                                'users.profile-photo',
                                [
                                    'path' => basename($record->profile_photo),
                                ]
                            )
                            : null
                    )
                    ->circular()
                    ->size(40)
                    ->placeholder('لا توجد')
                    ->alignCenter(),

                TextColumn::make('name')
                    ->label('اسم المستخدم')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->icon('heroicon-o-user'),

                TextColumn::make('full_name')
                    ->label('الاسم الكامل')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->label('البريد الإلكتروني')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('تم نسخ البريد الإلكتروني'),

                TextColumn::make('phone')
                    ->label('رقم الهاتف')
                    ->searchable()
                    ->copyable()
                    ->sortable()
                    ->copyMessage('تم نسخ رقم الهاتف'),

                TextColumn::make('branch.name')
                    ->label('الفرع')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('roles.name')
                    ->label('الدور')
                    ->searchable()
                    ->placeholder('بدون دور'),

                TextColumn::make('status')
                    ->label('الحالة')
                    ->formatStateUsing(
                        fn (string $state): string => match ($state) {
                            'active' => 'نشط',
                            'inactive' => 'غير نشط',
                            'blocked' => 'محظور',
                            default => $state,
                        }
                    )
                    ->sortable(),

                TextColumn::make('last_login_at')
                    ->label('آخر دخول')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->placeholder('لم يسجل دخول')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('last_login_ip')
                    ->label('عنوان IP')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('تم نسخ عنوان IP')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('تاريخ التسجيل')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('deleted_at')
                    ->label('تاريخ الحذف')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

            ])

            ->filters([

                SelectFilter::make('branch_id')
                    ->label('الفرع')
                    ->relationship('branch', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('status')
                    ->label('الحالة')
                    ->options([
                        'active' => 'نشط',
                        'inactive' => 'غير نشط',
                        'blocked' => 'محظور',
                    ]),

                SelectFilter::make('roles')
                    ->label('الدور')
                    ->relationship('roles', 'name')
                    ->searchable()
                    ->preload(),

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
                        ->modalHeading('حذف المستخدمين المحددين')
                        ->modalDescription(
                            'سيتم حذف المستخدمين المسموح بحذفهم، وسيتم تجاوز أي مستخدم هو مدير لفرع.'
                        )
                        ->modalSubmitActionLabel('نعم، حذف')
                        ->action(function (Collection $records): void {
                            $deletedCount = 0;
                            $skippedCount = 0;

                            foreach ($records as $record) {
                                if (! $record->canBeDeleted()) {
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
                                        "تم حذف {$deletedCount} مستخدم، بينما تم تجاوز {$skippedCount} مستخدم لأنهم مديرو فروع."
                                    )
                                    ->warning()
                                    ->send();

                                return;
                            }

                            Notification::make()
                                ->title('تم حذف المستخدمين')
                                ->body(
                                    "تم حذف {$deletedCount} مستخدم بنجاح."
                                )
                                ->success()
                                ->send();
                        }),

                    RestoreBulkAction::make()
                        ->label('استعادة المحدد')
                        ->requiresConfirmation()
                        ->modalHeading('استعادة المستخدمين')
                        ->modalDescription(
                            'هل أنت متأكد من استعادة المستخدمين المحددين؟'
                        )
                        ->modalSubmitActionLabel('نعم، استعادة'),

                    ForceDeleteBulkAction::make()
                        ->label('حذف نهائي')
                        ->requiresConfirmation()
                        ->modalHeading('حذف نهائي')
                        ->modalDescription(
                            'سيتم حذف المستخدمين المسموح بحذفهم نهائيًا، وسيتم تجاوز أي مستخدم هو مدير لفرع.'
                        )
                        ->modalSubmitActionLabel('نعم، حذف نهائي')
                        ->action(function (Collection $records): void {
                            $deletedCount = 0;
                            $skippedCount = 0;

                            foreach ($records as $record) {
                                if (! $record->canBeDeleted()) {
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
                                        "تم حذف {$deletedCount} مستخدم نهائيًا، بينما تم تجاوز {$skippedCount} مستخدم لأنهم مديرو فروع."
                                    )
                                    ->warning()
                                    ->send();

                                return;
                            }

                            Notification::make()
                                ->title('تم الحذف النهائي')
                                ->body(
                                    "تم حذف {$deletedCount} مستخدم نهائيًا بنجاح."
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
