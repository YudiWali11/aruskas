<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use App\Models\Transaction;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Carbon;
class WidgetIncomeChart extends ChartWidget
{
    protected static ?string $heading = 'Pemasukan';
    protected static string $color = 'success';

    use InteractsWithPageFilters;

    protected function getData(): array
    {
        $filters = $this->filters ?? [];

        $startDate = filled($filters['startDate'] ?? null)
            ? Carbon::parse($filters['startDate'])->startOfDay()
            : now()->startOfMonth();

        $endDate = filled($filters['endDate'] ?? null)
            ? Carbon::parse($filters['endDate'])->endOfDay()
            : now()->endOfDay();

        $data = Trend::query(Transaction::query()->incomes())
            ->between(
                start: $startDate,
                end: $endDate
            )
            ->perDay()
            ->sum('amount');

        if ($startDate->diffInDays($endDate) > 365) {
                $startDate = $endDate->copy()->subYear();
            }

        return [
            'datasets' => [
                [
                    'label' => 'Pemasukan per Hari',
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $data->map(fn (TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
