<?php

namespace App\Controllers\Admin;

use App\Entities\Entity;
use \Hermawan\DataTables\DataTable;

class Products extends \App\Controllers\BaseController
{
    public function __construct()
    {
        $this->model = new \App\Models\Products(); // table: products
        include_once "heartbeat/app/Controllers/Models.php";

        define('VIEWFOLDER', 'Admin/Product/');
        define('ITEM', 'Product');
        define('ITEMS', 'Products');
        define('DBTABLE', 'products');
        define('VARIABLE', 'data');
        define('ROUTE', 'admin/products');

        session()->set('activate', "admin");
        session()->set('child', "products");
    }

    /* -------------------------- List -------------------------- */

    public function index()
    {

        $const = [
            'route'      => ROUTE,
            'variable'   => VARIABLE,
            'item'       => ITEM,
            'items'      => ITEMS,
            'viewfolder' => VIEWFOLDER,
        ];

        return view(VIEWFOLDER . 'index', ['const' => $const]);
    }

    public function load()
    {
        $db = db_connect();
    
            $builder = $db->table(DBTABLE)
            ->select('productId, name, status, modified_at');
        return DataTable::of($builder)
            ->addNumbering()
            ->add('action', function ($row) {
                return '<div class="hstack gap-3 flex-wrap justify-content-center">' .

                (hasPermission(ROUTE.'/edit') ? '
                <a href="' . site_url(ROUTE . "/edit/" . $row->productId) . '" class="link-success fs-18"><i class="ri-edit-2-line"></i></a>' : '') .

                (hasPermission(ROUTE.'/delete') ? '
                <a href="' . site_url(ROUTE . "/delete/" . $row->productId) . '" class="link-danger fs-18"><i class="ri-delete-bin-line"></i></a>' : '') .

                '</div>';
            })
            ->edit('status', function ($row) {
                return ($row->status == 1)
                    ? '<span class="badge rounded-pill bg-success">Active</span>'
                    : '<span class="badge rounded-pill bg-danger">Inactive</span>';
            })
            ->edit('modified_at', function ($row) {
                return date("d/m/Y H:i a", strtotime($row->modified_at));
            })
            ->hide('productId')
            ->toJson();
    }

    /* -------------------------- New / Create (multi-add) -------------------------- */

    public function new()
    {
        $const = [
            'route'      => ROUTE,
            'variable'   => VARIABLE,
            'item'       => ITEM,
            'items'      => ITEMS,
            'viewfolder' => VIEWFOLDER,
        ];

        $data = new Entity();
        return view(VIEWFOLDER . "new", [
            VARIABLE => $data,
            'const'  => $const
        ]);
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            $response = service("response");
            $response->setStatusCode(403);
            $response->setBody("You do not have permission to access this page directly");
            return $response;
        }

        $post    = $this->request->getPost();
        $names   = $post['name'] ?? [];
        $status  = $post['status'] ?? [];

        $errors = [];
        $saved  = 0;

        if (!is_array($names) || count($names) === 0) {
            return redirect()->back()
                ->with('error', ['Please add at least one Product row'])
                ->with('warning', 'Please check the form for errors')
                ->withInput();
        }

        foreach ($names as $i => $rawName) {
            $name   = trim((string)$rawName);
            $pstatus = isset($status[$i]) ? (int)$status[$i] : 0;

            if ($name === '') {
                $errors[] = "Row ".($i+1).": Product Name is required.";
                continue;
            }

            $row = [
                'name'  => $name,
                'status'=> in_array($pstatus, [0,1], true) ? $pstatus : 0,
            ];

            $entity = new Entity($row);
            if ($this->model->insert($entity)) {
                $saved++;
            } else {
                $errs = $this->model->errors();
                $errors[] = "Row ".($i+1).": ".(is_array($errs) ? implode(', ', $errs) : 'Insert failed');
            }
        }

        if ($saved > 0 && empty($errors)) {
            return redirect()->to(ROUTE)->with('success', "{$saved} ".ITEMS." saved successfully")->withInput();
        }

        if ($saved > 0 && !empty($errors)) {
            return redirect()->to(ROUTE)->with('success', "{$saved} ".ITEMS." saved")
                ->with('error', $errors)
                ->with('warning', 'Some rows were skipped')
                ->withInput();
        }

        return redirect()->back()
            ->with('error', $errors ?: ['Nothing was saved'])
            ->with('warning', 'Please check the form for errors')
            ->withInput();
    }

    /* -------------------------- Edit / Update (single) -------------------------- */

    public function edit($productId)
    {
        $data = $this->check($productId);
        $const = [
            'route'      => ROUTE,
            'variable'   => VARIABLE,
            'item'       => ITEM,
            'items'      => ITEMS,
            'viewfolder' => VIEWFOLDER,
            'identifier' => $productId,
            'id'         => $productId
        ];
        return view(VIEWFOLDER . "edit", [VARIABLE => $data, 'const' => $const]);
    }

    public function update($productId)
    {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            $response = service("response");
            $response->setStatusCode(403);
            $response->setBody("You do not have permission to access this page directly");
            return $response;
        }

        $post = $this->request->getPost();
        $data = $this->check($productId);
        $origName = (string)$data->name;

        $data->fill($post); // name, status

        if (!$data->hasChanged()) {
            return redirect()->back()->with('warning', 'No changes were made to save')->withInput();
        } elseif ($this->model->save($data)) {
            return redirect()->to(ROUTE)->with('success', ITEM . ' updated successfully')->withInput();
        } else {
            return redirect()->back()->with('error', $this->model->errors())->with('warning', 'Something went wrong.')->withInput();
        }
    }

    /* -------------------------- Delete -------------------------- */

    public function delete($productId)
    {
        $data = $this->check($productId);
        if ($_SERVER['REQUEST_METHOD'] != 'POST')
        {
            $const = [
                'route'      => ROUTE,
                'variable'   => VARIABLE,
                'item'       => ITEM,
                'items'      => ITEMS,
                'viewfolder' => VIEWFOLDER,
                'identifier' => $productId,
                'id'         => $productId
            ];
            return view(VIEWFOLDER . "delete", [VARIABLE => $data, 'const' => $const]);
        }
        else
        {
            if ($this->model->delete($productId))
            {
                return redirect()->to(ROUTE)->with('success', ITEM . ' deleted successfully');
            }
            else
            {
                return redirect()->back()->with('error', $this->model->errors())
                    ->with('warning', 'Something went wrong. Please check the form fields.')->withInput();
            }
        }
    }

    /* -------------------------- Helpers -------------------------- */

    public function check($productId)
    {
        $data = $this->model->findById($productId);
        if ($data === null) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException(ITEM . " with the ID : $productId not found");
        }
        return $data;
    }

    private function ensureDir(string $absPath): void
    {
        if (!is_dir($absPath)) {
            @mkdir($absPath, 0777, true);
        }
    }

    private function isImage($file): bool
    {
        $ext = strtolower($file->getExtension() ?? '');
        $ok  = ['jpg','jpeg','png','gif','webp'];
        return in_array($ext, $ok, true);
    }

    private function moveImage($file, string $absBase): string
    {
        $ts   = date('Ymd_His');
        $rand = substr(md5(uniqid((string)mt_rand(), true)), 0, 6);
        $safe = $ts . '_' . $rand . '.' . strtolower($file->getExtension());

        $file->move($absBase, $safe, true);

        return 'uploads/products/' . $safe;
    }
}
