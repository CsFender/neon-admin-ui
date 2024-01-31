<?php

namespace Neon\Admin\Resources;

use Filament\FilamentManager;
use Filament\Forms;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\MorphToSelect;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
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
use Neon\Admin\Resources\ContentResource\Pages;
use Neon\Admin\Resources\ContentResource\RelationManagers;
use Neon\Attributable\Models\Attribute;
use Neon\Models\Link;
use Neon\Models\Scopes\ActiveScope;
use Neon\Models\Scopes\PublishedScope;
use Neon\Models\Statuses\BasicStatus;
use Neon\Site\Models\Scopes\SiteScope;
use Neon\Site\Models\Site;

class ContentResource extends Resource
{
  use NeonAdmin;

  protected static ?int $navigationSort = 4;

  protected static ?string $model = Link::class;

  protected static ?string $navigationIcon = 'heroicon-o-document-text';

  protected static ?string $activeNavigationIcon = 'heroicon-s-document-text';

  protected static ?string $recordTitleAttribute = 'title';

  // public static function getNavigationParentItem(): string
  // {
  //   return __('neon-admin::admin.resources.sites.title');
  // }

  public static function getNavigationLabel(): string
  {
    return __('neon-admin::admin.navigation.content');
  }

  public static function getNavigationGroup(): string
  {
    return __('neon-admin::admin.navigation.web');
  }

  public static function getModelLabel(): string
  {
    return __('neon-admin::admin.models.content');
  }

  public static function getPluralModelLabel(): string
  {
    return __('neon-admin::admin.models.content');
  }

  public static function builders(): array
  {

    $builders = [
      Forms\Components\Builder\Block::make('heading')
        ->schema([
          TextInput::make('content')
            ->label(__('neon-admin::admin.resources.content.form.fields.content.heading.label'))
            ->required(),
          Select::make('level')
            ->options([
              'h1' => __('neon-admin::admin.resources.content.form.fields.content.heading.options.h1'),
              'h2' => __('neon-admin::admin.resources.content.form.fields.content.heading.options.h2'),
              'h3' => __('neon-admin::admin.resources.content.form.fields.content.heading.options.h3'),
              'h4' => __('neon-admin::admin.resources.content.form.fields.content.heading.options.h4'),
              'h5' => __('neon-admin::admin.resources.content.form.fields.content.heading.options.h5'),
              'h6' => __('neon-admin::admin.resources.content.form.fields.content.heading.options.h6'),
            ])
            ->required(),
        ])
        ->icon('heroicon-m-bars-2')
        ->columns(2),
    ];

    foreach (app('filament')->getResources() as $resource) {
      if (method_exists($resource, 'builders') && $resource != self::class)
      {
        try {
          $builders = array_merge($builders, $resource::builders());
        } catch (\Exception $e) {
          dd($e);
        }
      }
    }

    /** Get the builders' list!
     */
    return $builders;
  }

  public static function tabs(): array
  {
    return  [
      Forms\Components\Tabs\Tab::make(__('neon-admin::admin.resources.content.form.tabs.content'))
        ->schema([
          Forms\Components\Builder::make('content')
            ->label(__('neon-admin::admin.resources.content.form.fields.content.label'))
            ->addActionLabel(__('neon-admin::admin.resources.content.form.fields.content.new'))
            // ->addActionIcon('heroicons-o-squares-2x2')
            ->blocks(self::builders())
            ->blockNumbers(false)
            ->collapsed()
            ->minItems(1),
        ])
        ->columns(1)
    ];
  }

  public static function items(): array
  {
    $t = [
      Fieldset::make(__('neon-admin::admin.resources.content.form.fieldset.name'))
        ->schema([
          TextInput::make('title')
            ->label(__('neon-admin::admin.resources.content.form.fields.title.label'))
            ->afterStateUpdated(function ($get, $set, ?string $state) {
              if (!$get('is_slug_changed_manually') && filled($state)) {
                $set('slug', Str::slug($state));
              }
            })
            ->reactive()
            ->required()
            ->maxLength(255),
          TextInput::make('slug')
            ->label(__('neon-admin::admin.resources.content.form.fields.slug.label'))
            ->afterStateUpdated(function (Closure $set) {
              $set('is_slug_changed_manually', true);
            })
            ->required()
            ->helperText(__('neon-admin::admin.resources.content.form.fields.slug.help')),
        ]),
      Fieldset::make(__('neon-admin::admin.resources.content.form.fieldset.og_data'))
        ->schema([
          TextInput::make('og_title')
            ->label(trans('neon-admin::admin.resources.content.form.fields.og_title.label')),
          SpatieMediaLibraryFileUpload::make('og_image')
            ->label(trans('neon-admin::admin.resources.content.form.fields.og_image.label'))
            ->collection('og_image')
            ->responsiveImages(),
          Forms\Components\Textarea::make('og_description')
            ->label(trans('neon-admin::admin.resources.content.form.fields.og_description.label'))
            ->rows(4)
            ->columnSpanFull(),
        ]),
      Fieldset::make(__('neon-admin::admin.resources.content.form.fieldset.publishing'))
        ->schema([
          Select::make('site')
            ->label(__('neon-admin::admin.resources.content.form.fields.site.label'))
            ->multiple()
            ->relationship(titleAttribute: 'title'),
          Select::make('status')
            ->label(__('neon-admin::admin.resources.content.form.fields.status.label'))
            ->required()
            ->reactive()
            ->default(BasicStatus::default())
            ->options(BasicStatus::class),
          Forms\Components\DateTimePicker::make('published_at')
            ->label(__('neon-admin::admin.resources.content.form.fields.published_at.label')),
          Forms\Components\DateTimePicker::make('expired_at')
            ->label(__('neon-admin::admin.resources.content.form.fields.expired_at.label'))
            ->minDate(now()),
        ])
        ->columns(2)
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
          ->label(__('neon-admin::admin.resources.content.form.fields.title.label'))
          ->searchable(),
        Tables\Columns\TextColumn::make('site.title')
          ->label(__('neon-admin::admin.resources.content.form.fields.site.label'))
          ->listWithLineBreaks()
          ->bulleted()
          ->searchable(),
        Tables\Columns\IconColumn::make('status')
          ->label(__('neon-admin::admin.resources.content.form.fields.status.label'))
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
        Tables\Filters\SelectFilter::make('site')
          ->label(__('neon-admin::admin.resources.content.form.fields.site.label'))
          ->relationship('site', 'title'),
        Tables\Filters\TrashedFilter::make(),
      ])
      ->actions([
        Tables\Actions\EditAction::make(),
        Tables\Actions\DeleteAction::make(),
        Tables\Actions\ForceDeleteAction::make(),
        Tables\Actions\RestoreAction::make(),
      ])
      ->bulkActions(self::bulkActions());
  }

  public static function getPages(): array
  {
    return [
      // 'index' => Pages\ManageContents::route('/'),
      'index' => Pages\ListContent::route('/'),
      'create' => Pages\CreateContent::route('/create'),
      'view' => Pages\ViewContent::route('/{record}'),
      'edit' => Pages\EditContent::route('/{record}/edit'),
    ];
  }

  public static function getEloquentQuery(): Builder
  {
    return parent::getEloquentQuery()
      ->withoutGlobalScopes([
        ActiveScope::class,
        PublishedScope::class,
        SiteScope::class,
        SoftDeletingScope::class
      ]);
  }
}
