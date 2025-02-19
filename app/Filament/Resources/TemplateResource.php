<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TemplateResource\Pages;
use App\Filament\Resources\TemplateResource\RelationManagers\EndpointsRelationManager;
use App\Models\Template;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TemplateResource extends Resource
{
    protected static ?string $model = Template::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationGroup = 'Settings';

    public static function getGloballySearchableAttributes(): array
    {
        return ['name'];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->columns(3)
            ->schema([
                Forms\Components\Section::make('Instellingen')
                    ->columnSpan(2)
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Naam')
                            ->helperText('naam om deze template te identificeren')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\MarkdownEditor::make('content')
                            ->toolbarButtons([
                                'undo',
                                'redo',
                            ])
                            ->label('Template')
                            ->helperText('Voer hier uw XML template in')
                            ->required(),
                    ]),
                Forms\Components\Section::make('Extra\'s')
                    ->columnSpan(1)
                    ->schema([
                        Forms\Components\Repeater::make('translations')
                            ->label('Vertalingen')
                            ->helperText('Gebruikt teksten in de template met hier ingevulde vertalingen. De volgorde maakt niet uit zolang het <text> element maar \'translate="true"\' bevat in de template en een overeenkomstige Nederlandse naam.')
                            ->addActionLabel('Vertaling toevoegen')
                            ->collapsed()
                            ->columns(3)
                            ->itemLabel(fn(array $state): ?string => $state['key'] ?? null)
                            ->schema([
                                Forms\Components\TextInput::make('key')
                                    ->label('Nederlands')
                                    ->live()
                                    ->columnSpan(3)
                                    ->required(),
                                Forms\Components\TextInput::make('de')
                                    ->label('Duits'),
                                Forms\Components\TextInput::make('fr')
                                    ->label('Frans'),
                                Forms\Components\TextInput::make('en')
                                    ->label('Engels'),
                            ]),
                        Forms\Components\FileUpload::make('images')
                            ->label('Afbeeldingen')
                            ->helperText('Houdt rekening met de volgorde van uw afbeeldingen')
                            ->disk('public')
                            ->directory('uploads/')
                            ->visibility('public')
                            ->image()
                            ->imageEditor()
                            ->multiple()
                            ->reorderable()
                            ->appendFiles(),
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
                Tables\Columns\TextColumn::make('endpoints_count')
                    ->label('Endpoints')
                    ->counts('endpoints')
                    ->alignEnd(),
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
            EndpointsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTemplates::route('/'),
            'create' => Pages\CreateTemplate::route('/create'),
            'edit' => Pages\EditTemplate::route('/{record}/edit'),
        ];
    }
}
