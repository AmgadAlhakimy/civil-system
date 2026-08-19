<?php

namespace App\Filament\Widgets;

use App\Models\Activity;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class RecentActivities extends TableWidget
{
    protected static ?string $heading = 'آخر العمليات';

    protected static ?int $sort = 5;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Activity::query()
                    ->with(['causer', 'subject'])
            )
            ->columns([
                TextColumn::make('causer.name')
                    ->label('المستخدم')
                    ->default('النظام')
                    ->searchable()
                    ->weight('medium'),

                TextColumn::make('event')
                    ->label('العملية')
                    ->badge()
                    ->formatStateUsing(function (?string $state): string {
                        return match ($state) {
                            'created' => 'إضافة',
                            'updated' => 'تعديل',
                            'deleted' => 'حذف',
                            'restored' => 'استعادة',
                            default => $state ?? 'غير معروف',
                        };
                    })
                    ->color(function (?string $state): string {
                        return match ($state) {
                            'created' => 'success',
                            'updated' => 'warning',
                            'deleted' => 'danger',
                            'restored' => 'info',
                            default => 'gray',
                        };
                    }),

                TextColumn::make('subject_type')
                    ->label('نوع السجل')
                    ->formatStateUsing(function (?string $state): string {
                        if (! $state) {
                            return 'غير محدد';
                        }

                        return match (class_basename($state)) {
                            'Citizen' => 'مواطن',
                            'Passport' => 'جواز سفر',
                            'FamilyCard' => 'بطاقة أسرة',
                            'IdentityCard' => 'بطاقة شخصية',
                            'BirthCertificate' => 'شهادة ميلاد',
                            'DeathCertificate' => 'شهادة وفاة',
                            'Branch' => 'فرع',
                            'User' => 'مستخدم',
                            default => class_basename($state),
                        };
                    })
                    ->badge(),

                TextColumn::make('description')
                    ->label('الوصف')
                    ->limit(50)
                    ->tooltip(fn ($state) => $state),

                TextColumn::make('created_at')
                    ->label('التاريخ')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated(false);
    }
}
