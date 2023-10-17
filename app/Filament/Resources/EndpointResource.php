<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EndpointResource\Pages;
use App\Models\Endpoint;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EndpointResource extends Resource
{
    protected static ?string $model = Endpoint::class;

    protected static ?string $navigationIcon = 'heroicon-o-cpu-chip';

    protected static ?string $navigationGroup = 'Output';

    public static function form(Form $form): Form
    {
        return $form
            ->columns(3)
            ->schema([
                Section::make('Instellingen')
                    ->columnSpan(2)
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Naam')
                            ->helperText('Eigen bedachte naam')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('company_id')
                            ->helperText('Bij welke company ID behoort dit endpoint')
                            ->numeric()
                            ->required(),
                        Forms\Components\Select::make('template_id')
                            ->relationship('template', 'name')
                            ->required(),
                        Forms\Components\Select::make('type')
                            ->options([
                                'sunmi' => 'Sunmi Cloudprinter',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('target')
                            ->helperText('Het apparaat of email adres waarheen het resultaat wordt gestuurd')
                            ->required()
                            ->maxLength(100),
                    ]),
                Section::make('Filters')
                    ->columnSpan(1)
                    ->schema([
                        Forms\Components\TextInput::make('filter_terminal')
                            ->label('Terminal')
                            ->helperText('Filteren op betaal terminal')
                            ->maxLength(40),
                        Forms\Components\TextInput::make('filter_zone')
                            ->label('Zone')
                            ->helperText('Filteren op deze zone')
                            ->maxLength(40),
                        Forms\Components\Toggle::make('filter_printable')
                            ->label('Printable')
                            ->helperText('Filteren op "printable" producten'),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Naam')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('company_id')
                    ->numeric()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('template.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('target')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('filter_terminal')
                    ->label('Terminal')
                    ->placeholder('Leeg')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('filter_zone')
                ->label('Zone')
                    ->placeholder('Leeg')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('filter_printable')
                ->label('Printable')
                    ->boolean()
                    ->searchable()
            ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Aangemaakt')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Geüpdate')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\ReplicateAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
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
            'index' => Pages\ListEndpoints::route('/'),
            'create' => Pages\CreateEndpoint::route('/create'),
            'edit' => Pages\EditEndpoint::route('/{record}/edit'),
        ];
    }
}
