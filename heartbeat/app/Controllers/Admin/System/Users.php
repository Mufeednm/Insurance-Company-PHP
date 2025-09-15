<?php

namespace App\Controllers\Admin\System;
use App\Entities\Entity;
use \Hermawan\DataTables\DataTable;

class Users extends \App\Controllers\BaseController
{
    public function __construct()
    {
        $this->model = new \App\Models\Login();
        include_once "heartbeat/app/Controllers/Models.php";

        define('VIEWFOLDER', 'Admin/System/Users/');
        define('ITEM', 'User');
        define('ITEMS', 'Users');
        define('DBTABLE', 'login');
        define('VARIABLE', 'data');
        define('ROUTE', 'admin/system/users');

        session()->set('activate', "settings");
        session()->set('child', "users");
    }

    public function index()
    {
        $const = array(
            'route' => ROUTE,
            'variable' => VARIABLE,
            'item' => ITEM,
            'items' => ITEMS,
            'viewfolder' => VIEWFOLDER,
        );

        return view(VIEWFOLDER . 'index', ['const' => $const]);
    }

    public function load()
    {
        $db = db_connect();
        $builder = $db->table(DBTABLE)->select('loginId, userName, mobileNumber, emailAddress, roleId, status, modified_at');

        return DataTable::of($builder)
            ->addNumbering()
            ->add('action', function ($row) {
                return '<div class="hstack gap-3 flex-wrap justify-content-center">' .

                        (hasPermission(ROUTE.'/edit') ? '
                        <a href="' . site_url(ROUTE . "/edit/" . $row->loginId) . '" class="link-success fs-18"><i class="ri-edit-2-line"></i></a>' : '') .

                        (hasPermission(ROUTE.'/delete') ? '
                        <a href="' . site_url(ROUTE . "/delete/" . $row->loginId) . '" class="link-danger fs-18"><i class="ri-delete-bin-line"></i></a>' : '') .

                        '</div>';
            })
            ->edit('roleId', function($row)
            {
                $roleName = $this->roles->findById($row->roleId) ? $this->roles->findById($row->roleId)->roleName: "N/A";
                return $roleName;
            })
            ->edit('status', function($row)
            {
                if($row->status == "Active")
                {
                    return '<span class="badge rounded-pill bg-success">'.$row->status.'</span>';
                }
                else if($row->status == "InActive")
                {
                    return '<span class="badge rounded-pill bg-warning">'.$row->status.'</span>';
                }
                else
                {
                    return '<span class="badge rounded-pill bg-danger">'.$row->status.'</span>';
                }
            })
            ->edit('modified_at', function($row)
            {
                return date("d/m/Y H:i a", strtotime($row->modified_at));
            })
            ->hide('loginId')
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
        $roles = $this->roles->orderBy('roleName', 'ASC')->find();

        return view(VIEWFOLDER . "new", [VARIABLE => $data, 'const' => $const, 'roles' => $roles]);
    }


    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            $response = service("response");
            $response->setStatusCode(403);
            $response->setBody("You do not have permission to access this page directly");
            return $response;
        }

        $post = $this->request->getPost();
        $data = new Entity($post);

        if ($this->model->insert($data))
        {
            return redirect()->to(ROUTE)->with('success', ITEM . ' updated successfully')->withInput();
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

        $roles = $this->roles->orderBy('roleName', 'ASC')->find();
        return view(VIEWFOLDER . "edit", [VARIABLE => $data, 'const' => $const, 'roles' => $roles]);
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

        if (!$post["password"] || !isset($post["password"]) || $post["password"] == "") {
            $this->model->disablePasswordValidation();
            unset($post["password"]);
            unset($post["password_confirmation"]);
        }

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

    public function check($id)
    {
        $data = $this->model->findById($id);
        if ($data === null) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException(ITEM . " with the ID : $id not found");
        }
        return $data;
    }
}
