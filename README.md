# HashidsBundle

Integrates [hashids/hashids](https://github.com/ivanakimov/hashids.php) in a Symfony project.

## Installation using composer

These commands requires you to have Composer installed globally.  
Open a command console, enter your project directory and execute the following 
commands to download the latest stable version of this bundle:

### Using Symfony Flex

```
    composer config extra.symfony.allow-contrib true
    composer req vipinbose/hashids-bundle
```

### Using Symfony Framework only

```
    composer require vipinbose/hashids-bundle
```

If this has not been done automatically, enable the bundle by adding the 
following line in the `config/bundles.php` file of your project:

```php
<?php

return [
    …,
    Vipinbose\HashidsBundle\VipinboseHashidsBundle::class => ['all' => true],
];
```

## Configuration

The configuration (`config/packages/vipinbose_hashids.yaml`) looks as follows :

```yaml
vipinbose_hashids:

    # if set, the hashids will differ from everyone else's
    salt:            ""

    # if set, will generate minimum length for the id
    # 0 — meaning hashes will be the shortest possible length
    min_hash_length: 0

    # if set, will use only characters of alphabet string
    alphabet:        "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890"

    # if set to true, it tries to convert all arguments passed to the controller
    auto_convert:    true
```

## Usage

```php

use Vipinbose\HashidsBundle\Interfaces\HashidsServiceInterface;

public function __construct(
        private HashidsServiceInterface $hasher,
    ) {
    }
```

Next it's the same things of [official documentation](https://hashids.org/php/).

## Hashids Converter

Converter Name: `hashids.converter`

The hashids converter attempts to convert any attribute set in the route into 
an integer parameter.

You should use `hashid`:

```php
/**
 * @Route("/users/{hashid}")
 */
public function getAction(User $user)
{
}
```
## Using auto_convert

`auto_convert` tries to convert all arguments in controller.

```yaml
vipinbose_hashids:
  auto_convert: true
```
## Twig Extension
### Usage

```twig
{{ path('users.show', {'hashid': user.id | hashids_encode }) }}
{{ app.request.query.get('hashid') | hashids_decode }}
```
