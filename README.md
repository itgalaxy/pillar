# Pillar

[![Packagist](https://img.shields.io/packagist/v/itgalaxy/pillar.svg?style=flat-square)](https://packagist.org/packages/itgalaxy/pillar)
[![Build Status](https://img.shields.io/travis/itgalaxy/pillar.svg?style=flat-square)](https://travis-ci.org/itgalaxy/pillar)
[![Dependency Status](https://www.versioneye.com/user/projects/58a1fab7940b23003d2b0128/badge.svg?style=flat-square)](https://www.versioneye.com/user/projects/58a1fab7940b23003d2b0128)

Collection WordPress mini plugins (theme features) to apply theme-agnostic modifications.

## Requirements

<table>
  <thead>
    <tr>
      <th>Prerequisite</th>
      <th>How to check</th>
      <th>How to install</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>PHP &gt;= 5.6.x</td>
      <td><code>php -v</code></td>
      <td>
        <a href="http://php.net/manual/en/install.php">php.net</a>
      </td>
    </tr>
  </tbody>
</table>

## Installation

You can install this plugin via the command-line or the WordPress admin panel.

### Via Command-line

If you're [using Composer to manage WordPress](https://roots.io/using-composer-with-wordpress/), 
add `Pillar` to your project's dependencies.

```sh
composer require itgalaxy/pillar
```

Then activate the plugin via [wp-cli](http://wp-cli.org/commands/plugin/activate/).

```sh
wp plugin activate pillar
```

### Via WordPress Admin Panel

1. Download the [latest zip](https://github.com/itgalaxy/pillar/releases/latest) of this repo.
2. In your WordPress admin panel, navigate to `Plugins`->`Add New`
3. Click Upload Plugin
4. Upload the zip file that you downloaded.

## Usage

1. Activate this plugin
2. Open `function.php` file in your theme.
3. Add this code for enable mini plugin.

```php
add_action('after_setup_theme', function () {
    add_theme_support('pillar-head-clean-up'); // List of all features placed below
});
```

## Features

### What is Feature

1. **Widely applicable.** The features we distribute need to be of importance to a large number of developers. 
Individual preferences for uncommon patterns are not supported.

2. **Generic.** Features cannot be so specific that users will have trouble understanding when to use them. 

3. **Atomic.** Features must function completely on their own. 
Features are expressly forbidden from knowing about the state or presence of other features.
Area of responsibility should be only one (one feature - one area of responsibility).

4. **Unique.** No two features can produce the same things.

5. **No conflicts.** No feature must directly conflict with another feature.

6. **Theme-agnostic.** No styles, no scripts, no images and other stuff.

7. **Open source.** No commercial services, packages, libraries and files.

8. **Simple.** Only action and filters. 

- No register new post types, taxonomies, widgets and shortcodes. 
- No database schema modification.
- No public api for use in plugins, themes and etc. 
- No new markup elements on front-end (buttons, tables, lists and etc). 

### List

Comming soon...

## Contribution

Feel free to push your code if you agree with publishing under the MIT license.

## [Changelog](CHANGELOG.md)

## [License](LICENSE)
