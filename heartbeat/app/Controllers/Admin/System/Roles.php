<?php

namespace App\Controllers\Admin\System;
use App\Entities\Entity;
use \Hermawan\DataTables\DataTable;

class Roles extends \App\Controllers\BaseController
{
    private $model;

    public function __construct()
    {
        $this->model = new \App\Models\Roles;
        include_once "heartbeat/app/Controllers/Models.php";

        define('VIEWFOLDER','Admin/System/Roles/');
        define('ITEM','Role');
        define('ITEMS','Roles');
        define('DBTABLE','roles');
        define('VARIABLE','data');
        define('ROUTE','admin/system/roles');

        session()->set('activate', "admin");
    }

    public function index()
    {
        $const = array(
            'route' => ROUTE,
            'variable'=> VARIABLE,
            'item'=> ITEM,
            'items'=> ITEMS,
            'viewfolder'=> VIEWFOLDER,
        );

        return view(VIEWFOLDER.'index', ['const' => $const]);

    }

    public function load()
    {
        $db = db_connect();
        $builder = $db->table('roles')
                    ->select('roleId, roleName, isAdmin, created_at, modified_at');
        
        return DataTable::of($builder)
            ->addNumbering()
            ->add('action', function($row)
            {
                return '<div class="hstack gap-3 flex-wrap justify-content-center">' .

                        (hasPermission(ROUTE.'/permissions') ? '
                        <a href="' . site_url(ROUTE . "/permissions/" . $row->roleId) . '" class="link-success fs-18"><i class="ri-shield-keyhole-line"></i></a>' : '') .
                        
                        (hasPermission(ROUTE.'/edit') ? '
                        <a href="' . site_url(ROUTE . "/edit/" . $row->roleId) . '" class="link-success fs-18"><i class="ri-edit-2-line"></i></a>' : '') .

                        (hasPermission(ROUTE.'/delete') ? '
                        <a href="' . site_url(ROUTE . "/delete/" . $row->roleId) . '" class="link-danger fs-18"><i class="ri-delete-bin-line"></i></a>' : '') .

                        '</div>';
            })

            ->edit('isAdmin', function($row)
            {
                if($row->isAdmin == 1)
                {
                    return '<span class="badge rounded-pill bg-danger">Yes</span>';
                }
                else
                {
                    return '<span class="badge rounded-pill bg-success">No</span>';
                }
            })
            ->edit('created_at', function($row)
            {
                return date("d/m/Y H:i a", strtotime($row->created_at));
            })
            ->edit('modified_at', function($row)
            {
                return date("d/m/Y H:i a", strtotime($row->modified_at));
            })
            ->hide('roleId')
            ->toJson();
    }

    public function new()
    {
        $const = array(
            'route' => ROUTE,
            'variable' => VARIABLE,
            'item' => ITEM,
            'items' => ITEMS,
            'viewfolder' => VIEWFOLDER,
        );

        $data = new Entity();
        return view(VIEWFOLDER . "new", [VARIABLE => $data, 'const' => $const]);
    }

    public function create()
    {   
        if($_SERVER['REQUEST_METHOD'] != 'POST')
        {
            $response = service("response");
            $response->setStatusCode(403);
            $response->setBody("You do not have permission to access this page directly");
            return $response;
        }
        
        $data = new Entity($this->request->getPost());
        if($this->model->where('roleName', $data->roleName)->first())
        {
            return redirect()->back()->with('error', $this->model->errors())->with('warning', 'Duplicate role')->withInput();
        }
        else if ($this->model->insert($data))
        {
            return redirect()->to(ROUTE)->with('success', ITEM . ' ceated successfully')->withInput();
        }
        else
        {
            return redirect()->back()->with('error', $this->model->errors())->with('warning', 'Please check the form for errors')->withInput();
        }
    }

    public function edit($id)
    {
        $data = $this->check($id);
        $const = array(
            'route' => ROUTE,
            'variable' => VARIABLE,
            'item' => ITEM,
            'items' => ITEMS,
            'viewfolder' => VIEWFOLDER,
            'identifier' => $id,
            'id' => $id
        );

        return view(VIEWFOLDER . "edit", [VARIABLE => $data, 'const' => $const]);
    }

    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            $response = service("response");
            $response->setStatusCode(403);
            $response->setBody("You do not have permission to access this page directly");
            return $response;
        }

        $post = $this->request->getPost();
        $data = $this->check($id);
        $data->fill($post);

        if (!$data->hasChanged())
        {
            return redirect()->back()->with('warning', 'No changes were made to save')->withInput();
        }
        else if ($this->model->save($data))
        {
            return redirect()->to(ROUTE)->with('success', ITEM . ' updated successfully')->withInput();
        }
        else
        {
            return redirect()->back()->with('error', $this->model->errors())->with('warning', 'Something went wrong.')->withInput();
        }
    }

    public function delete($id)
    {
        $data = $this->check($id);
        if ($_SERVER['REQUEST_METHOD'] != 'POST')
        {
            $const = array(
                'route' => ROUTE,
                'variable' => VARIABLE,
                'item' => ITEM,
                'items' => ITEMS,
                'viewfolder' => VIEWFOLDER,
                'identifier' => $id,
                'id' => $id
            );
            return view(VIEWFOLDER . "delete", [VARIABLE => $data, 'const' => $const]);
        }
        else
        {
            $data = $this->check($id);
            if ($this->model->delete($id))
            {
                return redirect()->to(ROUTE)->with('success', ITEM . ' deleted successfully');
            }
            else
            {
                return redirect()->back()->with('error', $this->model->errors())->with('warning', 'Something went wrong. Please check the form fields.')->withInput();
            }
        }
    }

    public function permissions($id)
    {

        $const = array(
            'route' => ROUTE,
            'variable' => VARIABLE,
            'item' => ITEM,
            'items' => ITEMS,
            'viewfolder' => VIEWFOLDER,
            'identifier' => $id,
            'id' => $id
        );
        
        $role = $this->check($id);
        $assignedPermissionIds = $this->rolePermissions
        ->where('roleId', $id)
        ->findColumn('permissionId');
        
        $globalPermissions = $this->permissions->findAll();

        return view(VIEWFOLDER. "permissions", [
            'role' => $role,
            'globalPermissions' => $globalPermissions,
            'assignedPermissionIds' => $assignedPermissionIds,
            'const' => $const
        ]);
    }

    public function updatepermissions($id)
    {
        if (!$this->request->is('post')) {
            return redirect()->back()->with('danger', 'Invalid request method.');
        }

        // Validate if role exists
        $role = $this->check($id);
        if (!$role)
        {
            return redirect()->back()->with('danger', 'Role not found.');
        }

        $permissions = $this->request->getPost('permissions') ?? [];
        $validPermissionIds = array_column($this->permissions->findAll(), 'permissionId');

        $safePermissions = array_filter($permissions, function($pid) use ($validPermissionIds) {
            return in_array($pid, $validPermissionIds);
        });

        // Start DB transaction for safety
        $db = \Config\Database::connect();
        $db->transStart();

        // Delete old role permissions
        $this->rolePermissions->where('roleId', $id)->delete();

        if (!empty($safePermissions)) {
            $data = [];
            foreach ($safePermissions as $permissionId) {
                $data[] = [
                    'roleId' => $id,
                    'permissionId' => $permissionId,
                    'isAllowed' => 1
                ];
            }
            $this->rolePermissions->insertBatch($data);
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('danger', 'Failed to update permissions.');
        }

        return redirect()->to("admin/system/roles/permissions/$id")->with('success', 'Permissions updated successfully');
    }

    public function check($id)
    {
        $data = $this->model->findById($id);
        if ($data === null) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException(ITEM . " with the ID : $id not found");
        }
        return $data;
    }
}