<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Custom\Actions\CreateReceiptAction;
use App\Filament\Resources\OrderResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('company')
                ->name($this->record->company->name)
                ->url(fn ($record): string => route('filament.admin.resources.companies.view', ['record' => $record->company_id]))
                ->color('gray')
                ->icon('heroicon-o-building-storefront'),
            CreateReceiptAction::make()
                ->setOrderId($this->record->id),
        ];
    }
}
