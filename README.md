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

namespace Example;

use App\Models\Example;

interface ExampleRepositoryInterface
{
    public function __construct(Example $example);

    /**
     * Armazena uma nova instância de Example no banco de dados
     * @param \Illuminate\Support\Collection|array|int|string $data
     * @return Example
     */
    public function store($data);

    /**
     * Retorna todas as instâncias de Example do banco de dados
     * @param array|string $columns
     * @param array<array> $filters
     * @return \Illuminate\Database\Eloquent\Collection<int, static>
     */
    public function getList($columns=['*'], $filters=null);

    /**
     * Retorna uma instância de Example a partir do id informado
     * @param int|string $id
     * @return Example
     */
    public function get($id);

    /**
     * Atualiza os dados de uma instância de Example
     * @param \Illuminate\Support\Collection|array|int|string $data $data
     * @param int|string $id
     * @return Example
     */
    public function update($data, $id);

    /**
     * Remove uma instância de Example do banco de dados
     * @param int|string $id
     * @return int
     */
    public function destroy($id);
}
```
