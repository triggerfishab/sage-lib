# [Sage](https://roots.io/sage/)
[![Packagist](https://img.shields.io/packagist/vpre/roots/sage-lib.svg?style=flat-square)](https://packagist.org/packages/roots/sage-lib)
[![Build Status](https://img.shields.io/travis/roots/sage-lib.svg?style=flat-square)](https://travis-ci.org/roots/sage-lib)

Sage is a WordPress starter theme with a modern development workflow.

Updated to work in PHP 8.0

Sage Repo: [https://github.com/roots/sage](https://github.com/roots/sage)

Obs! DO NOT REMOVE feature branch feature/illuminate-12

### Updating an existing Sage 9 theme for PHP 8.4+

Older Illuminate versions may show “Implicitly marking parameter as
nullable is deprecated” warnings on PHP 8.4 and newer.

In the theme's `composer.json`:

- Change `roots/sage-lib` to `"dev-feature/illuminate-12"`.
- Keep the repository pointing to `https://github.com/triggerfishab/sage-lib`.
- Remove `roots/sage-installer` from `require-dev` and its repository entry.
- Remove the `post-create-project-cmd` hook that calls the Sage installer.

`sage-installer` is only needed when creating a new theme. Its Illuminate
8.x requirements prevent this upgrade to Illuminate 12.

Then run from the theme directory:

    composer update roots/sage-lib roots/sage-installer "illuminate/*" -W


This fixes the older Illuminate warnings; other plugins or dependencies
may still produce their own PHP 8.4 deprecation warnings.
