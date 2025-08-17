<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class TicketSalesChart extends ChartWidget
{
    protected static ?string $heading = 'Penjualan Tiket (7 Hari Terakhir)';
    protected static string $color = 'info';
    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $data = Order::select('created_at')
            ->where('status', 'paid')
            ->whereBetween('created_at', [
                Carbon::now()->subDays(6)->startOfDay(),
                Carbon::now()->endOfDay(),
            ])
            ->get()
            ->groupBy(function ($date) {
                return Carbon::parse($date->created_at)->format('d M'); // Grup berdasarkan hari
            });

        $labels = [];
        $values = [];

        // Loop 7 hari ke belakang untuk memastikan semua hari ada di label
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dateString = $date->format('d M');
            $labels[] = $dateString;
            $values[] = $data->has($dateString) ? $data[$dateString]->count() : 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Order Lunas',
                    'data' => $values,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line'; // Tipe grafik
    }
}
