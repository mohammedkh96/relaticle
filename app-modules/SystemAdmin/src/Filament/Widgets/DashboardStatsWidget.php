<?php

declare(strict_types=1);

namespace Relaticle\SystemAdmin\Filament\Widgets;

use App\Models\Company;
use App\Models\Event;
use App\Models\Invoice;
use App\Models\Participation;
use App\Models\Payment;
use App\Models\Visitor;
use App\Enums\PaymentStatus;
use App\Enums\InvoiceStatus;
use App\Enums\ParticipationStatus;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardStatsWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';

    protected function getStats(): array
    {
        $currentYear = date('Y');
        $currentEvent = Event::where('year', $currentYear)->first();

        // Calculate trends - cast to float for number_format
        $lastMonthPayments = (float) Payment::where('status', PaymentStatus::PAID)
            ->whereBetween('payment_date', [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()])
            ->sum('amount');

        $thisMonthPayments = (float) Payment::where('status', PaymentStatus::PAID)
            ->whereBetween('payment_date', [now()->startOfMonth(), now()->endOfMonth()])
            ->sum('amount');

        $paymentTrend = $lastMonthPayments > 0
            ? round((($thisMonthPayments - $lastMonthPayments) / $lastMonthPayments) * 100, 1)
            : 0;

        $totalRevenue = (float) Payment::where('status', PaymentStatus::PAID)->sum('amount');
        $pendingPayments = (float) Payment::where('status', PaymentStatus::PENDING)->sum('amount');
        $unpaidInvoices = Invoice::whereIn('status', [InvoiceStatus::SENT, InvoiceStatus::OVERDUE])->count();
        $confirmedParticipations = $currentEvent
            ? Participation::where('event_id', $currentEvent->id)
                ->where('participation_status', ParticipationStatus::CONFIRMED)
                ->count()
            : 0;

        return [
            Stat::make('Total Events', Event::count())
                ->description('All exhibitions')
                ->descriptionIcon('heroicon-o-calendar-days')
                ->color('primary')
                ->chart([3, 5, 7, 9, 11, 13, 15]),

            Stat::make('Companies', Company::count())
                ->description('Registered exhibitors')
                ->descriptionIcon('heroicon-o-building-office-2')
                ->color('info')
                ->chart([10, 15, 12, 18, 20, 25, 30]),

            Stat::make('Confirmed Booths', $confirmedParticipations)
                ->description("For {$currentYear} event")
                ->descriptionIcon('heroicon-o-check-badge')
                ->color('success')
                ->chart([5, 8, 12, 15, 20, 25, max(1, $confirmedParticipations)]),

            Stat::make('Total Revenue', '$' . number_format($totalRevenue, 0))
                ->description($paymentTrend >= 0 ? "{$paymentTrend}% increase" : abs($paymentTrend) . "% decrease")
                ->descriptionIcon($paymentTrend >= 0 ? 'heroicon-o-arrow-trending-up' : 'heroicon-o-arrow-trending-down')
                ->color($paymentTrend >= 0 ? 'success' : 'danger')
                ->chart([3000, 5000, 4500, 8000, 12000, 15000, max(1, (int) $thisMonthPayments)]),

            Stat::make('Pending Revenue', '$' . number_format($pendingPayments, 0))
                ->description("{$unpaidInvoices} unpaid invoices")
                ->descriptionIcon('heroicon-o-clock')
                ->color('warning'),

            Stat::make('Total Visitors', Visitor::count())
                ->description('Event attendees')
                ->descriptionIcon('heroicon-o-user-group')
                ->color('gray')
                ->chart([100, 150, 200, 180, 220, 250, 300]),
        ];
    }
}
