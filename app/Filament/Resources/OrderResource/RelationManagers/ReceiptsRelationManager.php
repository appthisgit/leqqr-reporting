<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use App\Models\Receipt;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ReceiptsRelationManager extends RelationManager
{
    protected static string $relationship = 'receipts';

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
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('endpoint.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Completed' => 'success',
                        'Done' => 'gray',
                        'Parsing..' => 'warning',
                        'Prepared' => 'info',
                        default => 'danger',
                    }),
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
                Tables\Actions\Action::make('Open')
                    ->url(fn (Receipt $record): string => $record->response['result'])
                    ->openUrlInNewTab()
                    ->visible(fn(Receipt $record) => $record->status == 'Prepared')
                    ->color('info')
                    ->icon('heroicon-m-cpu-chip'),
                Tables\Actions\Action::make('view')
                    ->url(fn ($record): string => route('filament.admin.resources.receipts.view', ['record' => $record]))
                    ->icon('heroicon-s-eye')
                    ->iconButton(),
            ]);
    }
}
