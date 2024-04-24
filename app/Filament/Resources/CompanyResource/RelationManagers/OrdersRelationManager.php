<?php

namespace App\Filament\Resources\CompanyResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class OrdersRelationManager extends RelationManager
{
    protected static string $relationship = 'orders';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->poll('3s')
            ->defaultSort('id', 'desc')
            ->paginated([50, 100, 150, 200, 'all'])
            ->defaultPaginationPageOption(50)
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->numeric()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('confirmation_code')
                    ->numeric()
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->url(fn ($record): string => route('filament.admin.resources.orders.view', ['record' => $record]))
                    ->icon('heroicon-s-eye')
                    ->iconButton(),

            ]);
    }
}
