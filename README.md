# disambiguatable
[![disambiguatable](https://circleci.com/gh/adoxography/disambiguatable.svg?style=shield)](https://app.circleci.com/pipelines/github/adoxography/disambiguatable)

Distinguish between Laravel models with identical sets of columns

## Installation
1. Within a Laravel project, update your `composer.json` to include this repository

```json
{
  "repositories": [
    {
      "type": "vcs",
      "url": "https://github.com/adoxography/disambiguatable"
    }
  ]
}
```

2. Install the package through composer:

```bash
composer require adoxography/disambiguatable
```

## Usage
Just `use \Adoxography\Disambiguatable\Disambiguatable` in a subclass of `\Illuminate\Database\Eloquent\Model`,
and define an array of `$disambiguatableFields`.

```php
<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Adoxography\Disambiguatable\Disambiguatable;

class Foo extends Model
{
    use Disambiguatable;
    
    protected $disambiguatableFields = ['foo', 'bar'];
    protected $alwaysDisambiguate = true;  // optional
    
    // ...
}
```

A model's disambiguator can be accessed by calling `disambiguator` on it. If there are duplicates of the model
(only considering its `$disambiguatableFields`), an integer (beginning at 0) will be returned to distinguish the
model from its duplicates. If there are no duplicates, `disambiguator` will either return `null` (if
`$alwaysDisambiguate` is unset or falsey) or `0`.
```php
$model = Foo::first();
echo($model->disambiguator);
```

## License
See [LICENSE](/LICENSE).
