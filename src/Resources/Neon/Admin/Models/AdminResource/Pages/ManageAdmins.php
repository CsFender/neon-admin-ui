<?php

namespace Neon\Admin\Resources\AdminResource\Pages;

use Neon\Admin\Resources\AdminResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageAdmins extends ManageRecords
{
  protected static string $resource = AdminResource::class;

  protected function getHeaderActions(): array
  {
    return [
      Actions\CreateAction::make()
        ->slideOver(),
    ];
  }
}
