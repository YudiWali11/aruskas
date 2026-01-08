<?php

namespace App\Filament\Pages;

use Filament\Forms\Form;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Forms\Get;

class Dashboard extends BaseDashboard
{
    protected static ?string $title = 'Dashboard';

    use BaseDashboard\Concerns\HasFiltersForm;

    public function filtersForm(Form $form): Form
    {
        return $form->schema([
            Section::make()
                ->schema([
                    DatePicker::make('startDate')->default(now()->startOfMonth())->required(),
                    DatePicker::make('endDate')->default(now())->required(),
                    ])
                ->columns(2)
                ->columnSpanFull(),
        ]);
    }
}
