<?php

namespace App\Filament\Resources\ReceiptResource\Pages;

use App\Filament\Custom\Actions\CreateReceiptAction;
use App\Filament\Resources\ReceiptResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions;

class ViewReceipt extends ViewRecord
{
    protected static string $resource = ReceiptResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('company')
                ->name($this->record->order->company->name)
                ->url(fn ($record): string => route('filament.admin.resources.companies.view', ['record' => $record->order->company_id]))
                ->color('gray')
                ->icon('heroicon-o-building-storefront'),
            Actions\Action::make('order')
                ->name($this->record->order_id)
                ->url(fn ($record): string => route('filament.admin.resources.orders.view', ['record' => $record->order_id]))
                ->color('gray')
                ->icon('heroicon-o-shopping-cart'),
            Actions\Action::make('endpoint')
                ->name($this->record->endpoint->name)
                ->url(fn ($record): string => route('filament.admin.resources.endpoints.edit', ['record' => $record->endpoint_id]))
                ->color('gray')
                ->icon('heroicon-o-cpu-chip'),
            CreateReceiptAction::make()
                ->visible(fn () => $this->record->status != 'Prepared')
                ->setOrderId($this->record->order_id)
                ->redirectToModel(),
            Actions\Action::make('Open')
                ->url(fn (): string => $this->record->response['result'])
                ->openUrlInNewTab()
                ->visible(fn () => $this->record->status == 'Prepared')
                ->color('info')
                ->icon('heroicon-m-cpu-chip'),
        ];
    }
}
