<?php

namespace Neon\Admin\Resources\NewsResource\Pages;

use Neon\Admin\Resources\NewsResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateNews extends CreateRecord
{
    protected static string $resource = NewsResource::class;
}
