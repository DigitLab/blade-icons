# Blade Icons

[![StyleCI](https://styleci.io/repos/64119512/shield?style=flat)](https://styleci.io/repos/64119512)
[![Build Status](https://travis-ci.org/DigitLab/blade-icons.svg)](https://travis-ci.org/DigitLab/blade-icons)
[![Total Downloads](https://poser.pugx.org/digitlab/blade-icons/downloads)](https://packagist.org/packages/digitlab/blade-icons)
[![Latest Stable Version](https://poser.pugx.org/digitlab/blade-icons/v/stable)](https://packagist.org/packages/digitlab/blade-icons)
[![License](https://poser.pugx.org/digitlab/blade-icons/license)](https://packagist.org/packages/digitlab/blade-icons)

An SVG icon injector for Laravel Blade.

## Installation

Install using composer:

```bash
composer require digitlab/blade-icons
```

Add the service provider in app/config/app.php:

```php
DigitLab\AdaptiveView\BladeIconsServiceProvider::class,
```

Create the cache path (defaults to /storage/framework/icons) and resource path (defaults to /resources/svgs) as 
specified in your config.

## Usage

You can insert an SVG resource using the blade syntax ```@icon('name')```.

To override svg attributes, add them in an array: ```@icon('name', ['width' => 300])```.

## CSS

It is suggested that you add the following css to style your icons:
```css
.icon {
  display: inline-block;
  vertical-align: text-top;
  fill: currentColor;
}
```

## License

Adaptive View is licensed under [The MIT License (MIT)](LICENSE).
