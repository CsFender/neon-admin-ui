<?php

namespace Neon\Admin\Resources;

use Closure;
use Filament\Forms;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;
use Neon\Admin\Resources\AttributeResource\Pages;
use Neon\Admin\Resources\AttributeResource\RelationManagers;
use Neon\Attributable\Models\Attribute;

class AttributeResource extends Resource
{
  protected static ?string $model = Attribute::class;
  
  protected static ?string $navigationIcon = 'heroicon-o-code-bracket-square';
  
  protected static ?string $activeNavigationIcon = 'heroicon-s-code-bracket-square';

  public static function getNavigationLabel(): string
  {
    return trans('neon-admin::admin.resources.attributables.title');
  }

  public static function getNavigationGroup(): string
  {
    return trans('neon-admin::admin.navigation.settings');
  }

  public static function getModelLabel(): string
  {
    return trans('neon-admin::admin.models.attribute');
  }

  public static function getPluralModelLabel(): string
  {
    return trans('neon-admin::admin.models.attributes');
  }

  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        Select::make('class')
          ->label(__('neon-admin::admin.resources.attributables.form.fields.class.label'))
          ->options(function () {
            $array = [];
            $resources = app('filament')->getResources();
            foreach ($resources as $resource)
            {
              /** Create the resource, to get the related model.
               */
              $_r = new $resource();

              /** If getting  */
              if (in_array('Neon\Attributable\Models\Traits\Attributable', class_uses_recursive($_r->getModel())))
              {
                $array[$_r->getModel()] = $_r::getNavigationLabel();
              }
            }
            return $array;
          })
          ->columns(2),
        Fieldset::make(__('neon-admin::admin.resources.attributables.form.fieldset.name'))
          ->schema([
            TextInput::make('name')
              ->label(__('neon-admin::admin.resources.attributables.form.fields.name.label'))
              ->afterStateUpdated(function ($get, $set, ?string $state) {
                  if (!$get('is_slug_changed_manually') && filled($state)) {
                      $set('slug', Str::slug($state));
                  }
              })
              ->reactive()
              ->required()
              ->maxLength(255),
            TextInput::make('slug')
              ->label(__('neon-admin::admin.resources.attributables.form.fields.slug.label'))
              ->afterStateUpdated(function (Closure $set) {
                  $set('is_slug_changed_manually', true);
              })
              ->required()
          ])
          ->columns(2),
        Select::make('cast_as')
          ->label(__('neon-admin::admin.resources.attributables.form.fields.cast_as.label'))
          ->searchable()
          ->options([
            'string'  => __('neon-admin::admin.resources.attributables.form.fields.cast_as.options.string'),
            'integer' => __('neon-admin::admin.resources.attributables.form.fields.cast_as.options.integer'),
            'float'   => __('neon-admin::admin.resources.attributables.form.fields.cast_as.options.float'),
            'boolean' => __('neon-admin::admin.resources.attributables.form.fields.cast_as.options.boolean'),
            'array'   => __('neon-admin::admin.resources.attributables.form.fields.cast_as.options.array'),
          ])
          ->columns(2),
        Select::make('field')
          ->label('Beviteli mező')
          ->options([
            'text' => 'Szöveges beviteli mező'
          ]),
        Select::make('rules')
          ->label('Ellenőrzési szabályok')
          ->multiple()
          ->searchable()
          ->options([
            'activeUrl' => 'URL',
            'alpha'     => 'Csak betűk',
            'alphaDash' => 'Csak betűk, számok és kötőjel és aláhúzásjel',
            'alphaNum'  => 'Csak betűk és számok',
            'required'  => 'Kötelező kitölteni',
            'ascii'     => 'Csak ASCII karakterek'
          ]),
        KeyValue::make('params')
          ->label('Paraméterek')
      ]);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        //
      ])
      ->filters([
        //
      ])
      ->actions([
        Tables\Actions\EditAction::make(),
        Tables\Actions\DeleteAction::make(),
      ])
      ->bulkActions([
        Tables\Actions\BulkActionGroup::make([
          Tables\Actions\DeleteBulkAction::make(),
        ]),
      ]);
  }

  public static function getPages(): array
  {
    return [
      'index' => Pages\ManageAttributes::route('/'),
    ];
  }
}
