<?php

namespace Neon\Admin\Resources\SlideshowResource\Pages;

use Neon\Admin\Resources\SlideshowResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSlideshow extends EditRecord
{
    protected static string $resource = SlideshowResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
