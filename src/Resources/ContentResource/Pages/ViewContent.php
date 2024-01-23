<?php

namespace Neon\Admin\Resources\ContentResource\Pages;

use Neon\Admin\Resources\ContentResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewContent extends ViewRecord
{
    protected static string $resource = ContentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
