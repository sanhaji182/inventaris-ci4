<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Filters\CSRF;
use CodeIgniter\Filters\DebugToolbar;
use CodeIgniter\Filters\ForceHTTPS;
use CodeIgniter\Filters\Honeypot;

class Filters extends BaseConfig
{
    public array $aliases = [
        'csrf'       => CSRF::class,
        'toolbar'    => DebugToolbar::class,
        'honeypot'   => Honeypot::class,
        'forcehttps' => ForceHTTPS::class,
        'auth'       => \App\Filters\AuthFilter::class,
        'role'       => \App\Filters\RoleFilter::class,
    ];

    public array $globals = [
        'before' => [],
        'after'  => [
            'toolbar' => ['except' => ['api/*', 'unit/tersedia/*']],
        ],
    ];

    public array $methods = [
        'post' => ['csrf'],
        'put'  => ['csrf'],
    ];

    public array $filters = [];
}
