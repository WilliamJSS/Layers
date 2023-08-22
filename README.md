# Layers

A laravel package to generate files for layered architecture and automate interface bindings.

**Laravel version recommended:** >= `9.0`

Go to [Laravel Docs](https://laravel.com/docs/9.x/releases#support-policy) to see support policy.

## Requirements

```json
"php": "^8.0.2"
"illuminate/support": "^9.0"
"illuminate/console": "^9.0"
"symfony/finder": "^6.3"
```

## Installation

```bash
composer require williamjss/layers
```

## Usage

Using the `layers` artisan command, we can be generate files for repositories (interface and eloquent) and services.

`php artisan layers` + `{option}` + `{model name}`

Available options:

- **-e** or **--eloquent** : Generate a repository eloquent for the model
- **-i** or **--interface** : Generate a repository interface for the model
- **-s** or **--service** : Generate a service for the model
- **-r** or **--repository** : Generate a repository interface and eloquent for the model
- **-a** or **--all** : Generate a service, repository interface and repository eloquent for the model

### Generate Layers
```bash
php artisan layers --all User
```

**This command will generate 3 files:**
- app/Repositories/UserRepositoryInterface.php
- app/Repositories/UserRepositoryEloquent.php
- app/Services/UserService.php

<img src="./assets/structure_folder.png" alt="Structure Folder" />

#### UserRepositoryInterface.php
```php
<?php

namespace App\Repositories\User;

use App\Models\User;

interface UserRepositoryInterface
{
    public function __construct(User $user);

    /**
     * Stores a new instance of User in the database
     * @param \Illuminate\Support\Collection|array|int|string $data
     * @return User
     */
    public function store($data);

    /**
     * Returns all instances of User from the database
     * @param array|string $columns
     * @param array<array> $filters
     * @return \Illuminate\Database\Eloquent\Collection<int, static>
     */
    public function getList($columns=['*'], $filters=null);

    /**
     * Returns an instance of User from the given id
     * @param int|string $id
     * @return User
     */
    public function get($id);

    /**
     * Updates the data of an instance of User
     * @param \Illuminate\Support\Collection|array|int|string $data
     * @param int|string $id
     * @return User
     */
    public function update($data, $id);

    /**
     * Removes an instance of User from the database
     * @param int|string $id
     * @return int
     */
    public function destroy($id);
}
```

#### UserRepositoryEloquent.php
```php
<?php

namespace App\Repositories;

use App\Models\User;

class UserRepositoryEloquent implements UserRepositoryInterface
{
    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Stores a new instance of User in the database
     * @param \Illuminate\Support\Collection|array|int|string $data
     * @return User
     */
    public function store($data)
    {
        return $this->user->create($data);
    }

    /**
     * Returns all instances of User from the database
     * @param array|string $columns
     * @param array<array> $filters
     * @return \Illuminate\Database\Eloquent\Collection<int, static>
     */
    public function getList($columns=['*'], $filters=null)
    {
        return $this->user->where($filters)->get($columns);
    }

    /**
     * Returns an instance of User from the given id
     * @param int|string $id
     * @return User
     */
    public function get($id)
    {
        return $this->user->find($id);
    }

    /**
     * Updates the data of an instance of User
     * @param \Illuminate\Support\Collection|array|int|string $data
     * @param int|string $id
     * @return User
     */
    public function update($data, $id)
    {
        $user = $this->user->find($id);
        $user->update($data);
        return $user;
    }

    /**
     * Removes an instance of User from the database
     * @param int|string $id
     * @return int
     */
    public function destroy($id)
    {
        return $this->user->find($id)->delete();
    }
}

```

#### UserService.php
```php
<?php

namespace App\Services;

use App\Repositories\UserRepositoryInterface;


class UserService
{
    private $repoUser;

    public function __construct(UserRepositoryInterface $repoUser)
    {
        $this->repoUser = $repoUser;
    }

    /**
     * Stores a new instance of User in the database
     * @param \Illuminate\Support\Collection|array|int|string $data
     * @return \App\Models\User
     */
    public function store(array $data)
    {
        return $this->repoUser->store($data);
    }

    /**
     * Returns all instances of User from the database
     * @return \Illuminate\Database\Eloquent\Collection<int, static>
     */
    public function getList()
    {
        return $this->repoUser->getList();
    }

    /**
     * Returns an instance of User from the given id
     * @param int|string $id
     * @return \App\Models\User
     */
    public function get($id)
    {
        return $this->repoUser->get($id);
    }

    /**
     * Updates the data of an instance of User
     * @param \Illuminate\Support\Collection|array|int|string $data
     * @param int|string $id
     * @return \App\Models\User
     */
    public function update(array $data, $id)
    {
        return $this->repoUser->update($data, $id);
    }

    /**
     * Removes an instance of User from the database
     * @param int|string $id
     * @return int
     */
    public function destroy($id)
    {
        return $this->repoUser->destroy($id);
    }
}

```

### Generate Layers with Subfolders
```bash
php artisan layers --repository User.Address
```

**This command will generate 2 files:**
- app/Repositories/User/AddressRepositoryInterface.php
- app/Repositories/User/AddressRepositoryEloquent.php

<img src="./assets/structure_folder_with_subfolders.png" alt="Structure Folder with Subfolders" />
