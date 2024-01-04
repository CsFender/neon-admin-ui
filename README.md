# NEON &mdash; Site

NEON Administer interface package.

## Requirements
* `"neon/model-uuid": "^1.0"`
* `"neon/site": "^3.0"`

## Install


```bash
php artisan vendor:publish --provider=\"Neon\\Admin\\NeonAdminServiceProvider\"
```

...and then call the installer:

```bash
php artisan neon-admin:install
```

This step will silently add Filament support to your project, and then publish necessary config file, and publish and run all migrations.

## Create user

To be able to run the project, first you must create a user:

```bash
php artisan neon-admin:user
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