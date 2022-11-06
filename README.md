## About Laravel Userstamps

> This package forked from https://github.com/WildsideUK/Laravel-Userstamps

Laravel Userstamps provides an Eloquent trait which automatically maintains `created_by` and `updated_by` columns on your model, populated by the currently authenticated user in your application.

When using the Laravel `SoftDeletes` trait, a `deleted_by` column is also handled by this package.

## Installing

This package requires Laravel 5.2 or later running on PHP 5.6 or higher.

This package can be installed using composer:

````
composer require tris-nm/userstamps
````

## Usage

Your model will need to include a `created_by` and `updated_by` column, defaulting to `null`.

If using the Laravel `SoftDeletes` trait, it will also need a `deleted_by` column.

The column type just a text field.

An example migration:

```php
$table->text('created_by', 100)->nullable();
$table->text('updated_by', 100)->nullable();
$table->text('deleted_by', 100)->nullable();
```

You can now load the trait within your model, and userstamps will automatically be maintained:

```php
use Trisnm\Userstamps\Userstamps;

class Foo extends Model {

    use Userstamps;
}
```

Optionally, should you wish to override the names of the `created_by`, `updated_by` or `deleted_by` columns, you can do so by setting the appropriate class constants on your model. Ensure you match these column names in your migration.

```php
use Trisnm\Userstamps\Userstamps;

class Foo extends Model {

    use Userstamps;

    const CREATED_BY = 'alt_created_by';
    const UPDATED_BY = 'alt_updated_by';
    const DELETED_BY = 'alt_deleted_by';
}
```

Methods are also available to temporarily stop the automatic maintaining of userstamps on your models:

```php
$model->stopUserstamping(); // stops userstamps being maintained on the model
$model->startUserstamping(); // resumes userstamps being maintained on the model
```

## Workarounds

This package works by hooking into Eloquent's model event listeners, and is subject to the same limitations of all such listeners.

When you make changes to models that bypass Eloquent, the event listeners won't be fired and userstamps will not be updated.

Commonly this will happen if bulk updating, or their relations.

In this example, model relations are updated via Eloquent and userstamps **will** be maintained:

```php
$model->foos->each(function ($item) {
    $item->bar = 'x';
    $item->save();
});
```

However in this example, model relations are bulk updated and bypass Eloquent. Userstamps **will not** be maintained:

```php
$model->foos()->update([
    'bar' => 'x',
]);
```

As a workaroud to this issue helper methods available - `updateWithUserstamps`. This behaviour is identical to `update`, but it ensure the `updated_by` properties are maintained on the model.

 You generally won't have to use these methods, unless making bulk updates that bypass Eloquent events.

 In this example, models are bulk updated and userstamps **will not** be maintained:

```php
$model->where('name', 'foo')->update([
    'name' => 'bar',
]);
```

However in this example, models are bulk updated using the helper method and userstamps **will** be maintained:

```php
$model->where('name', 'foo')->updateWithUserstamps([
    'name' => 'bar',
]);
```

## License

This open-source software is licensed under the [MIT license](https://opensource.org/licenses/MIT).
