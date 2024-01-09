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
use Neon\Admin\Resources\MenuResource\Pages;
use Neon\Admin\Resources\SiteResource\RelationManagers;
use Neon\Attributable\Models\Attribute;
use Neon\Models\Menu;
use Neon\Models\Scopes\ActiveScope;
use Neon\Models\Statuses\BasicStatus;
use Neon\Site\Models\Site;

class MenuResource extends Resource
{
  protected static ?int $navigationSort = 2;

  protected static ?string $model = Menu::class;

  protected static ?string $navigationIcon = 'heroicon-o-bars-3';

  protected static ?string $activeNavigationIcon = 'heroicon-s-bars-3';

  // public static function getNavigationParentItem(): string
  // {
  //   return __('neon-admin::admin.resources.sites.title');
  // }

  public static function getNavigationLabel(): string
  {
    return __('neon-admin::admin.navigation.menu');
  }

  public static function getNavigationGroup(): string
  {
    return __('neon-admin::admin.navigation.web');
  }

  public static function getModelLabel(): string
  {
    return __('neon-admin::admin.models.menu');
  }

  public static function getPluralModelLabel(): string
  {
    return __('neon-admin::admin.models.menu');
  }

  public static function __attributables(): array
  {
    $attributes = Attribute::where('class', self::$model)->get();
    $a = [];

    foreach ($attributes as $attribute) {
      $fieldComponment = 'Filament\Forms\Components\\';

      switch ($attribute['field']) {
        case 'text':
          $fieldComponment .= 'TextInput';
          break;
      }
      $field = $fieldComponment::make($attribute['slug'])
        ->label($attribute['name']);
      foreach ($attribute['rules'] as $rule) {
        $field->$rule();
      }
      $a[] = $field;
    }

    return $a;
  }

  public static function __form(): array
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
        ->label('Státusz')
        ->required()
        ->reactive()
        ->default(BasicStatus::New)
        ->options(BasicStatus::class),
      // MorphToSelect::make('site')
      //   ->types([
      //     MorphToSelect\Type::make(Site::class)
      //       ->titleAttribute('name')
      //   ]),
      Select::make('site')
        ->relationship(titleAttribute: 'title')
      // FileUpload::make('favicon')
      //   ->image()
      //   ->getUploadedFileNameForStorageUsing(
      //     fn (TemporaryUploadedFile $file): string => (string) str($file->getClientOriginalName())
      //       ->prepend('neon-site-'),
      //   ),
      // Repeater::make('domains')
      //   ->label(__('neon-admin::admin.resources.menu.form.fields.domains.label'))
      //   ->helperText(__('neon-admin::admin.resources.menu.form.fields.domains.help'))
      //   ->simple(
      //     TextInput::make('domain')
      //       // ->activeUrl()
      //       ->nullable(),
      //   )
      //   ->addActionLabel(__('neon-admin::admin.resources.menu.form.fields.domains.new'))
      //   ->columns(2),
      // Repeater::make('prefixes')
      //   ->label(__('neon-admin::admin.resources.menu.form.fields.prefixes.label'))
      //   ->helperText(__('neon-admin::admin.resources.menu.form.fields.prefixes.help'))
      //   ->simple(
      //     TextInput::make('prefix')
      //       ->alphaDash()
      //       ->nullable(),
      //   )
      //   ->addActionLabel(__('neon-admin::admin.resources.menu.form.fields.prefixes.new'))
      //   ->columns(2),
      // Select::make('locale')
      //   ->label(__('neon-admin::admin.resources.menu.form.fields.locale.label'))
      //   ->options(function (): array {
      //     $result = array();

      //     if (class_exists(\Mcamara\LaravelLocalization\LaravelLocalization::class)) {
      //       foreach (LaravelLocalization::getLocalesOrder() as $locale => $locale_data) {
      //         $result[$locale] = ucfirst($locale_data['native']);
      //       }
      //     }

      //     return $result;
      //   }),
      // Toggle::make('is_default')
      //   ->label(__('neon-admin::admin.resources.menu.form.fields.is_default.label'))
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
    if (in_array(\Neon\Attributable\Models\Traits\Attributable::class, class_uses_recursive(self::$model))) {
      return $form
        ->schema([
          Tabs::make('Tabs')
            ->tabs([
              Tabs\Tab::make(__('neon-admin::admin.resources.menu.form.tabs.basic'))
                ->schema(self::__form())
                ->columns(1),
              Tabs\Tab::make(__('neon-admin::admin.resources.menu.form.tabs.attributables'))
                ->icon('heroicon-o-adjustments-horizontal')
                ->schema(self::__attributables()),
            ])
            ->activeTab(1)
        ])
        ->columns(1);
    } else {
      return $form
        ->schema(self::__form())
        ->columns(1);
    }
  }

  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        // Tables\Columns\TextColumn::make('id')
        //     ->searchable(),
        Tables\Columns\TextColumn::make('title')
          ->label(__('neon-admin::admin.resources.menu.form.fields.title.label'))
          ->searchable(),
        




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
          ->label(__('neon-admin::admin.resources.menu.form.fields.locale.label'))
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
      'index' => Pages\ManageMenus::route('/'),
    ];
  }

  public static function getEloquentQuery(): Builder
  {
      return parent::getEloquentQuery()
          ->withoutGlobalScopes([
              ActiveScope::class
          ]);
  }
}
