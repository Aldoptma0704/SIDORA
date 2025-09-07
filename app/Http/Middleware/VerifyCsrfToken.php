<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        '/login', 'reset-password', 'pegawai/surat/*', 'pimpinan/surat/*', 'pimpinan/pimpinan/surat-balasan/simpan', 
        'admin/users/*', 'admin/users', 'admin/users/create', 'admin/users/*/edit',
    ];
}
