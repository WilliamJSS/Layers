# Layers

A package to generate files for layered architecture.

## Installation

```bash
composer require williamjss/layers
```

## Usage

Using the `layer` artisan command, we can be generate files for repositories (interface and eloquent) and services.

`php artisan layer` + `{model name}` + `{option}`

Available options:

- **--e** or **--eloquent** : Generate a repository eloquent for the model
- **--i** or **--interface** : Generate a repository interface for the model
- **--s** or **--service** : Generate a service for the model
- **--r** or **--repository** : Generate a repository interface and eloquent for the model
- **--a** or **--all** : Generate a service, repository interface and repository eloquent for the model

### Example
```bash
php artisan layer Example --interface
```

This command will generate code follow:
```php
<?php

namespace App\Repositories\Example;

use App\Models\Example;

interface ExampleRepositoryInterface
{
    public function __construct(Example $example);

    /**
     * Stores a new instance of Example in the database
     * @param \Illuminate\Support\Collection|array|int|string $data
     * @return Example
     */
    public function store($data);

    /**
     * Returns all instances of Example from the database
     * @param array|string $columns
     * @param array<array> $filters
     * @return \Illuminate\Database\Eloquent\Collection<int, static>
     */
    public function getList($columns=['*'], $filters=null);

    /**
     * Returns an instance of Example from the given id
     * @param int|string $id
     * @return Example
     */
    public function get($id);

    /**
     * Updates the data of an instance of Example
     * @param \Illuminate\Support\Collection|array|int|string $data $data
     * @param int|string $id
     * @return Example
     */
    public function update($data, $id);

    /**
     * Removes an instance of Example from the database
     * @param int|string $id
     * @return int
     */
    public function destroy($id);
}
```
