<?php

namespace App\Filament\Widgets;

use App\Models\Branch;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class BranchesOverview extends TableWidget
{
    protected static ?string $heading = 'فروع الأحوال المدنية';

    protected static ?int $sort = 4;

    protected int | string | array $columnSpan = 1;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Branch::query()
                    ->withCount('users')
            )
            ->columns([
                TextColumn::make('name')
                    ->label('الفرع')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),

                TextColumn::make('users_count')
                    ->label('الموظفون')
                    ->badge()
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label('الحالة')
                    ->boolean(),
            ])
            ->defaultSort('name')
            ->paginated(false)
            ->striped(false);
    }
}
