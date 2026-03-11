<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Route filtering
    |--------------------------------------------------------------------------
    | Only named routes matching these patterns are exposed to the frontend.
    | Use either 'only' or 'except', not both (using both disables filtering).
    |
    */
    'only' => [
        'dashboard',
        'projects.*',
        'tasks.*',
        'invitations.accept',
        'logout'
    ],

    /*
    |--------------------------------------------------------------------------
    | Route groups
    |--------------------------------------------------------------------------
    | Optional. Pass a group name to @routes('group') to expose only that set.
    | When using groups, the 'only' / 'except' config is ignored for that call.
    |
    | 'groups' => [
    |     'admin' => ['admin.*', 'users.*'],
    | ],
    */
    'groups' => [],
];
