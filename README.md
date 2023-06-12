# neon-admin
 Neon Admin UI package develop.

## Install
```
php artisan vendor:publish --provider=\"Neon\\Admin\\NeonAdminServiceProvider\"
```

### Add Policies

In `app\Providers\AuthServiceProvider.php` you should add policies:
```
protected $policies = [
    \Neon\Admin\Models\Admin::class     => \App\Policies\AdminPolicy::class,
    \Neon\Site\Models\Site::class       => \App\Policies\SitePolicy::class,
    \Neon\Models\Menu::class            => \App\Policies\MenuPolicy::class,
    \Neon\Models\Link::class            => \App\Policies\LinkPolicy::class,
];
```