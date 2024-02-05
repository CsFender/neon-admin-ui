<?php

namespace Neon\Admin\Resources\SlideshowResource\Pages;

use Neon\Admin\Resources\SlideshowResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewSlideshow extends ViewRecord
{
    protected static string $resource = SlideshowResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
