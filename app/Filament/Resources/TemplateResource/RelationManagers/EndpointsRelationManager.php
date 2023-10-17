<?php

namespace App\Filament\Resources\TemplateResource\RelationManagers;

use App\Filament\Resources\EndpointResource;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class EndpointsRelationManager extends RelationManager
{
    protected static string $relationship = 'endpoints';

    public function form(Form $form): Form
    {
        return EndpointResource::form($form);
    }

    public function table(Table $table): Table
    {
        return EndpointResource::table($table);
    }
}
