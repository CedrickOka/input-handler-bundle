# Getting Started With OkaInputHandlerBundle

This bundle help the user input high quality data into your web services REST.

## Prerequisites

The OkaInputHandlerBundle has the following requirements:

 - PHP 7.2+
 - Symfony 4.4+

## Installation

Installation is a quick (I promise!) 3 step process:

1. Download OkaInputHandlerBundle
2. Register the Bundle
3. Configure the Bundle

### Step 1: Download the Bundle

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```bash
$ composer require coka/input-handler-bundle
```

This command requires you to have Composer installed globally, as explained
in the [installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

### Step 2: Register the Bundle

Then, register the bundle by adding it to the list of registered bundles
in the `config/bundles.php` file of your project (Flex did it automatically):

```php
return [
    //...
    Oka\InputHandlerBundle\OkaInputHandlerBundle::class => ['all' => true],
]
```

### Step 3: Configure the Bundle

Add the following configuration to your `config/packages/oka_input_handler.yaml`.

``` yaml
# config/packages/oka_input_handler.yaml
oka_input_handler:
    exception:
        enabled: true
    response:
	     headers:
	         server_time: true
	     error_builder_class: 'Oka\InputHandlerBundle\Util\ErrorResponseBuilder'
```

## How use this?

Now that the bundle is installed
