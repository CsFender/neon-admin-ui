<?php

return [
  "actions"    => [
    "acivate"       => [
      "label"         => "Activate selected items"
    ],
    "inacivate"     => [
      "label"         => "Inactivate selected items"
    ],
  ],
  "navigation" => [
    "home"          => "Home",
    "settings"      => "Settings",
    "web"           => "Website",
    "site"          => "Domains",
    "menu"          => "Menus",
    "news"          => "News"
  ],
  "models" => [
    "admin"         => "Administrator",
    "admins"        => "Administartors",
    "attribute"     => "Attribute",
    "attributes"    => "Attributes",
    "site"          => "Site",
    "sites"         => "Sites",
    "menu"          => "Menu",
    "menus"         => "Menus",
    "news_item"     => "News Post",
    "news"          => "News",
  ],
  "resources" => [
    "admins"        => "Administrators",
    "attributables"  => [
      "title"         => "Variables",
      "form"          => [
        "fieldset"      => [
          "name"          => 'Name',
        ],
        "fields"        => [
          "class"         => [
            "label"         => "Resource",
          ],
          "name"          => [
            "label"         => "Name"
          ],
          "slug"          => [
            "label"         => "Identifier"
          ],
          "cast_as"       => [
            "label"         => "Cast as",
            "help"          => "Technical parameter, how to store the variable in database.",
            "options"       => [
              "string"        => "String",
              "integer"       => "Integer",
              "float"         => "Float",
              "boolean"       => "Boolean (true/false)",
              "array"         => "Array",
            ]
          ],
          "field"         => [
            "label"         => "Input field",
            "options"       => [
              "text"         => "Text input",
            ],
          ],
          "rules"         => [
            "label"         => "Rules",
            "options"       => [
              "activeUrl"     => "URL",
              "alpha"         => "Only A-Z letters",
              "alphaDash"     => "A-Z letters and - _",
              "alphaNum"      => "Numbers and letters",
              "required"      => "Required",
              "ascii"         => "ASCII",
            ],
          ],
          "params"          => [
            "label"         => "Parameters"
          ],
          "slug"          => [
            "label"         => "Identifier"
          ]
        ],
      ],
    ],
    "variables"     => "Variables",
    "sites"         => [
      "title"         => "Sites",
      "table"         => [],
      "form"          => [
        "tabs"          => [
          "basic"         => "Basic",
          "attributables" => "Advanced"
        ],
        "fields"        => [
          "locale"        => [
            "label"         => "Localization"
          ],
          "domains"       => [
            "label"         => "Domains",
            "placeholder"   => "Add a domain without http or https.",
            "help"          => "Any kind of items under this site will be accessible only on these domains.",
            "new"           => "Add a new domain"
          ],
          "prefixes"       => [
            "label"         => "Prefixes",
            "placeholder"   => "Add a prefix.",
            "help"          => "Any kind of items under this site will be accessible only on these prefixes.",
            "new"           => "Set a new prefix"
          ],
          "is_default"    => [
            "label"         => "Is default?"
          ],
          "title"         => [
            "label"         => "Name"
          ],
          "slug"          => [
            "label"         => "Identifier"
          ]
        ],
        "fieldset"        => [
          "name"            => "Name"
        ]
      ]
    ],
    "menu"         => [
      "title"         => "Menu",
      "table"         => [],
      "form"          => [
        "tabs"          => [
          "basic"         => "Basic",
          "attributables" => "Advanced"
        ],
        "fieldset"      => [
          "name"          => 'Name',
        ],
        "fields"        => [
          "title"         => [
            "label"         => "Name"
          ],
          "slug"          => [
            "label"         => "Identifier"
          ],
          "site"          => [
            "label"         => "Website"
          ],
          "status"          => [
            "label"         => "Status"
          ]
        ]
      ]
    ],
    "news"         => [
      "title"         => "News",
      "table"         => [
        'tabs'          => [
          'all'           => 'All',
          'new'           => 'New',
          'live'          => 'Live',
          'pinned'        => 'Pinned',
          'archive'       => 'Archive',
        ]
      ],
      "form"          => [
        "tabs"          => [
          "basic"         => "Basic",
          "attributables" => "Advanced"
        ],
        "filters"       => [
          "is_active"     => "Is Active?",
          "is_published"  => "Is Published?"
        ],
        "fieldset"      => [
          "publishing"     => "Publishing"
        ],
        "fields"        => [
          "title"         => [
            "label"         => "Title"
          ],
          "slug"          => [
            "label"         => "Link"
          ],
          "header_image"  => [
            "label"         => "Header Image"
          ],
          "lead"          => [
            "label"         => "Lead"
          ],
          "content"       => [
            "label"         => "Content"
          ],
          "content_image" => [
            "label"         => "Content Images"
          ],
          "site"          => [
            "label"         => "Website"
          ],
          "tags"          => [
            "label"         => "Tags"
          ],
          "pinned"        => [
            "label"         => "Pinned"
          ],
          "status"        => [
            "label"         => "Status"
          ],
          "published_at"  => [
            "label"         => "Published at"
          ],
          "expired_at"    => [
            "label"         => "Expired at"
          ]
        ]
      ]
    ]
  ]
];
