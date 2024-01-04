<?php

namespace Neon\Admin\Resources\AttributeResource\Pages;

use Neon\Admin\Resources\AttributeResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageAttributes extends ManageRecords
{
  protected static string $resource = AttributeResource::class;

  protected function getHeaderActions(): array
  {
    return [
      Actions\CreateAction::make()
        ->slideOver(),
    ];
  }
}
