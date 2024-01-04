<?php

return [
      "navigation" => [
        "home"          => "Home",
        "settings"      => "Settings",
        "web"           => "Website"
      ],
      "models" => [
        "admin"         => "Administrator",
        "admins"        => "Administartors",
        "attribute"     => "Attribute",
        "attributes"    => "Attributes",
        "site"          => "Site",
        "sites"         => "Sites"
      ],
      "resources" => [
        "admins"        => "Administrators",
        "variables"     => "Variables",
        "sites"         => [
          "title"         => "Sites",
          "table"         => [

          ],
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
        ]
      ]
    ];