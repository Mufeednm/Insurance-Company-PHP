<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use mysqli;

class Install extends Controller
{
    public function index()
    {
        helper('form');

        return view('install/form', [
            'errors' => session()->getFlashdata('errors') ?? [],
            'old'    => session()->getFlashdata('old') ?? [],
        ]);
    }

    public function run()
    {
        $post = $this->request->getPost();

        $rules = [
            'db_host' => 'required',
            'db_port' => 'required|is_natural_no_zero',
            'db_name' => 'required',
            'db_user' => 'required',
            'db_pass' => 'permit_empty',
            'app_name'=> 'required',
            'base_url'=> 'required|valid_url',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()
                ->with('errors', $this->validator->getErrors())
                ->with('old', $post)
                ->withInput();
        }

        $host    = trim($post['db_host']);
        $port    = (int) $post['db_port'];
        $dbname  = trim($post['db_name']);
        $user    = trim($post['db_user']);
        $pass    = (string) ($post['db_pass'] ?? '');
        $appName = trim($post['app_name']);
        $baseUrl = rtrim($post['base_url'], '/') . '/';

        // 1) Try connecting to MySQL server (no DB selected yet)
        $mysqli = @new mysqli($host, $user, $pass, '', $port);
        if ($mysqli->connect_errno) {
            return redirect()->back()->with('errors', [
                'db' => 'Connection failed: ' . $mysqli->connect_error,
            ])->with('old', $post);
        }

        // 2) Create database if not exists (UTF8MB4)
        if (! $mysqli->query("CREATE DATABASE IF NOT EXISTS `{$mysqli->real_escape_string($dbname)}` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci")) {
            return redirect()->back()->with('errors', [
                'db' => 'Failed to create database: ' . $mysqli->error,
            ])->with('old', $post);
        }

        // 3) Select database
        if (! $mysqli->select_db($dbname)) {
            return redirect()->back()->with('errors', [
                'db' => 'Failed to select database: ' . $mysqli->error,
            ])->with('old', $post);
        }

        // 4) Import SQL dump (multi_query handles multiple statements)
        $sqlFile = APPPATH . 'Database/install/import.sql';
        if (!is_file($sqlFile)) {
            return redirect()->back()->with('errors', [
                'sql' => 'SQL file missing at ' . $sqlFile,
            ])->with('old', $post);
        }

        $sql = file_get_contents($sqlFile);
        if ($sql === false) {
            return redirect()->back()->with('errors', [
                'sql' => 'Unable to read SQL file.',
            ])->with('old', $post);
        }

        // Optional: Replace database name occurrences if your dump has `CREATE DATABASE` or `USE` statements.
        // $sql = str_replace('`gyandarshan`', "`{$dbname}`", $sql);

        if (!$mysqli->multi_query($sql)) {
            return redirect()->back()->with('errors', [
                'sql' => 'Import error: ' . $mysqli->error,
            ])->with('old', $post);
        }
        // Flush all result sets
        while ($mysqli->more_results() && $mysqli->next_result()) { /* drain */ }

        // 5) Ensure app encryption key exists
        $key = bin2hex(random_bytes(16)); // 32 hex chars

        // 6) Write .env (idempotent replace-or-add)
        $this->writeEnv([
            'app.baseURL'                 => $baseUrl,
            'app.appName'                 => $appName,
            'encryption.key'              => $key,
            'database.default.hostname'   => $host,
            'database.default.database'   => $dbname,
            'database.default.username'   => $user,
            'database.default.password'   => $pass,
            'database.default.DBDriver'   => 'MySQLi',
            'database.default.port'       => (string) $port,
            'CI_ENVIRONMENT'              => 'production',
        ]);

        // 7) Mark as installed
        file_put_contents(WRITEPATH . 'install.lock', date('c'));

        // 8) Done
        return redirect()->to(site_url('/'));
    }

    /**
     * Adds or updates .env keys safely.
     */
    private function writeEnv(array $pairs): void
    {
        $envPath = ROOTPATH . '.env';
        $env = is_file($envPath) ? file_get_contents($envPath) : '';

        foreach ($pairs as $k => $v) {
            $pattern = '/^' . preg_quote($k, '/') . '\s*=\s*.*$/m';
            $line    = $k . '="' . addcslashes($v, '"') . '"';

            if (preg_match($pattern, $env)) {
                $env = preg_replace($pattern, $line, $env);
            } else {
                $env .= (str_ends_with($env, PHP_EOL) ? '' : PHP_EOL) . $line . PHP_EOL;
            }
        }
        file_put_contents($envPath, $env);
    }
}
