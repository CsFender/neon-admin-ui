<?php

namespace Neon\Admin\Providers;

use App\Http\Middleware\TrustHosts;
use App\Http\Middleware\TrustProxies;
use Filament\Facades\Filament;
use Filament\FontProviders\GoogleFontProvider;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
  private function scanNeonResources(): array
  {
    $resources = [
      \Neon\Admin\Resources\SiteResource::class,
      \Neon\Admin\Resources\MenuResource::class,
      \Neon\Admin\Resources\MenuItemResource::class,
      \Neon\Admin\Resources\ContentResource::class,
      \Neon\Admin\Resources\AdminResource::class,
      \Neon\Admin\Resources\AttributeResource::class,
    ];

    if (class_exists(\Neon\News\Models\News::class))
    {
      $resources[] = \Neon\Admin\Resources\NewsResource::class;
    }

    if (class_exists(\Neon\Slideshow\Models\Slideshow::class))
    {
      $resources[] = \Neon\Admin\Resources\SlideshowResource::class;
    }

    if (class_exists(\Neon\Faq\Models\Faq::class))
    {
      $resources[] = \Neon\Admin\Resources\FaqResource::class;
      $resources[] = \Neon\Admin\Resources\FaqCategoryResource::class;
    }

    if (class_exists(\Neon\Document\Models\Document::class))
    {
      $resources[] = \Neon\Admin\Resources\DocumentResource::class;
      $resources[] = \Neon\Admin\Resources\DocumentCategoryResource::class;
    }

    return $resources;
  }

  public function panel(Panel $panel): Panel
  {
    // app()->setLocale('hu');


    Filament::registerRenderHook(
      'footer.after',
      fn (): string => 'Copyright 2023 - ' . date('Y') . ' &copy; Elementary Interactive. Neon vX.X',
    );

    $admin = $panel
      ->default()
      ->id('neon-admin')
      ->colors(config('neon-admin.colors', [
        'primary'   => '#3b0764',
        'danger'    => '#991b1b',
        'gray'      => '#d8b4fe',
        'info'      => '#6366f1',
        'success'   => '#5b21b6',
        'warning'   => '#c2410c',
      ]))
      ->databaseNotifications()
      ->middleware([
        EncryptCookies::class,
        AddQueuedCookiesToResponse::class,
        StartSession::class,
        AuthenticateSession::class,
        ShareErrorsFromSession::class,
        VerifyCsrfToken::class,
        SubstituteBindings::class,
        DisableBladeIconComponents::class,
        DispatchServingFilamentEvent::class,
        TrustProxies::class,
        TrustHosts::class
      ])
      // ->discoverResources(in: base_path('/vendor/neon/admin-ui/src/Resources'), for: 'Neon\\Admin\\Resources')
      ->resources($this->scanNeonResources())
      ->discoverPages(in: base_path('/vendor/neon/admin-ui/src/Pages'), for: 'Neon\\Admin\\Pages')
      ->pages([
        \Neon\Admin\Pages\Dashboard::class, // The basic Neon Admin Dashboard.
      ])
      ->discoverWidgets(in: base_path('/vendor/neon/admin-ui/src/Widgets'), for: 'Neon\\Admin\\Widgets');
    // ->topNavigation();

    if (config('neon-admin.path', 'admin') && !config('neon-admin.domain'))
    {
      $admin->path(config('neon-admin.path'));
    }

    if (!config('neon-admin.path') && config('neon-admin.domain'))
    {
      $admin->path(config('neon-admin.domain'));
    }

    if (config('neon-admin.guard'))
    {
      $admin
        ->login()
        ->authGuard(config('neon-admin.guard'))
        ->authMiddleware([
          Authenticate::class, // Authenticate admin.
        ]);
    }

    if (config('neon-admin.font', true))
    {
      $admin
        ->font(
          config('neon-admin.font.font-family', 'Inter'),
          provider: config('neon-admin.font.provider', GoogleFontProvider::class)
        );
    }

    if (is_array(config('neon-admin.resources')) && !empty(config('neon-admin.resources')))
    {
      foreach (config('neon-admin.resources') as $path) {
        $admin
          ->discoverResources(in: app_path($path), for: 'App\\Admin\\Resources');
      }
    }
    
    if (is_array(config('neon-admin.pages')) && !empty(config('neon-admin.pages')))
    {
      foreach (config('neon-admin.pages') as $path) {
        $admin
          ->discoverPages(in: app_path($path), for: 'App\\Admin\\Pages');
      }
    }
    
    if (is_array(config('neon-admin.widgets')) && !empty(config('neon-admin.widgets')))
    {
      foreach (config('neon-admin.widgets') as $path) {
        $admin
          ->discoverWidgets(in: app_path($path), for: 'App\\Admin\\Widgets');
      }
    }
    
    if (is_array(config('neon-admin.logo')) && !empty(config('neon-admin.logo')))
    {
      $admin
        ->brandLogo(fn () => view(config('neon-admin.logo.view')))
        ->brandLogoHeight(config('neon-admin.logo.height'));
    }
    
    if (is_array(config('neon-admin.plugins')) && !empty(config('neon-admin.plugins')))
    {
      $admin->plugins(config('neon-admin.plugins'));
    }
    
    if (config('neon-admin.unsaved-changes-alert', false))
    {
      $admin->unsavedChangesAlerts();
    }

    if (is_array(config('neon-admin.theme')) && !empty(config('neon-admin.theme')))
    {
      $admin
        ->viteTheme(config('neon-admin.theme'));
    }

    // if (config('neon-admin.groups')) {
    //   $admin
    //     ->navigationGroups(array_merge(config('neon-admin.groups', []), [
    //       'web', //__('neon-admin::admin.navigation.web'),
    //       'settings' //__('neon-admin::admin.navigation.settings')
    //     ]));
    // }

    $admin
      ->navigationGroups(array_merge(config('neon-admin.groups', []), [
        NavigationGroup::make()
          ->label(fn (): string => __('neon-admin::admin.navigation.web')),
        NavigationGroup::make()
          ->label(fn (): string => __('neon-admin::admin.navigation.settings'))
          ->collapsed(),
      ]));
    // ->navigationItems([
    //   \Filament\Navigation\NavigationItem::make('Analytics')
    //       ->url('https://filament.pirsch.io', shouldOpenInNewTab: true)
    //       ->icon('heroicon-o-presentation-chart-line')
    //       ->group('Reports')
    //       ->sort(3),]);

    $admin
      ->globalSearch(true)
      ->globalSearchKeyBindings(['command+k', 'ctrl+k']);

    return $admin;
  }
}
