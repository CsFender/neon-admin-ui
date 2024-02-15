<?php

namespace Neon\Admin\Resources\NewsResource\Pages;

use Neon\Admin\Resources\NewsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\IconPosition;
use Illuminate\Database\Eloquent\Builder;
use Neon\Models\Statuses\BasicStatus;
use Neon\News\Models\News;

class ListNews extends ListRecords
{
  protected static string $resource = NewsResource::class;

  protected function getHeaderActions(): array
  {
    return [
      Actions\CreateAction::make(),
    ];
  }

  public function getTabs(): array
  {
    return [
      'all'       => ListRecords\Tab::make(__('neon-admin::admin.resources.news.table.tabs.all')),
      'new'       => ListRecords\Tab::make(__('neon-admin::admin.resources.news.table.tabs.new'))
        ->query(fn ($query) => $query->where('status', BasicStatus::New))
        ->icon('heroicon-o-sparkles')
        ->iconPosition(IconPosition::After),
      'pinned'    => ListRecords\Tab::make(__('neon-admin::admin.resources.news.table.tabs.pinned'))
        ->query(fn (Builder $query): Builder => $query
          ->where('status', BasicStatus::Active)
          ->where('pinned', true)
          ->where('published_at', '<', now())->where(function ($query) {
            $query->whereNull('expired_at')->orWhere('expired_at', '>', now());
          }))
        ->icon('heroicon-o-star')
        ->iconPosition(IconPosition::After),      
      'live'      => ListRecords\Tab::make(__('neon-admin::admin.resources.news.table.tabs.live'))
        ->query(fn (Builder $query): Builder => $query->where('status', BasicStatus::Active)->where('published_at', '<', now())->where(function ($query) {
          $query->whereNull('expired_at')->orWhere('expired_at', '>', now());
        }))
        ->badge(News::where('status', BasicStatus::Active)
          ->where('published_at', '<', now())
          ->where(function ($query) {
            $query->whereNull('expired_at')->orWhere('expired_at', '>', now());
          })->count()),
      'archive'   => ListRecords\Tab::make(__('neon-admin::admin.resources.news.table.tabs.archive'))
        ->query(fn ($query) => $query->where('expired_at', '<', now()))
        ->icon('heroicon-o-archive-box')
        ->iconPosition(IconPosition::After),
    ];
  }

  public function getDefaultActiveTab(): string|int|null
  {
    return 'all';
  }
}
