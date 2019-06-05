# PHPStan extension for Zend Framework

[![Build Status](https://travis-ci.org/Slamdunk/phpstan-zend-framework.svg?branch=master)](https://travis-ci.org/Slamdunk/phpstan-zend-framework)
[![Code Coverage](https://scrutinizer-ci.com/g/Slamdunk/phpstan-zend-framework/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/Slamdunk/phpstan-zend-framework/?branch=master)
[![Packagist](https://img.shields.io/packagist/v/slam/phpstan-zend-framework.svg)](https://packagist.org/packages/slam/phpstan-zend-framework)
[![Total Downloads](https://img.shields.io/packagist/dt/slam/phpstan-zend-framework.svg)](https://packagist.org/packages/Slamdunk/phpstan-zend-framework)

* [PHPStan](https://github.com/phpstan/phpstan)
* [Zend Framework](https://framework.zend.com/)

This extension provides following features:

Adds support to PHPStan to correctly handle controller plugins that are called
using magic `__call()` in `\Zend\Mvc\Controller\AbstractController` and
children.

## Installation

To use this extension, require it in [Composer](https://getcomposer.org/):

```
composer require --dev slam/phpstan-zend-framework
```

If you also install [phpstan/extension-installer](https://github.com/phpstan/extension-installer) then you're all set!

<details>
    <summary>Manual installation</summary>

If you don't want to use `phpstan/extension-installer`, include extension.neon in your project's PHPStan config:

```
includes:
    - vendor/slam/phpstan-zend-framework/extension.neon
```

</details>

## Configuration

This library already recognize built-in services and plugins.

You can opt in for more advanced analysis by providing the service manager from your own application:

```neon
parameters:
    zendframework:
       serviceManagerLoader: tests/service-manager.php
```

For example, `tests/service-manager.php` would look something like this:

```php
$app = \Zend\Mvc\Application::init($config);
return $app->getServiceManager();
```
