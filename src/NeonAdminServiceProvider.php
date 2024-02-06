<?php

namespace Neon\Admin;

use \Illuminate\Support\Str;
use \Illuminate\Support\Facades\Storage;

use \Neon\Site\Http\Middleware\SiteMiddleware;
use \Neon\Site\Console\SiteClearCommand;
use \Neon\Site\Console\SiteGenerateSiteIdCommand;

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Support\ServiceProvider;
use Neon\Admin\Providers\AdminPanelProvider;
use Neon\Attributable\Console\AttributableClearCommand;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Spatie\LaravelPackageTools\Package;

class NeonAdminServiceProvider extends PackageServiceProvider
{
  const VERSION = '3.0.0-alpha-12';

  public function configurePackage(Package $package): void
  {
    AboutCommand::add('Neon', 'Admin', self::VERSION);

    $package
      ->name('neon-admin')
      ->hasConfigFile()
      // ->publishesServiceProvider('AdminPanelProvider')
      ->hasMigrations(['create_admins_table'])
      ->hasTranslations()
      ->hasCommands([
        \Neon\Admin\Console\MakeAdmin::class
      ])
      ->hasInstallCommand(function (InstallCommand $command) {
        $command
            ->startWith(function (InstallCommand $command) {
              $command->line('');
              $command->info('Installing the necessary Filament components and assets.');
              $command->line('');

              /** All the filament stuff... */
              $command->callSilent('vendor:publish', ['--tag' => 'filament-actions-migrations']);
              $command->callSilent('vendor:publish', ['--tag' => 'filament-actions-translations']);
              $command->callSilent('vendor:publish', ['--tag' => 'filament-actions-views']);
              $command->callSilent('vendor:publish', ['--tag' => 'filament-forms-translations']);
              $command->callSilent('vendor:publish', ['--tag' => 'filament-forms-views']);
              $command->callSilent('vendor:publish', ['--tag' => 'filament-infolists-translations']);
              $command->callSilent('vendor:publish', ['--tag' => 'filament-infolists-views']);
              $command->callSilent('vendor:publish', ['--tag' => 'filament-notifications-translations']);
              $command->callSilent('vendor:publish', ['--tag' => 'filament-notifications-views']);
              $command->callSilent('vendor:publish', ['--tag' => 'filament-panels-translations']);
              $command->callSilent('vendor:publish', ['--tag' => 'filament-panels-views']);
              $command->callSilent('vendor:publish', ['--tag' => 'filament-stubs']);
              $command->callSilent('vendor:publish', ['--tag' => 'filament-tables-translations']);
              $command->callSilent('vendor:publish', ['--tag' => 'filament-tables-views']);
              $command->callSilent('vendor:publish', ['--tag' => 'filament-translations']);
              $command->callSilent('vendor:publish', ['--tag' => 'filament-views']);
              $command->callSilent('vendor:publish', ['--tag' => 'filament-widgets-views']);

              // /** Activity log. */
              // $command->callSilent('vendor:publish', [
              //   '--provider'  => "Spatie\Activitylog\ActivitylogServiceProvider",
              //   '--tag'       => "activitylog-migrations"
              // ]);

              $command->callSilent('notifications:table'); // Notifications' database table.
            })
            ->publishConfigFile()
            ->publishMigrations()
            ->askToRunMigrations()
            ->endWith(function (InstallCommand $installCommand) {
                $installCommand->call('neon:admin');
                $installCommand->line('');
                $installCommand->info('You can view all docs at https://neon.elementary-interacctive.dev/docs');
                $installCommand->line('');
                $installCommand->info('Thank you very much for installing N30N packages!');
            });
          })
      ;
  }

  public function registeringPackage()
  {
    $this->mergeConfigFrom(__DIR__.'/../config/auth_guards.php', 'auth.guards');
    $this->mergeConfigFrom(__DIR__.'/../config/auth_passwords.php', 'auth.passwords');
    $this->mergeConfigFrom(__DIR__.'/../config/auth_providers.php', 'auth.providers');
  }
}



// class NeonAdminServiceProvider extends ServiceProvider
// {

