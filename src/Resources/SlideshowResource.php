<?php

namespace Neon\Admin\Resources;

use Neon\Admin\Resources\SlideshowResource\Pages;
use Neon\Admin\Resources\SlideshowResource\RelationManagers;
use Neon\Admin\Resources\Traits\NeonAdmin;
use Neon\Slideshow\Models\Slideshow;
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
use Neon\Slideshow\Models\SlideshowItem;
use Spatie\Tags\Tag;

class SlideshowResource extends Resource
{
  use NeonAdmin;

  protected static ?string $model = Slideshow::class;

  protected static ?int $navigationSort = 6;

  protected static ?string $navigationIcon = 'heroicon-o-photo';

  protected static ?string $activeNavigationIcon = 'heroicon-s-photo';

  protected static ?string $recordTitleAttribute = 'title';

  public static function getNavigationLabel(): string
  {
    return __('neon-admin::admin.navigation.slideshow');
  }

  public static function getNavigationGroup(): string
  {
    return __('neon-admin::admin.navigation.web');
  }

  public static function getModelLabel(): string
  {
    return __('neon-admin::admin.models.slideshow_item');
  }

  public static function getPluralModelLabel(): string
  {
    return __('neon-admin::admin.models.slideshow');
  }

  public static function builders(): array
  {

    return [
      Forms\Components\Builder\Block::make('slideshow-block')
        ->label(__('neon-admin::admin.resources.slideshow.blocks.slideshow-block.label'))
        ->schema([
          Select::make('slideshow')
            ->label(__('neon-admin::admin.resources.slideshow.blocks.slideshow-block.slideshow.label'))
            ->helperText(__('neon-admin::admin.resources.slideshow.blocks.slideshow-block.slideshow.help'))
            ->options(Slideshow::all()->pluck('title', 'id'))
            ->multiple()
            ->columnSpan(2)
        ])
        ->icon('heroicon-m-photo')
        ->columns(3),
    ];
  }


  public static function items(): array
  {
    return [
      TextInput::make('title')
        ->label(__('neon-admin::admin.resources.slideshow.form.fields.title.label'))
        ->required()
        ->maxLength(255),
      Fieldset::make(__('neon-admin::admin.resources.slideshow.form.fieldset.publishing'))
        ->schema([
          Select::make('site')
            ->label(__('neon-admin::admin.resources.slideshow.form.fields.site.label'))
            ->multiple()
            ->native(false)
            ->relationship(titleAttribute: 'title'),
          Select::make('status')
            ->label(__('neon-admin::admin.resources.slideshow.form.fields.status.label'))
            ->required()
            ->native(false)
            ->default(BasicStatus::default())
            ->options(BasicStatus::class),
          Forms\Components\DateTimePicker::make('published_at')
            ->label(__('neon-admin::admin.resources.slideshow.form.fields.published_at.label')),
          Forms\Components\DateTimePicker::make('expired_at')
            ->label(__('neon-admin::admin.resources.slideshow.form.fields.expired_at.label'))
            ->minDate(now()),
        ])
        ->columns(4),
      Repeater::make('items')
        ->label(__('neon-admin::admin.resources.slideshow_items.form.fieldset.items.label'))
        ->addActionLabel(__('neon-admin::admin.resources.slideshow_items.form.fieldset.items.button'))
        ->relationship()
        ->reorderableWithButtons()
        ->orderColumn('order')
        ->collapsible()
        ->collapsed()
        ->schema([
          TextInput::make('title')
            ->label(__('neon-admin::admin.resources.slideshow_items.form.fields.title.label'))
            ->helperText(__('neon-admin::admin.resources.slideshow_items.form.fields.title.help')),
          SpatieMediaLibraryFileUpload::make('slideshow-item')
            ->label(__('neon-admin::admin.resources.slideshow_items.form.fields.media.label'))
            ->collection(SlideshowItem::MEDIA_SLIDE)
            ->responsiveImages(),
          Forms\Components\Textarea::make('lead')
            ->label(__('neon-admin::admin.resources.slideshow_items.form.fields.lead.label'))
            ->rows(4)
            ->columnSpanFull(),
          TextInput::make('cta_text')
            ->label(__('neon-admin::admin.resources.slideshow_items.form.fields.cta_text.label'))
            ->helperText(__('neon-admin::admin.resources.slideshow_items.form.fields.cta_text.help')),
          TextInput::make('cta_link')
            ->label(__('neon-admin::admin.resources.slideshow_items.form.fields.cta_link.label'))
            ->helperText(__('neon-admin::admin.resources.slideshow_items.form.fields.cta_link.help'))
            ->activeUrl(),
          Select::make('status')
            ->label(__('neon-admin::admin.resources.slideshow_items.form.fields.status.label'))
            ->required()
            ->native(false)
            ->default(BasicStatus::default())
            ->options(BasicStatus::class),

        ])
        ->columns(2),
    ];
  }

  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        Tables\Columns\TextColumn::make('site.title')
          ->label(__('neon-admin::admin.resources.slideshow.form.fields.site.label'))
          ->toggleable(isToggledHiddenByDefault: false),
        Tables\Columns\TextColumn::make('title')
          ->label(__('neon-admin::admin.resources.slideshow.form.fields.title.label'))
          ->searchable(),
        // Tables\Columns\TextColumn::make('lead')
        //   ->label(__('neon-admin::admin.resources.slideshow.form.fields.lead.label'))
        //   ->searchable(),
        Tables\Columns\TextColumn::make('items_count')
          ->label(__('neon-admin::admin.resources.slideshow.form.fields.items.label'))
          ->counts('items'),
        Tables\Columns\ImageColumn::make('items.media')
          ->label(__('neon-admin::admin.resources.slideshow.form.fields.items_media.label'))
          ->circular()
          ->stacked(),
        Tables\Columns\IconColumn::make('status')
          ->label(__('neon-admin::admin.resources.slideshow.form.fields.status.label'))
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
        Tables\Columns\TextColumn::make('published_at')
          ->label(__('neon-admin::admin.resources.slideshow.form.fields.published_at.label'))
          ->description(fn (Slideshow $record): string => $record->expired_at?->format('M j, Y') ?: '-')
          ->date()
          ->sortable(),
        Tables\Columns\TextColumn::make('expired_at')
          ->label(__('neon-admin::admin.resources.slideshow.form.fields.expired_at.label'))
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
          ->label(__('neon-admin::admin.resources.slideshow.form.fields.site.label'))
          ->relationship('site', 'title'),
        // Filter::make('is_active')
        //   ->query(fn (Builder $query): Builder => $query->where('status', BasicStatus::Active))
        //   ->label(__('neon-admin::admin.resources.slideshow.form.filters.is_active'))
        //   ->toggle(),
        // Filter::make('is_published')
        //   ->query(fn (Builder $query): Builder => $query->where('published_at', '<', now())->where(function ($query) {
        //     $query->whereNull('expired_at')->orWhere('expired_at', '>', now());
        //   }))
        //   ->label(__('neon-admin::admin.resources.slideshow.form.filters.is_published'))
        //   ->toggle(),
        Tables\Filters\TrashedFilter::make(),
      ])
      ->actions([
        // Tables\Actions\ViewAction::make(),
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
      'index' => Pages\ListSlideshows::route('/'),
      'create' => Pages\CreateSlideshow::route('/create'),
      // 'view' => Pages\ViewSlideshow::route('/{record}'),
      'edit' => Pages\EditSlideshow::route('/{record}/edit'),
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
