<?php

namespace Neon\Admin\Resources;

use Neon\Admin\Resources\NewsResource\Pages;
use Neon\Admin\Resources\NewsResource\RelationManagers;
use Neon\Admin\Resources\Traits\NeonAdmin;
use Neon\News\Models\News;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\MorphToSelect;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\SpatieTagsInput;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TextArea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;
use Neon\Models\Scopes\ActiveScope;
use Neon\Models\Scopes\PublishedScope;
use Neon\Models\Statuses\BasicStatus;

use Neon\Site\Models\Scopes\SiteScope;

class NewsResource extends Resource
{
  use NeonAdmin;

  protected static ?string $model = News::class;

  protected static ?int $navigationSort = 3;

  protected static ?string $navigationIcon = 'heroicon-o-newspaper';

  protected static ?string $recordTitleAttribute = 'title';

  public static function getNavigationLabel(): string
  {
    return __('neon-admin::admin.navigation.news');
  }

  public static function getNavigationGroup(): string
  {
    return __('neon-admin::admin.navigation.web');
  }

  public static function getModelLabel(): string
  {
    return __('neon-admin::admin.models.news_item');
  }

  public static function getPluralModelLabel(): string
  {
    return __('neon-admin::admin.models.news');
  }

  public static function items(): array
  {
    return [
      TextInput::make('title')
        ->label(trans('neon-admin::admin.resources.news.form.fields.title.label'))
        ->afterStateUpdated(function ($get, $set, ?string $state) {
          if (!$get('is_slug_changed_manually') && filled($state)) {
            $set('slug', Str::slug($state));
          }
        })
        ->reactive()
        ->required()
        ->maxLength(255),
      TextInput::make('slug')
        ->label(trans('neon-admin::admin.resources.news.form.fields.slug.label'))
        ->afterStateUpdated(function (Closure $set) {
          $set('is_slug_changed_manually', true);
        })
        ->required(),
      SpatieMediaLibraryFileUpload::make('header_image')
        ->label(trans('neon-admin::admin.resources.news.form.fields.header_image.label'))
        ->collection('header_image')
        ->responsiveImages(),
      Forms\Components\Textarea::make('lead')
        ->label(trans('neon-admin::admin.resources.news.form.fields.lead.label'))
        ->required()
        ->rows(10)
        ->columnSpanFull(),
      SpatieMediaLibraryFileUpload::make('content_image')
        ->label(trans('neon-admin::admin.resources.news.form.fields.content_image.label'))
        ->collection('content_image')
        ->multiple()
        ->responsiveImages(),
      Forms\Components\RichEditor::make('content')
        ->label(__('neon-admin::admin.resources.news.form.fields.content.label'))
        ->required()
        ->toolbarButtons([
          'blockquote',
          'bold',
          'bulletList',
          'codeBlock',
          'h2',
          'h3',
          'italic',
          'link',
          'orderedList',
          'redo',
          'strike',
          'underline',
          'undo',
        ])
        ->columnSpanFull(),
      SpatieTagsInput::make('tags')
        ->label(__('neon-admin::admin.resources.news.form.fields.tags.label')),
      Fieldset::make(__('neon-admin::admin.resources.news.form.fieldset.publishing'))
        ->schema([
          Select::make('site')
            ->label(trans('neon-admin::admin.resources.news.form.fields.site.label'))
            ->multiple()
            ->relationship(titleAttribute: 'title'),
          Select::make('status')
            ->label(trans('neon-admin::admin.resources.news.form.fields.status.label'))
            ->required()
            ->reactive()
            ->default(BasicStatus::default())
            ->options(BasicStatus::class),
          Forms\Components\DateTimePicker::make('published_at')
            ->label(trans('neon-admin::admin.resources.news.form.fields.published.label')),
          Forms\Components\DateTimePicker::make('expired_at')
            ->label(trans('neon-admin::admin.resources.news.form.fields.expired_at.label'))
            ->minDate(now()),
          Toggle::make('pinned')
            ->label(__('neon-admin::admin.resources.news.form.fields.pinned.label'))
            ->onIcon('heroicon-o-check-circle'),
        ])
        ->columns(2)
    ];
  }

  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        Tables\Columns\TextColumn::make('site.title'),
        Tables\Columns\TextColumn::make('title')
          ->label(__('neon-admin::admin.resources.news.form.fields.title.label'))
          ->description(fn (News $record): string => $record->slug)
          ->searchable(),
        Tables\Columns\TextColumn::make('lead')
          ->label(__('neon-admin::admin.resources.news.form.fields.lead.label'))
          ->searchable(),
        Tables\Columns\IconColumn::make('status')
          ->label(__('neon-admin::admin.resources.news.form.fields.status.label'))
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
        Tables\Columns\ToggleColumn::make('pinned')
          ->label(__('neon-admin::admin.resources.news.form.fields.pinned.label')),
        Tables\Columns\TextColumn::make('published_at')
          ->label(__('neon-admin::admin.resources.news.form.fields.published_at.label'))
          ->description(fn (News $record): string => $record->expired_at?->format('M j, Y') ?: '-')
          ->date()
          ->sortable(),
        Tables\Columns\TextColumn::make('expired_at')
          ->label(__('neon-admin::admin.resources.news.form.fields.expired_at.label'))
          ->date()
          ->toggleable(isToggledHiddenByDefault: true),
        Tables\Columns\TextColumn::make('created_at')
          ->since()
          ->sortable()
          ->toggleable(isToggledHiddenByDefault: true),
        Tables\Columns\TextColumn::make('updated_at')
          ->since()
          ->sortable()
          ->toggleable(isToggledHiddenByDefault: true),
        Tables\Columns\TextColumn::make('deleted_at')
          ->since()
          ->sortable()
          ->toggleable(isToggledHiddenByDefault: true),
      ])
      ->filters([
        Tables\Filters\SelectFilter::make('site')
          ->label(__('neon-admin::admin.resources.news.form.fields.site.label'))
          ->relationship('site', 'title'),
        // Filter::make('is_active')
        //   ->query(fn (Builder $query): Builder => $query->where('status', BasicStatus::Active))
        //   ->label(__('neon-admin::admin.resources.news.form.filters.is_active'))
        //   ->toggle(),
        // Filter::make('is_published')
        //   ->query(fn (Builder $query): Builder => $query->where('published_at', '<', now())->where(function ($query) {
        //     $query->whereNull('expired_at')->orWhere('expired_at', '>', now());
        //   }))
        //   ->label(__('neon-admin::admin.resources.news.form.filters.is_published'))
        //   ->toggle(),
        Tables\Filters\TrashedFilter::make(),
      ])
      ->actions([
        Tables\Actions\ViewAction::make(),
        Tables\Actions\EditAction::make(),
      ])
      ->bulkActions(self::bulkActions());
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
      'index' => Pages\ListNews::route('/'),
      'create' => Pages\CreateNews::route('/create'),
      'view' => Pages\ViewNews::route('/{record}'),
      'edit' => Pages\EditNews::route('/{record}/edit'),
    ];
  }

  public static function getEloquentQuery(): Builder
  {
    return parent::getEloquentQuery()
      ->withoutGlobalScopes([
        SoftDeletingScope::class,
        ActiveScope::class,
        PublishedScope::class,
        SiteScope::class,
      ]);
  }
}
