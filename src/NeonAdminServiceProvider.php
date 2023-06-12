<?php

namespace Neon\Admin;

use \Illuminate\Support\Str;
use \Illuminate\Support\ServiceProvider;
use \Illuminate\Support\Facades\Storage;
use \Illuminate\Contracts\Http\Kernel;
use \Neon\Site\Http\Middleware\SiteMiddleware;
use \Neon\Site\Console\SiteClearCommand;
use \Neon\Site\Console\SiteGenerateSiteIdCommand;

class NeonAdminServiceProvider extends ServiceProvider
{

  /** Bootstrap any application services.
   *
   * @param \Illuminate\Contracts\Http\Kernel  $kernel
   *
   * @return void
   */
  public function boot(Kernel $kernel): void
  {
    if ($this->app->runningInConsole()) {
      
      /** Export migrations.
       */
      if (!class_exists('CreateAdminsTable')) {
        $this->publishes([
          __DIR__ . '/../database/migrations/create_admins_table.php.stub'
            => database_path('migrations/' . date('Y_m_d_', time()) . '000001_create_admins_table.php'),
        ], 'neon-migrations');
      }
      if (!class_exists('ChangeUserIdToStringOnActionEventsTable')) {
        $this->publishes([
          __DIR__ . '/../database/migrations/change_user_id_to_string_on_action_events_table.php.stub'
            => database_path('migrations/' . date('Y_m_d_', time()) . '000002_change_user_id_to_string_on_action_events_table.php'),
        ], 'neon-migrations');
      }
      if (!class_exists('ChangeActionableIdToStringOnActionEventsTable')) {
        $this->publishes([
          __DIR__ . '/../database/migrations/change_actionable_id_to_string_on_action_events_table.php.stub'
            => database_path('migrations/' . date('Y_m_d_', time()) . '000003_change_actionable_id_to_string_on_action_events_table.php'),
        ], 'neon-migrations');
      }
      if (!class_exists('ChangeTargetIdToStringOnActionEventsTable')) {
        $this->publishes([
          __DIR__ . '/../database/migrations/change_target_id_to_string_on_action_events_table.php.stub'
            => database_path('migrations/' . date('Y_m_d_', time()) . '000004_change_target_id_to_string_on_action_events_table.php'),
        ], 'neon-migrations');
      }
      if (!class_exists('ChangeModelIdToStringOnActionEventsTable')) {
        $this->publishes([
          __DIR__ . '/../database/migrations/change_model_id_to_string_on_action_events_table.php.stub'
            => database_path('migrations/' . date('Y_m_d_', time()) . '000004_change_model_id_to_string_on_action_events_table.php'),
        ], 'neon-migrations');
      }

      $this->publishes([
        __DIR__.'/../config/nova.php'                => config_path('nova.php'),
      ], 'neon-configs');

      $this->publishes([
        __DIR__.'/Nova/Admin.php'                    => app_path('Nova/Admin.php'),
        __DIR__.'/Nova/Link.php'                     => app_path('Nova/Link.php'),
        __DIR__.'/Nova/Menu.php'                     => app_path('Nova/Menu.php'),
        __DIR__.'/Nova/Site.php'                     => app_path('Nova/Site.php'),

        __DIR__.'/Policies/AdminPolicy.php.stub'     => app_path('Policies/AdminPolicy.php'),
        __DIR__.'/Policies/MenuPolicy.php.stub'      => app_path('Policies/MenuPolicy.php'),
        __DIR__.'/Policies/LinkPolicy.php.stub'      => app_path('Policies/LinkPolicy.php'),
        __DIR__.'/Policies/SitePolicy.php.stub'      => app_path('Policies/SitePolicy.php'),
      ], 'neon-admin');

      $this->publishes([
        __DIR__.'/Providers/NovaServiceProvider.php' => app_path('Providers/NovaServiceProvider.php')
      ], 'neon-admin-nova');
    }
  }

  public function register()
  {
    $this->mergeConfigFrom(__DIR__.'/../config/auth_guards.php', 'auth.guards');
    $this->mergeConfigFrom(__DIR__.'/../config/auth_passwords.php', 'auth.passwords');
    $this->mergeConfigFrom(__DIR__.'/../config/auth_providers.php', 'auth.providers');
  }
}
