<?php

use Filament\FontProviders\GoogleFontProvider;
use Filament\Support\Colors\Color;

return [

  /** Changing the Filament's path on what the admin panel is accessible.
   * 
   * @see https://filamentphp.com/docs/3.x/panels/configuration#changing-the-path
   */
  'path'  => env('NEON_ADMIN_PATH', 'admin'),

  /** Set up folder for admin resources. As you generate resources, please take
   * attention, Neon Admin uses 'App\Admin\Resources' namespace for your resources,
   * so pleasespecify namespace when generates resources.
   * 
   * May you have different resources in different folders, so you can put all 
   * path here, into an array.
   * 
   * @see https://filamentphp.com/docs/3.x/panels/resources/getting-started#specifiying-a-custom-model-namespace
   */
  'resources' => ['Admin/Resources'],

  /** By default, Filament will respond to requests from all domains. If you'd
   * like to scope it to a specific domain, you can use the domain() method,
   * similar to Route::domain() in Laravel.
   * 
   * @see https://filamentphp.com/docs/3.x/panels/configuration#setting-a-domain
   */
  'domain'  => null,

  /** Neon Admin uses separated guard for authentication and authorization.
   *
   */
  'guard' => 'admin',

  /** Admin colors.
   * 
   * @see https://filamentphp.com/docs/3.x/panels/themes#changing-the-colors
   */
  'colors'  => [
    'primary'   => '#3b0764',
    'danger'    => '#991b1b',
    'gray'      => '#d8b4fe',
    'info'      => '#6366f1',
    'success'   => '#5b21b6',
    'warning'   => '#c2410c',
  ],

  /** Set font. You can set font-family, and also the provider, if it's needed.
   * 
   * 'font'  => [
   *   'font-family' => 'Inter',
   *   'provider'    => GoogleFontProvider::class
   * ]
   * @see https://filamentphp.com/docs/3.x/panels/themes#changing-the-font
   */
  'font'  => null,
];