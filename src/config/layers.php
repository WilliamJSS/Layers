<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Namespace
    |--------------------------------------------------------------------------
    |
    | This option define the namespace of repositories and services
    |
    */

    'namespace' => [
        'repositories' => 'Repositories',
        'services' => 'Services',
    ],

    /*
    |--------------------------------------------------------------------------
    | Paths
    |--------------------------------------------------------------------------
    |
    | This option define the paths of models, repositories and services
    |
    */

    'path' => [
        'models' => app_path('Models'),
        'repositories' => app_path('Repositories'),
        'services' => app_path('Services'),
    ],

];