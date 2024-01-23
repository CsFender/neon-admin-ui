<?php

namespace Neon\Admin\Resources\ContentResource\Pages;

use Neon\Admin\Resources\ContentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\IconPosition;
use Illuminate\Database\Eloquent\Builder;
use Neon\Models\Statuses\BasicStatus;
use Neon\Content\Models\Content;
use Neon\Models\Link;

class ListContent extends ListRecords
{
  protected static string $resource = ContentResource::class;

  protected function getHeaderActions(): array
  {
    return [
      Actions\CreateAction::make(),
    ];
  }

  public function getTabs(): array
  {
    return [
      'all'       => ListRecords\Tab::make(__('neon-admin::admin.resources.content.table.tabs.all')),
      'new'       => ListRecords\Tab::make(__('neon-admin::admin.resources.content.table.tabs.new'))
        ->query(fn ($query) => $query->where('status', BasicStatus::New))
        ->icon('heroicon-o-sparkles')
        ->iconPosition(IconPosition::After),
      'live'      => ListRecords\Tab::make(__('neon-admin::admin.resources.content.table.tabs.live'))
        ->query(fn (Builder $query): Builder => $query->where('status', BasicStatus::Active)->where('published_at', '<', now())->where(function ($query) {
          $query->whereNull('expired_at')->orWhere('expired_at', '>', now());
        }))
        ->badge(Link::query()
          ->where('status', BasicStatus::Active)
          ->where('published_at', '<', now())
          ->where(function ($query) {
            $query->whereNull('expired_at')->orWhere('expired_at', '>', now());
          })->count()),
    ];
  }

  public function getDefaultActiveTab(): string|int|null
  {
    return 'all';
  }
}
