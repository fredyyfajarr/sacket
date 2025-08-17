<?php

namespace App\Filament\Widgets;

use App\Models\Event;
use App\Models\Order;
use App\Models\OrderItem;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        // Menghitung Total Pendapatan dari order yang sudah lunas
        $totalRevenue = Order::where('status', 'paid')->sum('total_price');

        // Menghitung jumlah semua tiket yang terjual dari order lunas
        $ticketsSold = OrderItem::whereHas('order', function ($query) {
            $query->where('status', 'paid');
        })->sum('quantity');

        // Menghitung jumlah event yang akan datang
        $activeEvents = Event::where('start_date', '>=', now())->count();

        return [
            Stat::make('Total Pendapatan', 'Rp ' . number_format($totalRevenue, 0, ',', '.'))
                ->description('Dari semua transaksi lunas')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
            Stat::make('Tiket Terjual', number_format($ticketsSold))
                ->description('Dari semua event')
                ->descriptionIcon('heroicon-m-ticket')
                ->color('info'),
            Stat::make('Event Aktif', $activeEvents)
                ->description('Event yang akan datang')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('warning'),
        ];
    }
}
