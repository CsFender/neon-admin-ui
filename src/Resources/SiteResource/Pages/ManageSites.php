<?php

namespace Neon\Admin\Resources\SiteResource\Pages;

use Neon\Admin\Resources\SiteResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageSites extends ManageRecords
{
  protected static string $resource = SiteResource::class;

  protected function getHeaderActions(): array
  {
    return [
      Actions\CreateAction::make()
        ->slideOver(),
    ];
  }
}
