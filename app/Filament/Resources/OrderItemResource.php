<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderItemResource\Pages;
use App\Models\OrderItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class OrderItemResource extends Resource
{
    protected static ?string $model = OrderItem::class;

    // Ganti Label Menu jadi "Tiket Peserta"
    protected static ?string $navigationLabel = 'Tiket Peserta';
    protected static ?string $pluralModelLabel = 'Tiket Peserta';
    protected static ?string $navigationIcon = 'heroicon-o-ticket';
    protected static ?string $navigationGroup = 'Manajemen Event';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Form hanya Read-Only karena data tiket digenerate otomatis
                Forms\Components\TextInput::make('unique_code')
                    ->label('Kode Unik')
                    ->readOnly(),

                Forms\Components\TextInput::make('order.customer_name')
                    ->label('Nama Pemilik')
                    ->readOnly(),

                Forms\Components\TextInput::make('ticketCategory.name')
                    ->label('Kategori')
                    ->readOnly(),

                Forms\Components\DateTimePicker::make('checked_in_at')
                    ->label('Waktu Check-in')
                    ->readOnly(),
            ]);
    }

   public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // 1. Kode Unik
                Tables\Columns\TextColumn::make('unique_code')
                    ->label('Kode Tiket')
                    ->searchable()
                    ->copyable()
                    ->weight('bold'),

                // 2. Nama Event
                Tables\Columns\TextColumn::make('order.event.name')
                    ->label('Event')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('ticketCategory.name')
                    ->label('Kategori')
                    ->badge()
                    ->color('info'),

                // 3. Nama Pemilik
                Tables\Columns\TextColumn::make('order.customer_name')
                    ->label('Pemilik')
                    ->searchable(),

                // 4. Status Check-in (PERBAIKAN DI SINI)
                // Kita beri nama 'status_check' agar tidak bentrok, tapi datanya ambil dari 'checked_in_at'
                Tables\Columns\TextColumn::make('status_check')
                    ->label('Status')
                    ->state(fn ($record) => $record->checked_in_at) // <-- Ambil data dari sini
                    ->formatStateUsing(fn ($state) => $state ? 'Sudah Masuk' : 'Belum Masuk')
                    ->badge()
                    ->color(fn ($state) => $state ? 'success' : 'gray')
                    ->icon(fn ($state) => $state ? 'heroicon-o-check-circle' : 'heroicon-o-clock'),

                // 5. Waktu Scan (Tetap gunakan nama asli kolom database)
                Tables\Columns\TextColumn::make('checked_in_at')
                    ->label('Waktu Scan')
                    ->dateTime('d M Y, H:i')
                    ->placeholder('-')
                    ->sortable(),
            ])
            // ... filters dan actions tetap sama ...
            ->filters([
                Tables\Filters\Filter::make('checked_in')
                    ->label('Sudah Check-in')
                    ->query(fn (Builder $query) => $query->whereNotNull('checked_in_at')),
                Tables\Filters\Filter::make('not_checked_in')
                    ->label('Belum Masuk')
                    ->query(fn (Builder $query) => $query->whereNull('checked_in_at')),
            ])
            ->actions([])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            // 'index' => Pages\ListOrderItems::route('/'),
            'index' => Pages\ManageOrderItems::route('/'),
        ];
    }

    // Agar tidak bisa create manual dari menu ini
    public static function canCreate(): bool
    {
        return false;
    }
}
