<?php

namespace Neon\Admin\Resources\Traits;

use Closure;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Neon\Attributable\Models\Attribute;

trait NeonAdmin
{
  public static function attributables(): array
  {
    /**
     * @var array
     */
    $attributables  = [];

    $attributes     = Attribute::where('class', self::$model)->get();

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
      $attributables[] = $field;
    }

    return $attributables;
  }

  public static function form(Forms\Form $form): Forms\Form
  {
    if (in_array(\Neon\Attributable\Models\Traits\Attributable::class, class_uses_recursive(self::$model))) {
      return $form
        ->schema([
          Forms\Components\Tabs::make('Tabs')
            ->tabs([
              Forms\Components\Tabs\Tab::make(__('neon-admin::admin.resources.sites.form.tabs.basic'))
                ->schema(self::items())
                ->columns(1),
              Forms\Components\Tabs\Tab::make(__('neon-admin::admin.resources.sites.form.tabs.attributables'))
                ->icon('heroicon-o-adjustments-horizontal')
                ->schema(self::attributables()),
            ])
            ->activeTab(1)
        ])
        ->columns(1);
    } else {
      return $form
        ->schema(self::items())
        ->columns(1);
    }
  }

  public static function getEloquentQuery(): Builder
  {
    return parent::getEloquentQuery()
      ->withoutGlobalScopes([
        SoftDeletingScope::class
      ]);
  }

  protected static function bulkActions()
  {
    /**
     * @var array
     */
    $bulk_actions = [];

    if (in_array(\Neon\Models\Traits\Statusable::class, class_uses_recursive(self::$model))) {
      $bulk_actions[] = Tables\Actions\BulkAction::make('activate')
        ->label(__('neon-admin::admin.actions.acivate.label'))
        ->action(fn (Collection $records) => $records->each->activate())
        ->deselectRecordsAfterCompletion()
        ->color('success')
        ->icon('heroicon-o-check-circle');
      $bulk_actions[] = Tables\Actions\BulkAction::make('inactivate')
        ->label(__('neon-admin::admin.actions.inacivate.label'))
        ->action(fn (Collection $records) => $records->each->inactivate())
        ->deselectRecordsAfterCompletion()
        ->color('danger')
        ->icon('heroicon-o-x-circle');
    }
    $bulk_actions[] = Tables\Actions\DeleteBulkAction::make();


    if (in_array(\Illuminate\Database\Eloquent\SoftDeletes::class, class_uses_recursive(self::$model))) {
      $bulk_actions[] = Tables\Actions\ForceDeleteBulkAction::make();
      $bulk_actions[] = Tables\Actions\RestoreBulkAction::make();
    }
    return [Tables\Actions\BulkActionGroup::make($bulk_actions)];
  }
}
