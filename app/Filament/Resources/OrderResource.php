<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationGroup = 'Manajemen Penjualan';

    // Kita nonaktifkan halaman 'Create' karena order dibuat oleh user
    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Form ini hanya untuk tampilan detail, jadi kita buat read-only
                Forms\Components\TextInput::make('order_number')->readOnly(),
                Forms\Components\TextInput::make('customer_name')->readOnly(),
                Forms\Components\TextInput::make('customer_email')->readOnly(),
                Forms\Components\TextInput::make('total_price')->readOnly(),
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
                Tables\Columns\TextColumn::make('event.name'),
                Tables\Columns\TextColumn::make('total_price')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge() // Tampilkan status sebagai badge berwarna
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'paid' => 'success',
                        'canceled', 'expired' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc') // Urutkan dari yang terbaru
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(), // Ganti EditAction dengan ViewAction
            ])
            ->bulkActions([]); // Nonaktifkan bulk actions
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            // Halaman create dan edit tidak kita perlukan
        ];
    }
}
