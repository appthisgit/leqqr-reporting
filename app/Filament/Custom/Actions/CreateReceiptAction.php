<?php

namespace App\Filament\Custom\Actions;

use App\Models\Receipt;
use App\Parsing\ReceiptProcessor;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Form;
use Filament\Actions\CreateAction;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms;
use Closure;
use Filament\Notifications\Notification;

class CreateReceiptAction extends CreateAction
{
    protected bool | Closure $canCreateAnother = false;
    private int $orderId = 0;
    private bool $redirect = false;

    public function setOrderId(int | Closure $id)
    {
        $this->orderId = (int)$this->evaluate($id);
        return $this;
    }

    public function redirectToModel()
    {
        $this->redirect = true;
        return $this;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Print');
        $this->icon('heroicon-o-printer');
        $this->model(Receipt::class);
        $this->form([
            Forms\Components\Hidden::make('order_id')
                ->default($this->orderId),
            Forms\Components\Select::make('endpoint_id')
                ->relationship(name: 'endpoint', titleAttribute: 'name')
        ]);

        $this->action(function (array $arguments, Form $form) {
            $model = $this->getModel();

            $record = $this->process(function (array $data, HasActions $livewire) use ($model): Model {
                if ($translatableContentDriver = $livewire->makeFilamentTranslatableContentDriver()) {
                    $record = $translatableContentDriver->makeRecord($model, $data);
                } else {
                    $record = new $model();
                    $record->fill($data);
                }

                if ($relationship = $this->getRelationship()) {
                    $relationship->save($record);

                    return $record;
                }

                $record->order_id = $this->orderId;
                $record->save();

                return $record;
            });

            $this->record($record);
            $form->model($record)->saveRelationships();

            $controller = new ReceiptProcessor($record);
            if ($record->endpoint->type == 'sunmi') {
                $controller->parse();
                if ($this->redirect) {
                    $this->successRedirectUrl = fn (Model $record): string =>  route('filament.admin.resources.receipts.view', [
                        'record' => $record->id,
                    ]);
                }
                $this->success();
            } else {
                $controller->prepare();

                if ($this->redirect) {
                    $this->successRedirectUrl = fn (Model $record): string => route('filament.admin.resources.receipts.view', [
                        'record' => $record->id,
                    ]);
                    $this->success();
                } else {
                    Notification::make()
                        ->info()
                        ->title('Voorbereid')
                        ->send();
                }
            }
        });
    }
}
