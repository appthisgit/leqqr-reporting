<?php

namespace App\Filament\Tables\Components\Actions;

use App\Models\Receipt;
use App\Parsing\ReceiptProcessor;
use Filament\Tables;
use Filament\Forms;

class ViewReceiptAction extends Tables\Actions\ViewAction
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->form([
            Forms\Components\TextInput::make('result_message', 'Parse message'),
            Forms\Components\Textarea::make('result_response', 'Parse response')
                ->formatStateUsing(fn ($state) => json_encode(json_decode($state), JSON_PRETTY_PRINT))
                ->rows(6),
            Forms\Components\Textarea::make('order')
                ->formatStateUsing(fn (array $state) => json_encode($state, JSON_PRETTY_PRINT))
                ->rows(15),
        ]);

        $this->extraModalFooterActions([
            Tables\Actions\ReplicateAction::make()
                ->requiresConfirmation()
                ->label('Reprint')
                ->excludeAttributes(['printed', 'result_message', 'result_response'])
                ->mutateRecordDataUsing(function (array $data): array {
                    $data['user_id'] = auth()->id();

                    return $data;
                })
                ->after(function (Receipt $replica): void {
                    $controller = new ReceiptProcessor($replica);
                    $controller->parse();
                })
                ->cancelParentActions(),
        ]);
    }
}
