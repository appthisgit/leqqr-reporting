<?php

namespace App\Filament\Resources\CompanyResource\RelationManagers;

use App\Filament\Tables\Components\Actions\ViewReceiptAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ReceiptsRelationManager extends RelationManager
{
    protected static string $relationship = 'receipts';

    // So the Reprint action can be called
    public function isReadOnly(): bool
    {
        return false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->poll('3s')
            ->defaultSort('id', 'desc')
            ->recordTitleAttribute('confirmation_code')
            ->paginated([50, 100, 150, 200, 'all'])
            ->defaultPaginationPageOption(50)
            ->columns([
                Tables\Columns\TextColumn::make('order_id')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('confirmation_code')
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
}
