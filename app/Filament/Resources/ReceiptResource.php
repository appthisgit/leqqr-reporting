<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReceiptResource\Pages;
use App\Filament\Tables\Components\Actions\ViewReceiptAction;
use App\Models\Receipt;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ReceiptResource extends Resource
{
    protected static ?string $model = Receipt::class;

    protected static ?string $navigationIcon = 'heroicon-o-document';

    protected static ?string $navigationGroup = 'IO';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Forms\Components\TextInput::make('id')
                //     ->required()
                //     ->numeric(),
                Forms\Components\Fieldset::make('Order')
                    ->relationship('order')
                    ->schema([
                        Forms\Components\TextInput::make('id'),
                        Forms\Components\TextInput::make('confirmation_code'),
                    ]),
                Forms\Components\Select::make('endpoint_id')
                    ->relationship(name: 'endpoint', titleAttribute: 'name'),
                Forms\Components\DateTimePicker::make('created_at'),
                Forms\Components\DateTimePicker::make('updated_at'),
                Forms\Components\TextInput::make('result_message'),
                Forms\Components\TextInput::make('result_response'),
                Forms\Components\TextInput::make('printed')->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->poll('3s')
            ->defaultSort('id', 'desc')
            ->paginated([50, 100, 150, 200, 'all'])
            ->defaultPaginationPageOption(50)
            ->columns([
                Tables\Columns\TextColumn::make('order.id')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('order.confirmation_code')
                    ->numeric()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('endpoint.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('printed')
                    ->numeric(),
                Tables\Columns\TextColumn::make('result_message')
                    ->badge(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                ViewReceiptAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReceipts::route('/'),
            'view' => Pages\ViewReceipt::route('/{record}'),
        ];
    }
}
