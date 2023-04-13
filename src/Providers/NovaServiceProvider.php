<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Laravel\Nova\Nova;
use Laravel\Nova\NovaApplicationServiceProvider;

use App\Nova\Admin;
use App\Nova\Site;

use Illuminate\Http\Request;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Dashboards\Main;
use Laravel\Nova\Menu\Menu;
use Laravel\Nova\Menu\MenuItem;
use Laravel\Nova\Menu\MenuSection;

class NovaServiceProvider extends NovaApplicationServiceProvider
{
  /**
   * Bootstrap any application services.
   *
   * @return void
   */
  public function boot()
  {
    parent::boot();

    Nova::mainMenu(function (Request $request) {
      return [
        MenuSection::dashboard(Main::class)->icon('chart-bar'),

        MenuSection::make(__('Administer'), [
          MenuItem::resource(Admin::class)
            ->canSee(function (NovaRequest $request) {
              return $request->user()->can('viewAny', \Neon\Admin\Models\Admin::class);
            }),
        ])
          ->icon('adjustments')
          ->collapsable(),

        MenuSection::make(__('Website'), [
          MenuItem::resource(Site::class)
            ->canSee(function (NovaRequest $request) {
              return config('site.driver', 'file') == 'database' && $request->user()->can('viewAny', \Neon\Site\Models\Site::class);
            }),
        ])
          ->icon('globe')
          ->collapsable(),

        MenuSection::make(__('Resources'), [

          /** Here comes all the menu items...
         * 
         * ...
         * 
         * ...
         * 
         */
        ])->collapsable()
      ];
    });
  }

  /**
   * Register the Nova routes.
   *
   * @return void
   */
  protected function routes()
  {
    Nova::routes()
      ->withAuthenticationRoutes()
      ->withPasswordResetRoutes()
      ->register();
  }

  /**
   * Register the Nova gate.
   *
   * This gate determines who can access Nova in non-local environments.
   *
   * @return void
   */
  protected function gate()
  {
    Gate::define('viewNova', function ($user) {
      return in_array($user->email, [
        //
      ]);
    });
  }

  /**
   * Get the dashboards that should be listed in the Nova sidebar.
   *
   * @return array
   */
  protected function dashboards()
  {
    return [
      new \App\Nova\Dashboards\Main,
    ];
  }

  /**
   * Get the tools that should be listed in the Nova sidebar.
   *
   * @return array
   */
  public function tools()
  {
    return [];
  }

  /**
   * Register any application services.
   *
   * @return void
   */
  public function register()
  {
    //
  }
}
