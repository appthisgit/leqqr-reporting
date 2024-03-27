<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReceiptResource\Pages;
use App\Filament\Resources\ReceiptResource\RelationManagers;
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

    protected static ?string $navigationGroup = 'Input';

    // public static function form(Form $form): Form
    // {
    //     return $form
    //         ->schema([
    //             Forms\Components\Select::make('company_id')
    //                 ->relationship('company', 'name')
    //                 ->required(),
    //             Forms\Components\Select::make('endpoint_id')
    //                 ->relationship('endpoint', 'name')
    //                 ->required(),
    //             Forms\Components\TextInput::make('order')
    //                 ->required(),
    //             Forms\Components\Toggle::make('printed')
    //                 ->required(),
    //         ]);
    // }

    public static function table(Table $table): Table
    {
        return $table
            ->poll('3s')
            ->defaultSort('id', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('order_id')
                    ->sortable(),
                Tables\Columns\TextColumn::make('confirmation_code')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('company.name')
                    ->numeric()
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
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->form([
                        Forms\Components\TextInput::make('result_response'),
                        Forms\Components\Textarea::make('order')
                            ->formatStateUsing(fn (array $state) => json_encode($state, JSON_PRETTY_PRINT))
                            ->rows(20),
                    ])
                // Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ])
            ->emptyStateActions([
                // Tables\Actions\CreateAction::make(),
            ]);
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
            'index' => Pages\ListReceipts::route('/'),
            // 'create' => Pages\CreateReceipt::route('/create'),
            // 'edit' => Pages\EditReceipt::route('/{record}/edit'),
        ];
    }
}
