<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TicketCategoryResource\Pages;
use App\Models\TicketCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TicketCategoryResource extends Resource
{
    protected static ?string $model = TicketCategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket'; // Ikon tiket
    protected static ?string $navigationGroup = 'Manajemen Event'; // Grup menu di sidebar

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Pilihan untuk memilih Event
                Forms\Components\Select::make('event_id')
                    ->relationship('event', 'name') // Ambil relasi 'event' dan tampilkan kolom 'name'
                    ->required()
                    ->searchable()
                    ->preload(),

                // Input untuk Nama Kategori
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Contoh: Presale 1, VIP, Reguler'),

                // Input untuk Harga
                Forms\Components\TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('Rp'),

                // Input untuk Stok
                Forms\Components\TextInput::make('stock')
                    ->required()
                    ->numeric(),

                // Pilihan Tanggal & Waktu
                Forms\Components\DateTimePicker::make('sale_start_date'),
                Forms\Components\DateTimePicker::make('sale_end_date'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Tampilkan nama Event
                Tables\Columns\TextColumn::make('event.name')
                    ->searchable()
                    ->sortable(),

                // Tampilkan nama Kategori Tiket
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),

                // Tampilkan Harga
                Tables\Columns\TextColumn::make('price')
                    ->money('IDR') // Format sebagai mata uang Rupiah
                    ->sortable(),

                // Tampilkan Stok
                Tables\Columns\TextColumn::make('stock')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTicketCategories::route('/'),
            'create' => Pages\CreateTicketCategory::route('/create'),
            'edit' => Pages\EditTicketCategory::route('/{record}/edit'),
        ];
    }
}
