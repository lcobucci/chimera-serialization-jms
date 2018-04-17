# Chimera - serialization-jms

[![Total Downloads](https://img.shields.io/packagist/dt/lcobucci/chimera-serialization-jms.svg?style=flat-square)](https://packagist.org/packages/lcobucci/chimera-serialization-jms)
[![Latest Stable Version](https://img.shields.io/packagist/v/lcobucci/chimera-serialization-jms.svg?style=flat-square)](https://packagist.org/packages/lcobucci/chimera-serialization-jms)
[![Unstable Version](https://img.shields.io/packagist/vpre/lcobucci/chimera-serialization-jms.svg?style=flat-square)](https://packagist.org/packages/lcobucci/chimera-serialization-jms)

![Branch master](https://img.shields.io/badge/branch-master-brightgreen.svg?style=flat-square)
[![Build Status](https://img.shields.io/travis/lcobucci/chimera-serialization-jms/master.svg?style=flat-square)](http://travis-ci.org/#!/lcobucci/chimera-serialization-jms)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/lcobucci/chimera-serialization-jms/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/lcobucci/chimera-serialization-jms/?branch=master)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/lcobucci/chimera-serialization-jms/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/lcobucci/chimera-serialization-jms/?branch=master)

> The term Chimera (_/kɪˈmɪərə/_ or _/kaɪˈmɪərə/_) has come to describe any
mythical or fictional animal with parts taken from various animals, or to
describe anything composed of very disparate parts, or perceived as wildly
imaginative, implausible, or dazzling.

There are many many amazing libraries in the PHP community and with the creation
and adoption of the PSRs we don't necessarily need to rely on full stack
frameworks to create a complex and well designed software. Choosing which
components to use and plugging them together can sometimes be a little
challenging.

The goal of this set of packages is to make it easier to do that (without
compromising the quality), allowing you to focus on the behaviour of your
software.

This package provides an adapter for [`jms/serializer`](https://github.com/schmittjoh/serializer),
allowing us to use it as a `MessageCreator` - which is responsible for converting
the user input into a message to be handled.

## Installation

Package is available on [Packagist](http://packagist.org/packages/lcobucci/chimera-serialization-jms),
you can install it using [Composer](http://getcomposer.org).

```shell
composer require lcobucci/chimera-serialization-jms
```

### PHP Configuration

In order to make sure that we're dealing with the correct data, we're using `assert()`,
which is a very interesting feature in PHP but not often used. The nice thing
about `assert()` is that we can (and should) disable it in production mode so
that we don't have useless statements.

So, for production mode, we recommend you to set `zend.assertions` to `-1` in your `php.ini`.
For development you should leave `zend.assertions` as `1` and set `assert.exception` to `1`, which
will make PHP throw an [`AssertionError`](https://secure.php.net/manual/en/class.assertionerror.php)
when things go wrong.

Check the documentation for more information: https://secure.php.net/manual/en/function.assert.php

## Usage

It's quite simple to use the `ArrayTransformer` as a `MessageCreator`:

```php
<?php
declare(strict_types=1);

namespace MyApp;

use JMS\Serializer\EventDispatcher\EventDispatcher;
use JMS\Serializer\EventDispatcher\Events;
use JMS\Serializer\SerializerBuilder;
use Lcobucci\Chimera\ExecuteQuery;
use Lcobucci\Chimera\MessageCreator\JmsSerializer\ArrayTransformer;
use Lcobucci\Chimera\MessageCreator\JmsSerializer\InputDataInjector;

// First we need to have a JMS serializer instance with the event listener set

$addListeners = function (EventDispatcher $dispatcher): void {
    $dispatcher->addListener(Events::PRE_DESERIALIZE, [new InputDataInjector(), 'injectData']);
};

$serializer = SerializerBuilder::create()->configureListeners($addListeners)
                                         ->build();

// Then instantiate the `ArrayTransformer`
$messageCreator = new ArrayTransformer($serializer);

// And finally use it on the actions
$action = new ExecuteQuery($queryBus, $messageCreator, MyQuery::class); // considering that $queryBus is a valid instance of `ServiceBus`
$result = $action->fetch($input); // considering that $input is a valid instance of `Input`

var_dump($result);
```

## License

MIT, see [LICENSE file](https://github.com/lcobucci/chimera-foundation/blob/master/LICENSE).
