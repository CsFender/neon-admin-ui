<?php

namespace Neon\Admin\Resources\ContentResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Neon\Admin\Resources\ContentResource;
use Neon\Admin\Resources\ContentResource\Pages;

class EditContent extends EditRecord
{
  protected static string $resource = ContentResource::class;

  protected function getHeaderActions(): array
  {
    return [
      Actions\Action::make('index')
        ->label('Vissza')
        ->color('gray')
        ->icon('heroicon-o-arrow-small-left')
        ->url(fn (): string => $this->getResource()::getUrl('index')),
      Actions\ViewAction::make(),
      Actions\DeleteAction::make(),
      Actions\ForceDeleteAction::make(),
      Actions\RestoreAction::make(),
    ];
  }

  protected function getRedirectUrl(): string
  {
    return $this->getResource()::getUrl('index');
  }
}
