<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class InstallCheck implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $lockFile = WRITEPATH . 'install.lock';

        // If not installed and not already on /install, send to installer
        if (!is_file($lockFile) && !str_starts_with($request->getPath(), 'install')) {
            return redirect()->to(site_url('install'));
        }

        // If installed and trying to access /install, bounce to home
        if (is_file($lockFile) && str_starts_with($request->getPath(), 'install')) {
            return redirect()->to(site_url('/'));
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
