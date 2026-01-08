<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Filament\Resources\TransactionResource\RelationManagers;
use App\Models\Transaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form->schema([Forms\Components\TextInput::make('name')->required()->maxLength(255), Forms\Components\Select::make('category_id')->relationship('category', 'name')->required(), Forms\Components\DatePicker::make('date_transaction')->required(), Forms\Components\TextInput::make('amount')->required()->numeric(), Forms\Components\TextInput::make('note')->required()->maxLength(255), Forms\Components\FileUpload::make('image')->image()->required()]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn(Builder $query) => $query->with('category')) // ✅ FIX N+1
->columns([
    Tables\Columns\ImageColumn::make('category.image')
        ->label('Kategori')
        ->disk('public')
        ->height(32)
        ->width(32),

    Tables\Columns\TextColumn::make('category.name')
        ->description(fn (Transaction $record): string => $record->name)
        ->label('Transaksi')
        ->searchable()
        ->sortable(),

    Tables\Columns\IconColumn::make('category.is_expense')
        ->label('Tipe')
        ->boolean()
        ->trueIcon('heroicon-s-arrow-up-circle')
        ->falseIcon('heroicon-s-arrow-down-circle')
        ->trueColor('danger')
        ->falseColor('success')
        ->sortable(),

    Tables\Columns\TextColumn::make('date_transaction')
        ->label('Tanggal')
        ->date('Y-m-d')
        ->sortable(),

    Tables\Columns\TextColumn::make('amount')
        ->label('Jumlah')
        ->money('IDR', locale: 'id')
        ->sortable(),

    // ✅ Tambahkan ini agar ikon Columns muncul lagi
    Tables\Columns\TextColumn::make('created_at')
        ->label('Created')
        ->dateTime()
        ->sortable()
        ->toggleable(isToggledHiddenByDefault: true),

    Tables\Columns\TextColumn::make('updated_at')
        ->label('Updated')
        ->dateTime()
        ->sortable()
        ->toggleable(isToggledHiddenByDefault: true),

    Tables\Columns\TextColumn::make('deleted_at')
        ->label('Deleted')
        ->dateTime()
        ->sortable()
        ->toggleable(isToggledHiddenByDefault: true),
])

            ->paginated([10, 25, 50]) // ✅ biar gak kebanyakan row
            ->defaultPaginationPageOption(10) // ✅ default ringan
            ->actions([Tables\Actions\EditAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getRelations(): array
    {
        return [
                //
            ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransactions::route('/'),
            'create' => Pages\CreateTransaction::route('/create'),
            'edit' => Pages\EditTransaction::route('/{record}/edit'),
        ];
    }
}
