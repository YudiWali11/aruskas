<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class StatsOverview extends BaseWidget
{
    use InteractsWithPageFilters;

    protected function getStats(): array
{
    $filters = $this->filters ?? $this->pageFilters ?? [];

    $startDate = filled($filters['startDate'] ?? null)
        ? Carbon::parse($filters['startDate'])->startOfDay()
        : null;

    $endDate = filled($filters['endDate'] ?? null)
        ? Carbon::parse($filters['endDate'])->endOfDay()
        : null;

    $pemasukan = Transaction::incomes()
        ->whereBetween('date_transaction', [$startDate, $endDate])
        ->sum('amount');

    $pengeluaran = Transaction::expenses()
        ->whereBetween('date_transaction', [$startDate, $endDate])
        ->sum('amount');

    return [

        Stat::make('Pemasukan', 'Rp ' . number_format($pemasukan, 0, ',', '.')),
        Stat::make('Pengeluaran', 'Rp ' . number_format($pengeluaran, 0, ',', '.')),
        Stat::make('Selisih', 'Rp ' . number_format($pemasukan - $pengeluaran, 0, ',', '.')),
    ];
}
}
