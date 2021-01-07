# Laravel Domain Oriented

This package builds a structure to domain-oriented APIs (not DDD, they are different things). With search filters, validations and clean code.

## Requirements
- PHP 7.2+, 8.0 (new version)
- Laravel 7.x, 8 (prefer-stable)

## Introduction
My need was simple: build structures in an organized and productive way. A structure that supports filters, validations and data caching (CQRS).  

Before proceeding, take a look at the final structure:
```bash
app
├── ...
├── Domain
│   └── Dummy
│       ├── DummyFilterService.php
│       ├── DummyPersistenceModel.php
│       ├── DummyPersistenceService.php
│       ├── DummyResource.php
│       ├── DummySearchModel.php
│       ├── DummySearchService.php
│       └── DummyValidateService.php
├── Http
│   ├── Controllers
│   │   ├── ...
│   │   └── DummyController.php
├── ...
database
├── factories
│   └── ...
│   └── DummyFactory.php
├── migrations
│   ├── ...
│   └── 2021_01_06_193044_create_dummies_table.php
└── seeders
    ├── DatabaseSeeder.php
    └── DummySeeder.php
```

You must be asking yourself:
1. Why not use Repository Pattern?  
   A. It is not possible to obtain a database abstraction more than what Eloquent offers. <sup>[1](https://adelf.tech/2019/useless-eloquent-repositories "Please, stop talking about Repository pattern with Eloquent") </sup>
2. What is the idea of PersistenceModel, and SearchModel?  
   A. In fact, I go further. Model instances of Eloquent should not be returned. So we guarantee a "read-only" instance (which is not used for persistence in the database) <sup>[2](https://adelf.tech/2019/read-eloquent-repositories "Useful Eloquent Repositories?") [3](https://medium.com/laraveltips/voc%C3%AA-entende-repository-pattern-voc%C3%AA-est%C3%A1-certo-disso-d739ecaf544e "Você entende Repository Pattern? Você está certo disso?") </sup>
3. There are a lot of files, how do I build it all?  
   A. It's simple, get a coffee and let's do it...
   
## Instalação
1. Run this Composer command to install the latest version  
```bash
$ composer require adhenrique/laravel-domain-oriented
```
2. If you prefer, you can export the location files:
```bash
php artisan vendor:publish --provider="LaravelDomainOriented\ServiceProvider" --tag="lang"
```
3. Run this command to build the domain structure:
```bash
$ php artisan domain:create Dummy
```
4. Stay calm. If the structure already exists, the console asks you if you want to rewrite it, unless you pass the `--force` flag: 
```bash
$ php artisan domain:create Dummy --force
```
5. And of course, if you want to remove the structure, just run this command:
```bash
$ php artisan domain:remove Dummy
```
That's it enjoy!

## Configuration
### Adjust your Models
Our Model's follow the [Eloquent Model Conventions](https://laravel.com/docs/8.x/eloquent#eloquent-model-conventions)
- PersistenceModel: used only for persistence in the database. Define your fields, casts, etc...
- SearchModel: used for searches. It is very likely that your [relationship](https://laravel.com/docs/8.x/eloquent-relationships) will be here.

### Adjust your Migrations
Our Migrations follow the [Laravel Migration Structure](https://laravel.com/docs/8.x/migrations#migration-structure)

### Adjust your Seeders and Factories
Here, too, we follow the Laravel way of doing things:
- [Factories](https://laravel.com/docs/8.x/database-testing)
- [Seeders](https://laravel.com/docs/8.x/seeding)

### Config your validations
ValidateService is located at `app/Domain/YourDomainName/*`:
```php
use LaravelDomainOriented\Services\ValidateService;

class DummyValidateService extends ValidateService
{
    protected array $rules = [
        // You can define general validation rules, which will be inherited
        // for all actions, or you can define validation rules for each action:
        // SHOW, STORE, UPDATE, DESTROY

        // General rules validation.
        // If any action validation rule is not defined, it will inherit from here.
        'name' => 'required|string',

        // Specific action rules validation. If set, ignores general validations.
        self::SHOW => [
            'id' => 'required|integer',
        ],
        self::UPDATE => [
            'id' => 'required|integer',
            'name' => 'required|string',
        ],
        self::DESTROY => [
            'id' => 'required|integer',
        ],
    ];
}
```
### Confit routes
We follow [Laravel routes](https://laravel.com/docs/8.x/routing) pattern. But as we are dealing with API, modify the file `routes/api.php`, adding the following routes:
```php
Route::get('dummies', 'App\Http\Controllers\DummyController@index');
Route::get('dummies/{id}', 'App\Http\Controllers\DummyController@show');
Route::post('dummies', 'App\Http\Controllers\DummyController@store');
Route::put('dummies/{id}', 'App\Http\Controllers\DummyController@update');
Route::delete('dummies/{id}', 'App\Http\Controllers\DummyController@destroy');
```

## Using
### Searching with filters
You can filter and paginate the data on the listing routes. To do this, send a payload on the request, using your favorite client:

**Simple Where:**  
```json
{
    "name": "adhenrique",
    "email": "eu@adhenrique.com.br"
}
```

**Where in:**  
```json
{
    "id": [1,2,3]
}
```

**Where by operator (like, >, =>, <, <=, <>):**  
```json
{
    "name": {
        "operator": "like",
        "value": "%adhenrique%"
    }
}
```

**Where between:**
```json
{
    "birthdate": {
        "start": "1988-13-12",
        "end": "2021-01-01"
    }
}
```

**Paginate results**

```json
{
    "paginate": {
        "per_page": 1,
        "page": 1
    }
}
```
**Note:** You can use the filters and pagination together.

## Todo
- [ ] CQRS
- [ ] Suporte para versões antigas do Laravel
- [ ] Or Where filter
- [ ] OOP improvements

## Testing
```bash
$ composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security
If you discover any security related issues, please email [eu@adhenrique.com.br](mailto:eu@adhenrique.com.br) instead of using the issue tracker.

## Credits
- [Bruno Maranesi](https://github.com/maranesi)
- [Adelf](https://adelf.tech/)
- [Brent](https://stitcher.io/)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Reading Articles
[1] [Please, stop talking about Repository pattern with Eloquent](https://adelf.tech/2019/useless-eloquent-repositories)  
[2] [Useful Eloquent Repositories?](https://adelf.tech/2019/read-eloquent-repositories)  
[3] [Você entende Repository Pattern? Você está certo disso?](https://medium.com/laraveltips/voc%C3%AA-entende-repository-pattern-voc%C3%AA-est%C3%A1-certo-disso-d739ecaf544e)  
[Laravel — Why you’ve been using the Repository Pattern the wrong way](https://medium.com/@sergiumneagu/laravel-why-youve-been-using-the-repository-pattern-the-wrong-way-952aedf1989b)  

