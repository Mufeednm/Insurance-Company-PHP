<?php

namespace App\Controllers\Admin\System;
use App\Entities\Entity;
use \Hermawan\DataTables\DataTable;

class Permissions extends \App\Controllers\BaseController
{
    private $model;

    public function __construct()
    {
        $this->model = new \App\Models\Permissions;
        session()->set('activate', "admin");

        define('VIEWFOLDER','Admin/System/Permissions/');
        define('ITEM','Permission');
        define('ITEMS','Permissions');
        define('DBTABLE','permissions');
        define('VARIABLE','data');
        define('ROUTE','admin/system/permissions');

        include_once "heartbeat/app/Controllers/Models.php";
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

        // Fetch distinct group values
        $db = db_connect();
        $groups = $db->table(DBTABLE)
                    ->select("`group`")
                    ->distinct()
                    ->orderBy('group', 'asc')
                    ->get()->getResultArray();

        $groupNames = array_column($groups, 'group');

        return view(VIEWFOLDER.'index', ['const' => $const, 'groups' => $groupNames]);
    }

    public function load()
    {
        $db = db_connect();
        $builder = $db->table(DBTABLE)
                    ->select('permissionId, description, permissionKey, `group`, created_at, modified_at');

        // Apply group filter if any
        $group = $this->request->getGet('group');
        if (!empty($group)) {
            $builder->where('`group`', $group);
        }

        return DataTable::of($builder)
            ->addNumbering()
            ->add('action', function($row)
            {
                return '<div class="hstack gap-3 flex-wrap justify-content-center">' .

                        (hasPermission('admin/permissions/edit') ? '
                        <a href="'.site_url(ROUTE."/edit/".$row->permissionId).'" class="link-success fs-18"><i class="ri-edit-2-line"></i></a>' : '') .

                        (hasPermission('admin/permissions/delete') ? '
                        <a href="'.site_url(ROUTE."/delete/".$row->permissionId).'" class="link-danger fs-18"><i class="ri-delete-bin-line"></i></a>' : '') .

                        '</div>';
            })

            ->hide('permissionId')
            ->toJson();
    }

    public function new()
    {
        $const = array(
            'route' => ROUTE,
            'variable'=> VARIABLE,
            'item'=> ITEM,
            'items'=> ITEMS,
            'viewfolder'=> VIEWFOLDER,
        );

        $data = new Entity();

        return view(VIEWFOLDER."new", [VARIABLE => $data, 'const' => $const]);
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

        $permissionKeys = $post['permissionKey'];
        $descriptions = $post['description'];
        $groups = $post['group'];

        $errors = [];
        $inserted = 0;

        foreach ($permissionKeys as $index => $permissionKey) {
            $permissionKey = trim($permissionKey);
            $description = trim($descriptions[$index]);
            $group = trim($groups[$index]);

            if (empty($permissionKey)) continue;

            // Check for duplicate
            $existing = $this->model->where('permissionKey', $permissionKey)->first();
            if ($existing) {
                $errors[] = "Permission Key '{$permissionKey}' already exists";
                continue;
            }

            $data = new Entity([
                'permissionKey' => $permissionKey,
                'description' => $description,
                'group' => $group
            ]);

            if ($this->model->insert($data)) {
                $inserted++;
            } else {
                $errors[] = "Failed to insert '{$permissionKey}': " . implode(", ", $this->model->errors());
            }
        }

        if (!empty($errors)) {
            return redirect()->back()->with('error', $errors)->withInput();
        }

        return redirect()->to(ROUTE)->with('success', "$inserted permission(s) created successfully.");
    }

    public function edit($permissionId)
    {
        $data = $this->check($permissionId);
        
        $const = array(
            'route' => ROUTE,
            'variable'=> VARIABLE,
            'item'=> ITEM,
            'items'=> ITEMS,
            'viewfolder'=> VIEWFOLDER,
            'identifier' => $data->permissionId,
            'id' => $data->permissionId
        );

        
        return view(VIEWFOLDER."edit", [VARIABLE => $data, 'const' => $const]);
    }

    public function update($permissionId)
    {
        if($_SERVER['REQUEST_METHOD'] != 'POST')
		{
			$response = service("response");
			$response->setStatusCode(403);
			$response->setBody("You do not have permission to access this page directly");
			return $response;
		}

		$post = $this->request->getPost();
		$data = $this->check($permissionId);

		$data->fill($post);

		if (!$data->hasChanged())
		{
			return redirect()->back()->with('warning', 'No changes were made to save')->withInput();
		}
		else if ($this->model->save($data))
		{
			return redirect()->to(ROUTE)->with('success', ITEM.' updated successfully')->withInput();
		}
		else
		{
			return redirect()->back()->with('error', $this->model->errors())->with('warning', 'Something went wrong.')->withInput();
		}
    }

    public function delete($permissionId)
    {
        if($_SERVER['REQUEST_METHOD'] != 'POST')
		{
            $data = $this->check($permissionId);

            $const = array(
                'route' => ROUTE,
                'variable'=> VARIABLE,
                'item'=> ITEM,
                'items'=> ITEMS,
                'viewfolder'=> VIEWFOLDER,
                'identifier' => $data->permissionId,
                'id' => $data->permissionId
            );

			
			return view(VIEWFOLDER."delete", [VARIABLE => $data, 'const' => $const]);
		}
		else
		{
			$data = $this->check($permissionId);
			if ($this->model->delete($permissionId))
			{
				return redirect()->to(ROUTE)->with('success', ITEM.' deleted successfully');
			}
			else
			{
				return redirect()->back()->with('error', $this->model->errors())->with('warning', 'Something went wrong. Please check the form fields.')->withInput();
			}
		}
    }

    public function check($permissionId)
	{
		$data = $this->model->findById($permissionId);
		if($data===null)
		{
			throw new \CodeIgniter\Exceptions\PageNotFoundException(ITEM." with the ID : $permissionId not found");
		}
		return $data;

	}

}