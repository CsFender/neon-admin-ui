<?php

namespace Neon\Admin\Resources\SlideshowResource\Pages;

use Neon\Admin\Resources\SlideshowResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\IconPosition;
use Illuminate\Database\Eloquent\Builder;
use Neon\Models\Statuses\BasicStatus;
use Neon\Slideshow\Models\Slideshow;

class ListSlideshows extends ListRecords
{
  protected static string $resource = SlideshowResource::class;

  protected function getHeaderActions(): array
  {
    return [
      Actions\CreateAction::make(),
    ];
  }

  public function getTabs(): array
  {
    return [
      'all'       => ListRecords\Tab::make(__('neon-admin::admin.resources.slideshow.table.tabs.all')),
      'new'       => ListRecords\Tab::make(__('neon-admin::admin.resources.slideshow.table.tabs.new'))
        ->query(fn ($query) => $query->where('status', BasicStatus::New))
        ->icon('heroicon-o-sparkles')
        ->iconPosition(IconPosition::After),
      'live'      => ListRecords\Tab::make(__('neon-admin::admin.resources.slideshow.table.tabs.live'))
        ->query(fn (Builder $query): Builder => $query->withoutGlobalScopes()->where('status', BasicStatus::Active)->where('published_at', '<', now())->where(function ($query) {
          $query->whereNull('expired_at')->orWhere('expired_at', '>', now());
        }))
        ->badge(Slideshow::withoutGlobalScopes()
          ->where('status', BasicStatus::Active)
          ->where('published_at', '<', now())
          ->where(function ($query) {
            $query->whereNull('expired_at')->orWhere('expired_at', '>', now());
          })->count()),
      'archive'   => ListRecords\Tab::make(__('neon-admin::admin.resources.slideshow.table.tabs.archive'))
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
