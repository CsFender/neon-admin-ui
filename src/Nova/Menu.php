<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Http\Requests\NovaRequest;

/** Nova fields.
 * 
 */
use Laravel\Nova\Fields\{
    Badge,
    Boolean,
    Slug,
    Text,
};

class Menu extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \Neon\Models\Menu::class;

    /**
     * The visual style used for the table. Available options are 'tight' and 'default'.
     *
     * @var string
     */
    public static $tableStyle = 'tight';

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'title';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'title',
    ];

    public static function label()
    {
        return __('Menus');
    }

    public static function singularLabel()
    {
        return __('Menu');
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        $model = $this;

        $fields = [
            BelongsToMany::make(__('Oldal'), 'site', \App\Nova\Site::class)
                ->fields(function ($request, $relatedModel) {
                    return [
                        Text::make(__('Kapcsolat típusa'), 'dependence_type')
                            ->default(\Neon\Models\Menu::class),
                    ];
                }),
            Text::make('Menü', 'title')
                ->rules('required', 'max:255'),
            Slug::make('', 'slug')
                ->from('title')
                // ->slugifyOptions([
                //     'lang'  => 'hu'
                // ])
                ->hideFromIndex()
                ->hideFromDetail(),
                Text::make('Használata', function () use ($model) {
                    return "<x-neon-menu id=\"{$model->slug}\">\n\r
                                <x-slot:tools>\n\r
                                    ...\n\r
                                </x-slot>\n\r
                            </x-neon-menu>";
            })
                // ->asHtml()
                ->showOnDetail(),
            Boolean::make(__('Active'), 'status')
                ->trueValue(\Neon\Models\Statuses\BasicStatus::Active->value)
                ->falseValue(\Neon\Models\Statuses\BasicStatus::Inactive->value)
                ->hideFromIndex(),
            Badge::make(__('Status'))->map([
                    \Neon\Models\Statuses\BasicStatus::Inactive->value  => 'danger',
                    \Neon\Models\Statuses\BasicStatus::Active->value    => 'success',
                ])
                ->onlyOnIndex(),
            // Text::make('Látható linkek', function () use ($model) {
            //     return $model->links()->count();
            // })
            //     ->asHtml()
            //     ->hideFromDetail(),
            HasMany::make(__('Items'), 'items', \App\Nova\MenuItem::class)
        ];

        // /** Collect languages.
        //  * @var Illuminate\Database\Eloquent\Collection
        //  */
        // $languages = collect(config('laravellocalization.supportedLocales'));

        // if ($languages->count() > 1)
        // {
        //     $fields[] = Select::make('Lokalizáció', 'locale')
        //         ->options($languages->mapWithKeys(function($item, $key) {
        //             return [$key => \Str::ucfirst($item['native'])];
        //         }))
        //         ->help('A menürendszer csak ezen a nyelvű oldalon fog megjelenni. Különböző nyelvekhez azonos nevű menü is létrehozható, hogy ne kelljen nyelvenként külön kulcsot használni.')
        //         ->rules('required');
        // }

        return $fields;
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function cards(Request $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function lenses(Request $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function actions(Request $request)
    {
        return [];
    }

    /**
     * Build an "index" query for the given resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function indexQuery(NovaRequest $request, $query)
    {
        $next = parent::indexQuery($request, $query);

        $next->withoutGlobalScopes([
            \Neon\Models\Scopes\ActiveScope::class,
            \Neon\Site\Models\Scopes\SiteScope::class
        ]);

        // dd($next);
        return $next;
    }
}
