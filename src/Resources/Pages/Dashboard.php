<?php
 
namespace Neon\Admin\Resources\Pages;

use Filament\Tables\Columns\Layout\Panel;
use Filament\Widgets;

class Dashboard extends \Filament\Pages\Dashboard
{
    // protected static string $routePath = 'kezdooldal';

    protected static ?string $title = 'Vezérlőpult';

    public function getWidgets(): array
    {
        return [
                Widgets\AccountWidget::class
            ];
    }
}