<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Mail; // Import Mail
use App\Mail\TicketPurchased; // Import Mailable
use Filament\Notifications\Notification; // Import Notification

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationGroup = 'Manajemen Penjualan';

    // Nonaktifkan Create Manual (Order hanya dari frontend)
    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('order_number')->readOnly(),
                Forms\Components\TextInput::make('customer_name')->readOnly(),
                Forms\Components\TextInput::make('customer_email')->readOnly(),
                Forms\Components\TextInput::make('total_price')
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                    ->readOnly(),
                Forms\Components\TextInput::make('status')->readOnly(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('customer_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('event.name')
                    ->limit(30),
                Tables\Columns\TextColumn::make('total_price')
                    ->money('IDR')
                    ->sortable(),

                // Badge Status Warna-warni
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'paid' => 'success',
                        'canceled', 'expired' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                // Lihat Detail Order
                Tables\Actions\ViewAction::make(),

                // ACTION BARU: Kirim Ulang Email
                Tables\Actions\Action::make('resend_email')
                    ->label('Kirim Email')
                    ->icon('heroicon-o-envelope')
                    ->color('info')
                    ->visible(fn (Order $record) => $record->status === 'paid') // Hanya jika sudah lunas
                    ->requiresConfirmation()
                    ->modalHeading('Kirim Ulang E-Tiket')
                    ->modalDescription(fn (Order $record) => "Kirim ulang tiket PDF ke email {$record->customer_email}?")
                    ->action(function (Order $record) {
                        try {
                            // Kirim Email dengan Attachment PDF
                            Mail::to($record->customer_email)->send(new TicketPurchased($record));

                            Notification::make()
                                ->title('Email berhasil dikirim ulang')
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Gagal mengirim email')
                                ->body('Error: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            // Halaman edit/create tidak dipakai
        ];
    }
}
