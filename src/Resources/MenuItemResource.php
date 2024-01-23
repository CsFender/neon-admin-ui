<?php

namespace Neon\Admin\Resources;


use Closure;
use Filament\Forms;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\MorphToSelect;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Neon\Admin\Resources\Traits\NeonAdmin;
use Neon\Admin\Resources\MenuResource\Pages;
use Neon\Admin\Resources\SiteResource\RelationManagers;
use Neon\Attributable\Models\Attribute;
use Neon\Models\Menu;
use Neon\Models\Scopes\ActiveScope;
use Neon\Models\Statuses\BasicStatus;
use Neon\Site\Models\Scopes\SiteScope;
use Neon\Site\Models\Site;

class MenuItemResource extends Resource
{
  use NeonAdmin;

  protected static ?int $navigationSort = 3;

  protected static ?string $model = Menu::class;

  // protected static ?string $navigationIcon = 'heroicon-o-bars-3';

  // protected static ?string $activeNavigationIcon = 'heroicon-s-bars-3';

  protected static ?string $recordTitleAttribute = 'title';

  public static function getNavigationParentItem(): string
  {
    return __('neon-admin::admin.navigation.menu');
  }

  public static function getNavigationLabel(): string
  {
    return __('neon-admin::admin.navigation.menu_item');
  }

  public static function getNavigationGroup(): string
  {
    return __('neon-admin::admin.navigation.web');
  }

  public static function getModelLabel(): string
  {
    return __('neon-admin::admin.models.menu_item');
  }

  public static function getPluralModelLabel(): string
  {
    return __('neon-admin::admin.models.menu_item');
  }

  public static function items(): array
  {
    $t = [
      Fieldset::make(trans('neon-admin::admin.resources.menu.form.fieldset.name'))
        ->schema([
          TextInput::make('title')
            ->label(trans('neon-admin::admin.resources.menu.form.fields.title.label'))
            ->afterStateUpdated(function ($get, $set, ?string $state) {
              if (!$get('is_slug_changed_manually') && filled($state)) {
                $set('slug', Str::slug($state));
              }
            })
            ->reactive()
            ->required()
            ->maxLength(255),
          TextInput::make('slug')
            ->label(trans('neon-admin::admin.resources.menu.form.fields.slug.label'))
            ->afterStateUpdated(function (Closure $set) {
              $set('is_slug_changed_manually', true);
            })
            ->required(),
        ])
        ->columns(2),
      Select::make('status')
        ->label(trans('neon-admin::admin.resources.menu.form.fields.status.label'))
        ->required()
        ->reactive()
        ->default(BasicStatus::default())
        ->options(BasicStatus::class),
      Select::make('site')
        ->label(trans('neon-admin::admin.resources.menu.form.fields.site.label'))
        ->multiple()
        ->relationship(titleAttribute: 'title'),
    ];
    // if (in_array(\Neon\Attributable\Models\Traits\Attributable::class, class_uses_recursive(self::$model))) {
    //   return Tabs::make('Tabs')
    //     ->tabs([
    //       Tabs\Tab::make('Tab 1')
    //         ->schema($t)
    //         ->columns(1),
    //       Tabs\Tab::make('Tab 2')
    //         ->schema([
    //           // ...
    //         ]),
    //       Tabs\Tab::make('Tab 3')
    //         ->schema([
    //           // ...
    //         ]),
    //       ]);
    // } else {
    // }
    return $t;
  }

  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        Tables\Columns\TextColumn::make('title')
          ->label(__('neon-admin::admin.resources.menu.form.fields.title.label'))
          ->description(fn (Menu $record): string => "
          <x-neon-menu id=\"{$record->slug}\"> <x-slot:tools> ... </x-slot> </x-neon-menu>")
          ->searchable(),
        Tables\Columns\TextColumn::make('site.title')
          ->label(__('neon-admin::admin.resources.menu.form.fields.site.label'))
          ->listWithLineBreaks()
          ->bulleted()
          ->searchable(),
        Tables\Columns\IconColumn::make('status')
          ->label(__('neon-admin::admin.resources.menu.form.fields.status.label'))
          ->icon(fn (BasicStatus $state): string => match ($state) {
              BasicStatus::New      => 'heroicon-o-sparkles',
              BasicStatus::Active   => 'heroicon-o-check-circle',
              BasicStatus::Inactive => 'heroicon-o-x-circle',
          })
          ->color(fn (BasicStatus $state): string => match ($state) {
              BasicStatus::New      => 'gray',
              BasicStatus::Active   => 'success',
              BasicStatus::Inactive => 'danger',
          })
          ->searchable()
          ->sortable(),
      Tables\Columns\TextColumn::make('created_at')
          ->dateTime()
          ->sortable()
          ->toggleable(isToggledHiddenByDefault: true),
      Tables\Columns\TextColumn::make('updated_at')
          ->dateTime()
          ->sortable()
          ->toggleable(isToggledHiddenByDefault: true),
      Tables\Columns\TextColumn::make('deleted_at')
          ->dateTime()
          ->sortable()
          ->toggleable(isToggledHiddenByDefault: true),
      ])
      ->filters([
        Tables\Filters\TrashedFilter::make(),
      ])
      ->actions([
        Tables\Actions\EditAction::make()
          ->slideOver(),
        Tables\Actions\DeleteAction::make(),
        Tables\Actions\ForceDeleteAction::make(),
        Tables\Actions\RestoreAction::make(),
      ])
      ->bulkActions(self::bulkActions());
  }

  public static function getPages(): array
  {
    return [
      'index' => Pages\ManageMenus::route('/'),
    ];
  }

  public static function getEloquentQuery(): Builder
  {
      return parent::getEloquentQuery()
          ->withoutGlobalScopes([
              ActiveScope::class,
              SiteScope::class,
              SoftDeletingScope::class
          ]);
  }
}
