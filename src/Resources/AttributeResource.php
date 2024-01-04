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
    return trans('neon-admin::admin.resources.variables');
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
          ->label('Erőforrás')
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
            // dd(in_array('Neon\Attributable\Models\Traits\Attributable', class_uses_recursive('\Neon\Site\Models\Site')), $array);
            // foreach (get_declared_classes() as $class)
            // {
            //   // if (in_array('Neon\Attributable\Models\Traits\Attributable', class_uses_recursive($class)))
            //   // {
            //   //   $array[] = $class;
            //   // }
            //   // $array = array_merge($array, class_uses_recursive($class));


            //   // if(strpos($class, $namespace) === 0){
            //   //     $c = substr($class, strlen($namespace));
            //   //     echo 'class ' . $c . ' exists in namespace '. $namespace . '<br/>'; 
            //   // }
            // }
            return $array;
          })
          ->columns(2),
        Fieldset::make('Name')
          ->schema([
            TextInput::make('name')
              ->afterStateUpdated(function ($get, $set, ?string $state) {
                  if (!$get('is_slug_changed_manually') && filled($state)) {
                      $set('slug', Str::slug($state));
                  }
              })
              ->reactive()
              ->required()
              ->maxLength(255),
            TextInput::make('slug')
              ->afterStateUpdated(function (Closure $set) {
                  $set('is_slug_changed_manually', true);
              })
              ->required()
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
        Select::make('cast_as')
          ->label('Adattípus')
          ->searchable()
          ->options([
            'string'  => 'Szöveg',
            'integer' => 'Egész szám',
            'float'   => 'Tizedes tört (Lebegőpontos szám)',
            'boolean' => 'Logikai (igaz/hamis) érték',
            'array'   => 'Tömb',
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
