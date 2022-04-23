OkaInputHandlerBundle
=====================

This bundle help the user input high quality data into your web services REST.

[![Latest Stable Version](https://poser.pugx.org/coka/input-handler-bundle/v/stable)](https://packagist.org/packages/coka/input-handler-bundle)
[![Total Downloads](https://poser.pugx.org/coka/input-handler-bundle/downloads)](https://packagist.org/packages/coka/input-handler-bundle)
[![Latest Unstable Version](https://poser.pugx.org/coka/input-handler-bundle/v/unstable)](https://packagist.org/packages/coka/input-handler-bundle)
[![License](https://poser.pugx.org/coka/input-handler-bundle/license)](https://packagist.org/packages/coka/input-handler-bundle)
[![Monthly Downloads](https://poser.pugx.org/coka/input-handler-bundle/d/monthly)](https://packagist.org/packages/coka/input-handler-bundle)
[![Daily Downloads](https://poser.pugx.org/coka/input-handler-bundle/d/daily)](https://packagist.org/packages/coka/input-handler-bundle)
[![Travis CI](https://travis-ci.com/CedrickOka/input-handler-bundle.svg?branch=master)](https://travis-ci.org/CedrickOka/input-handler-bundle)

Installation
============

Make sure Composer is installed globally, as explained in the
[installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

Applications that use Symfony Flex
----------------------------------

Open a command console, enter your project directory and execute:

```console
$ composer require coka/input-handler-bundle
```

Applications that don't use Symfony Flex
----------------------------------------

### Step 1: Download the Bundle

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```console
$ composer require coka/input-handler-bundle
```

### Step 2: Enable the Bundle

Then, enable the bundle by adding it to the list of registered bundles
in the `config/bundles.php` file of your project:

```php
// config/bundles.php

return [
    // ...
    Oka\\InputHandlerBundle\OkaInputHandlerBundle::class => ['all' => true],
];
```
