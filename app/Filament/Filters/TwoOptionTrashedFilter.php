<?php

namespace App\Filament\Filters;

use Filament\Forms\Components\Select;
use Filament\Tables\Filters\TrashedFilter;

class TwoOptionTrashedFilter extends TrashedFilter
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->trueLabel('السجلات المحذوفة فقط');

        $this->queries(
            true: fn ($query) => $query->onlyTrashed(),
            false: fn ($query) => $query->withoutTrashed(),
            blank: fn ($query) => $query->withoutTrashed(),
        );
    }

    public function getFormField(): Select
    {
        return Select::make('value')
            ->label($this->getLabel())
            ->options([
                1 => 'السجلات المحذوفة فقط',
            ])
            ->placeholder($this->getPlaceholder())
            ->native()
            ->selectablePlaceholder(true);
    }
}