//   /** Bootstrap any application services.
//    *
//    * @param \Illuminate\Contracts\Http\Kernel  $kernel
//    *
//    * @return void
//    */
//   public function boot(Kernel $kernel): void
//   {
//     $this->loadTranslationsFrom(app_path('lang'), 'neon');

//     if ($this->app->runningInConsole()) {

//       /** Export migrations.
//        */
//       if (!class_exists('CreateAdminsTable')) {
//         $this->publishes([
//           __DIR__ . '/../database/migrations/create_admins_table.php.stub'
//             => database_path('migrations/' . date('Y_m_d', time()) . '_000001_create_admins_table.php'),
//         ], 'neon-migrations');
//       }
//       if (!class_exists('ChangeUserIdToStringOnActionEventsTable')) {
//         $this->publishes([
//           __DIR__ . '/../database/migrations/change_user_id_to_string_on_action_events_table.php.stub'
//             => database_path('migrations/' . date('Y_m_d', time()) . '_000002_change_user_id_to_string_on_action_events_table.php'),
//         ], 'neon-migrations');
//       }
//       if (!class_exists('ChangeActionableIdToStringOnActionEventsTable')) {
//         $this->publishes([
//           __DIR__ . '/../database/migrations/change_actionable_id_to_string_on_action_events_table.php.stub'
//             => database_path('migrations/' . date('Y_m_d', time()) . '_000003_change_actionable_id_to_string_on_action_events_table.php'),
//         ], 'neon-migrations');
//       }
//       if (!class_exists('ChangeTargetIdToStringOnActionEventsTable')) {
//         $this->publishes([
//           __DIR__ . '/../database/migrations/change_target_id_to_string_on_action_events_table.php.stub'
//             => database_path('migrations/' . date('Y_m_d', time()) . '_000004_change_target_id_to_string_on_action_events_table.php'),
//         ], 'neon-migrations');
//       }
//       if (!class_exists('ChangeModelIdToStringOnActionEventsTable')) {
//         $this->publishes([
//           __DIR__ . '/../database/migrations/change_model_id_to_string_on_action_events_table.php.stub'
//             => database_path('migrations/' . date('Y_m_d', time()) . '_000004_change_model_id_to_string_on_action_events_table.php'),
//         ], 'neon-migrations');
//       }

//       $this->publishes([
//         __DIR__.'/../config/nova.php'                => config_path('nova.php'),
//       ], 'neon-configs');

//       $this->publishes([
//         // __DIR__.'/Nova/Resource.php'                 => app_path('Nova/Resource.php'), //- The root resource itself.

//         // __DIR__.'/Nova/Admin.php'                    => app_path('Nova/Admin.php'),
//         // __DIR__.'/Nova/Link.php'                     => app_path('Nova/Link.php'),
//         // __DIR__.'/Nova/Menu.php'                     => app_path('Nova/Menu.php'),
//         // __DIR__.'/Nova/MenuItem.php'                 => app_path('Nova/MenuItem.php'),
//         // __DIR__.'/Nova/Site.php'                     => app_path('Nova/Site.php'),

//         __DIR__.'/Policies/AdminPolicy.php.stub'     => app_path('Policies/AdminPolicy.php'),
//         __DIR__.'/Policies/MenuPolicy.php.stub'      => app_path('Policies/MenuPolicy.php'),
//         __DIR__.'/Policies/LinkPolicy.php.stub'      => app_path('Policies/LinkPolicy.php'),
//         __DIR__.'/Policies/SitePolicy.php.stub'      => app_path('Policies/SitePolicy.php'),

//         __DIR__ . '/../resources/views/vendor'       => resource_path('views/vendor'),

//         /** Deploy langugage files. */
//         __DIR__ . '/../lang'                         => app_path('lang'),
//       ], 'neon-admin');

//       // $this->publishes([
//       //   __DIR__.'/Providers/NovaServiceProvider.php' => app_path('Providers/NovaServiceProvider.php')
//       // ], 'neon-admin-nova');
//     }
//   }

//   public function register()
//   {
//     $this->mergeConfigFrom(__DIR__.'/../config/auth_guards.php', 'auth.guards');
//     $this->mergeConfigFrom(__DIR__.'/../config/auth_passwords.php', 'auth.passwords');
//     $this->mergeConfigFrom(__DIR__.'/../config/auth_providers.php', 'auth.providers');
//   }
// }
