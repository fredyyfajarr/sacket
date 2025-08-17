<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PromoCodeResource\Pages;
use App\Models\PromoCode;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PromoCodeResource extends Resource
{
    protected static ?string $model = PromoCode::class;

    protected static ?string $navigationIcon = 'heroicon-o-receipt-percent';
    protected static ?string $navigationGroup = 'Manajemen Penjualan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('code')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                Forms\Components\Select::make('type')
                    ->options([
                        'fixed' => 'Potongan Harga Tetap (Rp)',
                        'percentage' => 'Potongan Persentase (%)',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('value')
                    ->required()
                    ->numeric()
                    ->helperText('Isi dengan nominal (misal: 50000) atau persentase (misal: 20)'),
                Forms\Components\TextInput::make('max_uses')
                    ->numeric()
                    ->helperText('Kosongkan jika tidak ada batas penggunaan'),
                Forms\Components\DateTimePicker::make('expires_at')
                    ->helperText('Kosongkan jika tidak ada batas waktu'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'fixed' => 'info',
                        'percentage' => 'success',
                    }),
                Tables\Columns\TextColumn::make('value')
                    ->money(fn (PromoCode $record) => $record->type === 'fixed' ? 'IDR' : null)
                    ->suffix(fn (PromoCode $record) => $record->type === 'percentage' ? '%' : ''),
                Tables\Columns\TextColumn::make('uses')
                    ->label('Digunakan')
                    ->formatStateUsing(fn (PromoCode $record) => "{$record->uses} / " . ($record->max_uses ?? '∞')),
                Tables\Columns\TextColumn::make('expires_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListPromoCodes::route('/'),
            'create' => Pages\CreatePromoCode::route('/create'),
            'edit' => Pages\EditPromoCode::route('/{record}/edit'),
        ];
    }
}
