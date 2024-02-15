<?php

namespace Neon\Admin\Resources;

use Neon\Admin\Resources\AdminResource\Pages;
use Neon\Admin\Resources\AdminResource\RelationManagers;
use Neon\Admin\Models\Admin;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class AdminResource extends Resource
{
  protected static ?int $navigationSort = 99;
  
  protected static ?string $model = Admin::class;

  protected static ?string $navigationIcon = 'heroicon-o-user';

  protected static ?string $activeNavigationIcon = 'heroicon-s-user';

  public static function getNavigationLabel(): string
  {
    return trans('neon-admin::admin.resources.admins');
  }

  public static function getNavigationGroup(): string
  {
    return trans('neon-admin::admin.navigation.settings');
  }

  public static function getModelLabel(): string
  {
    return trans('neon-admin::admin.models.admin');
  }

  public static function getPluralModelLabel(): string
  {
    return trans('neon-admin::admin.models.admins');
  }

  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        // Forms\Components\TextInput::make('name')
        //     ->required()
        //     ->maxLength(255),
        // Forms\Components\TextInput::make('slug')
        //     ->required()
        //     ->maxLength(255),
        Forms\Components\TextInput::make('name')
          ->required()
          ->maxLength(255),
        Forms\Components\TextInput::make('email')
          ->required()
          ->unique(ignoreRecord: true)
          ->email()
          ->maxLength(255),
      ]);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        // Tables\Columns\TextColumn::make('id')
        //     ->searchable(),
        Tables\Columns\TextColumn::make('name')
          ->searchable(),
        Tables\Columns\TextColumn::make('email')
          ->searchable(),
        Tables\Columns\TextColumn::make('created_at')
          ->dateTime()
          ->sortable()
          ->since()
          ->toggleable(isToggledHiddenByDefault: true),
        Tables\Columns\TextColumn::make('updated_at')
          ->dateTime()
          ->sortable()
          ->toggleable(isToggledHiddenByDefault: true)
          ->since(),
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
      'index' => Pages\ManageAdmins::route('/'),
    ];
  }

  public static function getEloquentQuery(): Builder
  {
    return parent::getEloquentQuery()
      ->withoutGlobalScopes([
        SoftDeletingScope::class,
      ]);
  }
}
