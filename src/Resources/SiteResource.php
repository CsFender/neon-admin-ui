<?php

namespace Neon\Admin\Resources;

use Neon\Admin\Resources\SiteResource\Pages;
use Neon\Admin\Resources\SiteResource\RelationManagers;
use Neon\Site\Models\Site;
use Closure;
use Filament\Forms;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
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

class SiteResource extends Resource
{
  protected static ?string $model = Site::class;

  protected static ?string $navigationIcon = 'heroicon-o-globe-europe-africa';

  protected static ?string $activeNavigationIcon = 'heroicon-s-globe-europe-africa';

  public static function getNavigationLabel(): string
  {
    return trans('neon-admin::admin.resources.sites.title');
  }

  public static function getNavigationGroup(): string
  {
    return trans('neon-admin::admin.navigation.web');
  }

  public static function getModelLabel(): string
  {
    return trans('neon-admin::admin.models.site');
  }

  public static function getPluralModelLabel(): string
  {
    return __('neon-admin::admin.models.sites');
  }

  public static function __form(): array
  {
    $t = [
      Fieldset::make(trans('neon-admin::admin.resources.sites.form.fieldset.name'))
        ->schema([
          TextInput::make('title')
            ->label(trans('neon-admin::admin.resources.sites.form.fields.title.label'))
            ->afterStateUpdated(function ($get, $set, ?string $state) {
              if (!$get('is_slug_changed_manually') && filled($state)) {
                $set('slug', Str::slug($state));
              }
            })
            ->reactive()
            ->required()
            ->maxLength(255),
          TextInput::make('slug')
            ->label(trans('neon-admin::admin.resources.sites.form.fields.slug.label'))
            ->afterStateUpdated(function (Closure $set) {
              $set('is_slug_changed_manually', true);
            })
            ->required()
        ])
        ->columns(2),
      FileUpload::make('favicon')
        ->image()
        ->getUploadedFileNameForStorageUsing(
          fn (TemporaryUploadedFile $file): string => (string) str($file->getClientOriginalName())
            ->prepend('neon-site-'),
        ),
      Repeater::make('domains')
        ->label(__('neon-admin::admin.resources.sites.form.fields.domains.label'))
        ->helperText(__('neon-admin::admin.resources.sites.form.fields.domains.help'))
        ->simple(
          TextInput::make('domain')
            // ->activeUrl()
            ->nullable(),
        )
        ->addActionLabel(__('neon-admin::admin.resources.sites.form.fields.domains.new'))
        ->columns(2),
      Repeater::make('prefixes')
        ->label(__('neon-admin::admin.resources.sites.form.fields.prefixes.label'))
        ->helperText(__('neon-admin::admin.resources.sites.form.fields.prefixes.help'))
        ->simple(
          TextInput::make('prefix')
            ->alphaDash()
            ->nullable(),
        )
        ->addActionLabel(__('neon-admin::admin.resources.sites.form.fields.prefixes.new'))
        ->columns(2),
      Select::make('locale')
        ->label(__('neon-admin::admin.resources.sites.form.fields.locale.label'))
        ->options(function (): array {
          $result = array();

          if (class_exists(\Mcamara\LaravelLocalization\LaravelLocalization::class)) {
            foreach (LaravelLocalization::getLocalesOrder() as $locale => $locale_data) {
              $result[$locale] = ucfirst($locale_data['native']);
            }
          }

          return $result;
        }),
      // Toggle::make('is_default')
      //   ->label(__('neon-admin::admin.resources.sites.form.fields.is_default.label'))
      //   ->onIcon('heroicon-m-check')
      //   ->onColor('success')
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

  public static function form(Form $form): Form
  {
    // if (in_array(\Neon\Attributable\Models\Traits\Attributable::class, class_uses_recursive(self::$model))) {
      return $form
        ->schema([
          Tabs::make('Tabs')
            ->tabs([
              Tabs\Tab::make(__('neon-admin::admin.resources.sites.form.tabs.basic'))
                ->schema(self::__form())
                ->columns(1),
              Tabs\Tab::make(__('neon-admin::admin.resources.sites.form.tabs.attributables'))
                ->icon('heroicon-o-adjustments-horizontal')
                ->schema([
                  TextInput::make('that')->label('that'),
                ]),
            ])
            ->activeTab(1)
        ])
        ->columns(1);
    // } else {
    //   return $form
    //     ->schema(self::__form())
    //     ->columns(1);
    // }
  }

  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        // Tables\Columns\TextColumn::make('id')
        //     ->searchable(),
        Tables\Columns\TextColumn::make('title')
          ->label(__('neon-admin::admin.resources.sites.form.fields.title.label'))
          ->searchable(),
        Tables\Columns\TextColumn::make('domains')
          ->label(__('neon-admin::admin.resources.sites.form.fields.domains.label'))
          ->badge()
          ->separator(',')
          ->searchable(),
        Tables\Columns\TextColumn::make('prefixes')
          ->label(__('neon-admin::admin.resources.sites.form.fields.prefixes.label'))
          ->badge()
          ->separator(',')
          ->searchable(),
        Tables\Columns\TextColumn::make('locale')
          ->label(__('neon-admin::admin.resources.sites.form.fields.locale.label'))
          ->getStateUsing(function (\Neon\Site\Models\Site $site) {
            $result = $site->locale;

            if (class_exists(\Mcamara\LaravelLocalization\LaravelLocalization::class)) {
              $result = ucfirst(LaravelLocalization::getLocalesOrder()[$site->locale]['native']);
            }

            return $result;
          }),
        Tables\Columns\ToggleColumn::make('is_default')
          ->label(__('neon-admin::admin.resources.sites.form.fields.is_default.label'))
          ->afterStateUpdated(function ($record, $state) {
            if ($state == true) {
              $class = $record::class;
              $class::where('is_default', '=', $state)
                ->where('id', '!=', $record->id)
                ->where('locale', '=', $record->locale)
                ->update([
                  'is_default' => false
                ]);
            }
          }),
        Tables\Columns\TextColumn::make('created_at')
          ->label('Létrehozva')
          ->dateTime()
          ->sortable()
          ->since()
          ->toggleable(isToggledHiddenByDefault: true),
        Tables\Columns\TextColumn::make('updated_at')
          ->label('Utoljára módosítva')
          ->dateTime()
          ->sortable()
          ->toggleable(isToggledHiddenByDefault: true)
          ->since(),
      ])
      ->filters([
        Tables\Filters\TrashedFilter::make(),
        Tables\Filters\SelectFilter::make('locale')
          ->options(function (): array {
            $result = array();

            if (class_exists(\Mcamara\LaravelLocalization\LaravelLocalization::class)) {
              foreach (LaravelLocalization::getLocalesOrder() as $locale => $locale_data) {
                $result[$locale] = ucfirst($locale_data['native']);
              }
            }

            return $result;
          })
          ->label(__('neon-admin::admin.resources.sites.form.fields.locale.label'))
          ->searchable(),
      ])
      ->actions([
        Tables\Actions\EditAction::make()
          ->slideOver(),
        Tables\Actions\DeleteAction::make(),
        Tables\Actions\ForceDeleteAction::make(),
        Tables\Actions\RestoreAction::make(),
      ])
      ->bulkActions([
        Tables\Actions\BulkActionGroup::make([
          Tables\Actions\DeleteBulkAction::make(),
          Tables\Actions\ForceDeleteBulkAction::make(),
          Tables\Actions\RestoreBulkAction::make(),
        ]),
      ]);
  }

  public static function getPages(): array
  {
    return [
      'index' => Pages\ManageSites::route('/'),
    ];
  }

  // public static function getEloquentQuery(): Builder
  // {
  //   return parent::getEloquentQuery()
  //     ->withoutGlobalScopes([
  //     ]);
  // }
}
