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

You can publish and run the migrations with:

```bash
php artisan vendor:publish --provider="BluefynInternational\ReportEngine\ReportEngineServiceProvider" --tag="report-engine-migrations"
php artisan migrate
```

## Usage

```php
$report-engine = new BluefynInternational\ReportEngine();
echo $report-engine->echoPhrase('Hello, Spatie!');
```

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
