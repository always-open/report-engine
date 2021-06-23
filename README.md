# General reporting engine for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/bluefyn-international/report-engine.svg?style=flat-square)](https://packagist.org/packages/bluefyn-international/report-engine)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/bluefyn-international/report-engine/run-tests?label=tests)](https://github.com/bluefyn-international/report-engine/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/bluefyn-international/report-engine/Check%20&%20fix%20styling?label=code%20style)](https://github.com/bluefyn-international/report-engine/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/bluefyn-international/report-engine.svg?style=flat-square)](https://packagist.org/packages/bluefyn-international/report-engine)

General reporting engine for Laravel

## Installation

You can install the package via composer:

```bash
composer require bluefyn-international/report-engine
```

## Usage

### Create a report

Create a report that extends the ReportBase. Within this class you will define the query to fetch the data as well as 
the columns which will be output.

```php
<?php

namespace App\Reports\User;

use App\Models\User;
use BluefynInternational\ReportEngine\BaseFeatures\Data\Types\Text;
use BluefynInternational\ReportEngine\ReportBase;
use Illuminate\Database\Query\Builder;

class UserReport extends ReportBase
{
    protected $autoloadInitialData = true;

    /**
     * @return string
     */
    public function title(): string
    {
        return 'User Maintenance';
    }

    /**
     * @return string
     */
    public function description(): string
    {
        return 'List of all users within the system';
    }

    /**
     * @return Builder
     */
    public function baseQuery(): Builder
    {
        return User::toBase()
            ->select([
                'id',
                'email',
                'name',
            ]);
    }

    /**
     * @return array
     */
    public function availableColumns(): array
    {
        return [
            'name' => [
                'label'      => 'Name',
                'filterable' => true,
                'type'       => new Text(),
            ],
            'email' => [
                'label'      => 'Email',
                'filterable' => true,
                'type'       => new Text(),
            ],
        ];
    }
}

```

### Create a controller

Create a controller to output the report
```php
<?php

namespace App\Http\Controllers;

use App\Reports\User\UserReport;

class UserController extends Controller
{
    /**
     * @return UserReport
     */
    public function index() : UserReport
    {
        return app(UserReport::class);
    }
}
```

### Create a route

When creating a route ensure you include `multiformat` as this will handle things like `.sql` and `.json` endpoint calls.

```php
<?php

use App\Http\Controllers\UserController;

Route::get('users', [UserController::class, 'index'])
    ->multiformat();
```

#### Routes

Multiformat adds handling multiple formats to the url which can give the following output building upon the above 
examples.

This will output an HTML page that will contain a tabulator table and make ajax requests to get the data needed.
```
{{ base_url }}/users
```

This will output a JSON payload of the data
```
{{ base_url }}/users.json
```

This will output the entire SQL query, useful for debugging
```
{{ base_url }}/users.sql
```

This will return the output of the explain command for the query, useful for debugging
```
{{ base_url }}/users.explain
```

## Filters
Here are the possible filters for the default types. To build a filter follow this format:
```javascript
let filterParams = new URLSearchParams();
let filterName = 'name'
let action = 'equals'
let value = 'bob'
filterParams.append('filters['+filterName+']['+action+']', value)
```

### DateTime
- does_not_equal
- equals
- greater_than
- greater_than_or_equal
- less_than
- less_than_or_equal

### Decimal
- does_not_equal
- equals
- greater_than
- greater_than_or_equal
- less_than
- less_than_or_equal

### Dollar
- does_not_equal
- equals
- greater_than
- greater_than_or_equal
- less_than
- less_than_or_equal

### Enum
- equals

### Html
- contains
- does_not_contain

### Integer
- does_not_equal
- equals
- greater_than
- greater_than_or_equal
- less_than
- less_than_or_equal

### NullableDecimal
- does_not_equal
- equals
- greater_than
- greater_than_or_equal
- is_empty
- is_not_empty
- less_than
- less_than_or_equal

### NullableInteger
- does_not_equal
- equals
- greater_than
- greater_than_or_equal
- is_empty
- is_not_empty
- less_than
- less_than_or_equal

### Number
- does_not_equal
- equals
- greater_than
- greater_than_or_equal
- less_than
- less_than_or_equal

### NumericHtml
- does_not_equal
- equals
- greater_than
- greater_than_or_equal
- less_than
- less_than_or_equal

### Percentage
- does_not_equal
- equals
- greater_than
- greater_than_or_equal
- less_than
- less_than_or_equal

### Text
- contains
- does_not_contain
- does_not_equal
- equals

### Url
- contains
- does_not_contain

### YesNo
- is_true
- is_false

### YesNoShort
- is_true
- is_false


## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [BluefynInternational](https://github.com/bluefyn-international)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
