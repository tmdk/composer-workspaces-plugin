Workspaces Plugin for Composer
==============================

This composer plugin enables you to work with a monorepo containing multiple composer packages in a way similar to [Yarn Workspaces](https://yarnpkg.com/lang/en/docs/workspaces/).

Requirements
------------
- PHP 7.1 or above
- Composer 2

### Composer v1 support

Starting from version 2.x of this plugin, composer v1 is no longer supported. If you do need
composer 1 support, please use the 1.x version of the plugin. Please note that version 1 will not
receive any updates except for security fixes.

Installation
------------

Add `tmdk/composer-workspaces-plugin` to your project:

```sh
composer require --dev tmdk/composer-workspaces-plugin=^2.0.0
```

Usage
-----

The `workspaces:init` command will prompt you to configure your workspace paths:

```sh
composer workspaces:init
```

The plugin scans all configured paths for composer packages. To bootstrap all found workspace packages, run `workspaces:bootstrap`. You can rerun this command any time you add another workspace package.

```sh
composer workspaces:bootstrap
```

After bootstrapping your packages, you can run composer commands in the context of a specific package with the `workspace` command.

For example, if you have two workspace packages, `acme/foo` and `acme/bar`, and you want to add `acme/bar` as a dependency of `acme/foo`:

```sh
composer workspace acme/foo require acme/bar
```

Use `workspaces:list` to list all available workspace packages:

```sh
composer workspaces:list
```

Todo
----

* Add tests.
* Add feature to centralize dependency version management.
