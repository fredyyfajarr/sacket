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
use Filament\Notifications\Notification; // Import Notification

class OrderItemResource extends Resource
{
    protected static ?string $model = OrderItem::class;

    // Label & Ikon Menu
    protected static ?string $navigationLabel = 'Tiket Peserta';
    protected static ?string $pluralModelLabel = 'Tiket Peserta';
    protected static ?string $navigationIcon = 'heroicon-o-ticket';
    protected static ?string $navigationGroup = 'Manajemen Event';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Form Read-Only untuk melihat detail tiket
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

                // 3. Kategori
                Tables\Columns\TextColumn::make('ticketCategory.name')
                    ->label('Kategori')
                    ->badge()
                    ->color('info'),

                // 4. Nama Pemilik
                Tables\Columns\TextColumn::make('order.customer_name')
                    ->label('Pemilik')
                    ->searchable(),

                // 5. Status Check-in (Logika sudah diperbaiki)
                Tables\Columns\TextColumn::make('status_check')
                    ->label('Status')
                    ->state(fn ($record) => $record->checked_in_at) // Ambil data dari checked_in_at
                    ->formatStateUsing(fn ($state) => $state ? 'Sudah Masuk' : 'Belum Masuk')
                    ->badge()
                    ->color(fn ($state) => $state ? 'success' : 'gray')
                    ->icon(fn ($state) => $state ? 'heroicon-o-check-circle' : 'heroicon-o-clock'),

                // 6. Waktu Scan Real
                Tables\Columns\TextColumn::make('checked_in_at')
                    ->label('Waktu Scan')
                    ->dateTime('d M Y, H:i')
                    ->placeholder('-')
                    ->sortable(),
            ])
            ->filters([
                // Filter Status Kehadiran
                Tables\Filters\Filter::make('checked_in')
                    ->label('Sudah Check-in')
                    ->query(fn (Builder $query) => $query->whereNotNull('checked_in_at')),

                Tables\Filters\Filter::make('not_checked_in')
                    ->label('Belum Masuk')
                    ->query(fn (Builder $query) => $query->whereNull('checked_in_at')),
            ])
            ->actions([
                // ACTION BARU: Manual Check-in / Batal Masuk
                Tables\Actions\Action::make('toggle_checkin')
                    ->label(fn ($record) => $record->checked_in_at ? 'Batal' : 'Check-in')
                    ->icon(fn ($record) => $record->checked_in_at ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                    ->color(fn ($record) => $record->checked_in_at ? 'danger' : 'success')
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Check-in Manual')
                    ->modalDescription('Ubah status kehadiran tiket ini secara manual?')
                    ->action(function (OrderItem $record) {
                        if ($record->checked_in_at) {
                            // Jika sudah masuk -> batalkan (set null)
                            $record->update(['checked_in_at' => null]);
                            Notification::make()->title('Status check-in dibatalkan')->warning()->send();
                        } else {
                            // Jika belum masuk -> set waktu sekarang
                            $record->update(['checked_in_at' => now()]);
                            Notification::make()->title('Berhasil check-in manual')->success()->send();
                        }
                    }),
            ])
            ->bulkActions([
                // Kosongkan bulk actions agar aman
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageOrderItems::route('/'),
        ];
    }

    // Nonaktifkan tombol "New Ticket" manual
    public static function canCreate(): bool
    {
        return false;
    }
}
