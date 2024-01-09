<?php

return [
  "navigation" => [
    "home"          => "Home",
    "settings"      => "Settings",
    "web"           => "Website",
    "site"          => "Domains",
    "menu"          => "Menus"
  ],
  "models" => [
    "admin"         => "Administrator",
    "admins"        => "Administartors",
    "attribute"     => "Attribute",
    "attributes"    => "Attributes",
    "site"          => "Site",
    "sites"         => "Sites",
    "menu"          => "Menu",
    "menus"         => "Menus"
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
          "cast_as"         => [
            "label"         => "Cast as",
            "options"       => [
              "string"        => "String",
              "integer"       => "Integer",
              "float"         => "Float",
              "boolean"       => "Boolean (true/false)",
              "array"         => "Array",
            ]
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
          ]
        ]
      ]
    ]
  ]
];
