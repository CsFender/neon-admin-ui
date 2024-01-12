<?php

namespace Neon\Admin\Pages;

use Filament\Tables\Columns\Layout\Panel;
use Filament\Widgets;

class Dashboard extends \Filament\Pages\Dashboard
{
  // protected static string $routePath = 'kezdooldal';

  public function getTitle(): string
  {
    return __('neon-admin::admin.navigation.home');
  }

  public function getWidgets(): array
  {
    return [
      Widgets\AccountWidget::class
    ];
  }
}
