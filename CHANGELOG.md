# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.2.1] - 2024-05-17

- fix: display git branch of the current symlinked plugin

## [1.2.0] - 2023-09-15

- Your plugin's home URL path will now display as a tilde for improved readability.
- Added a quick view of symlinked plugins and the current branch they are on in the WordPress Admin Toolbar.
- Added support for the network plugins screen in a WordPress multisite.
- Registered new field, `symlinkedPlugins`, with WPGraphQL

```graphql
query NewQuery {
  symlinkedPlugins {
    name
    version
    currentBranch
    symlinkPath
  }
}
```

```json
{
  "data": {
    "symlinkedPlugins": [
      {
        "name": "Advanced Custom Fields PRO",
        "version": "6.2.0",
        "currentBranch": "develop",
        "symlinkPath": "~/Code/acf/src/advanced-custom-fields-pro"
      },
      {
        "name": "Faust.js™",
        "version": "1.0.3",
        "currentBranch": "MERL-1205-refactor-custom-preview",
        "symlinkPath": "~/Code/faustjs/plugins/faustwp"
      },
      {
        "name": "Symlinked Plugin Branch",
        "version": "1.1.0",
        "currentBranch": "refactor",
        "symlinkPath": "~/Code/symlinked-plugin-branch"
      },
      {
        "name": "WPGraphQL",
        "version": "1.16.0",
        "currentBranch": "develop",
        "symlinkPath": "~/Code/wp-graphql"
      }
    ]
  }
}
```

## [1.1.0] - 2023-01-26

### Added

- Symlinked Branch column now shows the path to the symlinked plugin.
- Fresh styles to help with visually scanning the plugins screen while developing.
