<?php 
    return [
        'version'    => '0.1.2-Alpha',
        'prefix'     => env('SYNCIT_POS_ROUTES_PREFIX', 'api/pos'),
        'middleware' => 'api',
        'adminstrator_role' => env('SYNCIT_ADMIN_ROLE', 'admin'),
        'employee_role' => env('SYNCIT_EMPLOYEE_ROLE', 'employe|admin'),
    ];