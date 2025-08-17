<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EventResource\Pages;
use App\Models\Event;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class EventResource extends Resource
{
    protected static ?string $model = Event::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days'; // Ganti ikon di sidebar

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true) // Aktifkan live update saat input tidak fokus
                    ->afterStateUpdated(fn (Forms\Set $set, ?string $state) => $set('slug', Str::slug($state))), // Otomatis isi slug

                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->maxLength(255)
                    ->readOnly(),

                Forms\Components\RichEditor::make('description') // Ganti textarea biasa menjadi rich editor
                    ->required()
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('location')
                    ->required()
                    ->maxLength(255),

                Forms\Components\DateTimePicker::make('start_date') // Ganti input teks menjadi date-time picker
                    ->required(),

                Forms\Components\DateTimePicker::make('end_date')
                    ->required(),

                Forms\Components\FileUpload::make('image') // Ganti input teks menjadi file upload
                    ->image() // Hanya izinkan gambar
                    ->directory('images') // Simpan di folder public/storage/images
                    ->imageEditor(), // Tambahkan editor gambar (crop, rotate, etc)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image'), // Tampilkan gambar sebagai gambar, bukan teks

                Tables\Columns\TextColumn::make('name')
                    ->searchable(), // Aktifkan pencarian untuk kolom ini

                Tables\Columns\TextColumn::make('location')
                    ->searchable(),

                Tables\Columns\TextColumn::make('start_date')
                    ->dateTime() // Format tanggal agar mudah dibaca
                    ->sortable(), // Aktifkan pengurutan untuk kolom ini

                Tables\Columns\TextColumn::make('end_date')
                    ->dateTime()
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
            'index' => Pages\ListEvents::route('/'),
            'create' => Pages\CreateEvent::route('/create'),
            'edit' => Pages\EditEvent::route('/{record}/edit'),
        ];
    }
}
