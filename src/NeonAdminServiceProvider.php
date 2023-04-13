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
      $this->publishes([
        __DIR__ . '/../database/migrations/create_admins_table.php.stub'        => database_path('migrations/' . date('Y_m_d_', time()) . '000001_create_admins_table.php'),
        __DIR__ . '/../database/migrations/change_user_id_to_string_on_action_events_table.php.stub'        => database_path('migrations/' . date('Y_m_d_', time()) . '000002_change_user_id_to_string_on_action_events_table.php'),
        __DIR__ . '/../database/migrations/change_actionable_id_to_string_on_action_events_table.php.stub'        => database_path('migrations/' . date('Y_m_d_', time()) . '000003_change_actionable_id_to_string_on_action_events_table.php'),
        __DIR__ . '/../database/migrations/change_target_id_to_string_on_action_events_table.php.stub'        => database_path('migrations/' . date('Y_m_d_', time()) . '000004_change_target_id_to_string_on_action_events_table.php'),
        __DIR__ . '/../database/migrations/change_model_id_to_string_on_action_events_table.php.stub'        => database_path('migrations/' . date('Y_m_d_', time()) . '000004_change_model_id_to_string_on_action_events_table.php'),
      ], 'neon-migrations');
  
    // $kernel->pushMiddleware(SiteMiddleware::class);
    
      // file_put_contents(__DIR__.'/../config/config.php', Str::of(file_get_contents(__DIR__.'/../config/config.php'))->replace('##uuid##', Str::uuid()));
      // Storage::put(__DIR__.'/../config/config.php', Str::of(Storage::get(__DIR__.'/../config/config.php'))->replace('##uuid##', Str::uuid()));

      $this->publishes([
        __DIR__.'/../config/nova.php'                => config_path('nova.php'),
      ], 'neon-admin-config');

      $this->publishes([
        __DIR__.'/Nova/Admin.php'                    => app_path('Nova/Admin.php'),
        __DIR__.'/Nova/Site.php'                     => app_path('Nova/Site.php'),
      ], 'neon-admin-nova');

      $this->publishes([
        __DIR__.'/Policies/AdminPolicy.php.stub'     => app_path('Policies/AdminPolicy.php'),
        __DIR__.'/Policies/SitePolicy.php.stub'      => app_path('Policies/SitePolicy.php'),
      ], 'neon-admin-policies');

      $this->publishes([
        __DIR__.'/Providers/NovaServiceProvider.php' => app_path('Providers/NovaServiceProvider.php')
      ], 'neon-admin-nova');
      // $this->publishes([
      //   __DIR__.'/../config/config_database.php'   => config_path('site.php'),
      // ], 'neon-site-database');

      // if (!class_exists('CreateSitesTable')) {
      //   $this->publishes([
      //     __DIR__ . '/../database/migrations/create_sites_table.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_sites_table.php'),
      //     __DIR__ . '/../database/migrations/create_sites_pivot.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_sites_pivot.php'),
      //   ], 'neon-site');
      // }

      // $this->commands([
      //     SiteGenerateSiteIdCommand::class,
      //     SiteClearCommand::class
      // ]);
    }
  }

  public function register()
  {
    $this->mergeConfigFrom(__DIR__.'/../config/auth_guards.php', 'auth.guards');
    $this->mergeConfigFrom(__DIR__.'/../config/auth_passwords.php', 'auth.passwords');
    $this->mergeConfigFrom(__DIR__.'/../config/auth_providers.php', 'auth.providers');
  }
}
