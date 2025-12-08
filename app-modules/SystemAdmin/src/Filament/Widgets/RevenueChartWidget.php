<?php

namespace Relaticle\SystemAdmin\Filament\Widgets;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use Filament\Widgets\ChartWidget;


class RevenueChartWidget extends ChartWidget
{
    protected ?string $heading = 'Revenue per Month';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $data = Payment::query()
            ->selectRaw('DATE_FORMAT(payment_date, "%Y-%m") as date, sum(amount) as aggregate')
            ->whereBetween('payment_date', [now()->startOfYear(), now()->endOfYear()])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Revenue',
                    'data' => $data->pluck('aggregate')->toArray(),
                ],
            ],
            'labels' => $data->pluck('date')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
