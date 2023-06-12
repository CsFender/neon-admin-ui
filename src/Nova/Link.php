<?php

namespace App\Nova;

class Link extends Resource
{
    // use Orderable;

    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \Neon\Models\Link::class;

    /** Hide from navigation.
     *
     * @var boolean
     */
    // public static $displayInNavigation = false;

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
    
    /** Set the order field.
     * 
     * @var string
     */
    public static $defaultOrderField = 'order';
    
    /** Show as many items as it could be.
     * 
     * @var integer
     */
    public static $perPageViaRelationship = 15;

    // public static function icon() 
    // {
    //     return view('nova::icon.svg-link', [
    //         'height'    => 20,
    //         'width'     => 20,
    //         'color'     => 'var(--sidebar-icon)',
    //         'class'     => 'sidebar-icon'
    //     ])->render();
    // }

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
            BelongsTo::make('Menü', 'menu', \App\Nova\Menu::class),
            Text::make('Link', 'title')
                // ->slug('slug')
                ->rules('required', 'max:255'),
            Slug::make('URI', 'slug')
                // ->slugifyOptions([
                //     'lang'  => 'hu'
                // ])
                ->from('title')
                ->hideFromIndex()
                ->hideFromDetail(),
            Text::make('', function() use ($model) {
                return "<a style=\"color: inherit;\" href=\"".url($model->href)."\" target=\"_blank\" title=\"{$model->href}\">".view('nova::icon.svg-link', [
                    'color'     => 'var(--colors-gray-400)'
                ])->render()."</a>";
            })
                ->asHtml()
                ->hideFromDetail(),
            // OrderField::make('Sorrend', 'order'),
            Select::make('Nyitás', 'target')
                ->options([
                    '_self'     => 'Saját ablakban',
                    '_blank'    => 'Új ablakba'
                ])
                ->hideFromIndex()
                ->hideFromDetail(),
            Heading::make('Megosztás'),
            Text::make('Megosztási cím', 'og_title')
                ->help('Csak akkor kell megadni, ha nem egyezik az oldal címével.')
                ->hideFromIndex(),
            Textarea::make('Leírás', 'og_description')
                ->hideFromIndex(),
            Image::make('Kép', 'og_image')
                ->hideFromIndex(),
            Heading::make('Elérhetőség'),
            Boolean::make('Elérhető', 'status')
                ->trueValue(\Neon\Models\Statuses\BasicStatus::Active->value)
                ->falseValue(\Neon\Models\Statuses\BasicStatus::Inactive->value)
                ->help('Kapcsoljuk be, ha azt akarjuk, hogy a link elérhető legyen.'),
            DateTime::make('Elérhetőség kezdete', 'published_at')
                ->help('Ha a link elérhetőre van állítva, akkor kötelező megadni. Itt állíthatjuk be hogy mikortól legyen elérhető.'),
            DateTime::make('Elérhetőség vége', 'expired_at')
                ->help('Nem kötelező kitölteni, de ezzel szabályozhatjuk, hogy meddig legyen elérhető a link.'),
            Heading::make('Haladó beállítások')
                ->hideFromDetail(),
            Text::make('URI', 'url')
                ->hideFromIndex()
                ->help('Automatikusan generált URI a menüstruktúra alapján.'),
            Select::make('Kérés', 'method')
                ->options([
                    'GET' => 'GET',
                    'POST' => 'POST',
                    'PUT' => 'PUT',
                    'PATCH' => 'PATCH',
                    'DELETE' => 'DELETE'
                ])
                ->hideFromIndex()
                ->hideFromDetail(),
            Text::make('Útvonal', 'route')
                ->help('A keretrendszerben előre definiált útvonal. Bővebb információ: <a href="https://laravel.com/docs/6.x/routing" target="_blank">https://laravel.com/docs/6.x/routing</a>')
                ->hideFromIndex()
                ->hideFromDetail(),
            KeyValue::make('Paraméterek', 'parameters')
                ->rules('json')
                ->hideFromDetail(),
            Text::make('Külső link', 'link')
                ->help('Külső hivatkozás, például: https://brightly.hu')
                ->hideFromIndex()
                ->hideFromDetail(),
            HasMany::make('Elemek', 'children', \App\Nova\Link::class),
            // MorphOne::make('Tartalom', 'content', \App\Nova\Content::class)
        ];

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
            'Brightly\Mango\Scopes\ActiveScope',
            'Brightly\Mango\Scopes\PublishedScope',
            'Brightly\Mango\Scopes\SiteScope',
        ]);

        /** Empty orders and the order value... */
        $next->getQuery()->orders = [];
        $next->orderBy(
            'status',
            'asc'
        );
        $next->orderBy(
            self::$defaultOrderField,
            'asc'
        );

        return $next;
    }
}
