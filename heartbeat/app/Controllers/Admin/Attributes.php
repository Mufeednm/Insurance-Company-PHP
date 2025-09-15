<?php

namespace App\Controllers\Admin;
use App\Entities\Entity;
use \Hermawan\DataTables\DataTable;

class Attributes extends \App\Controllers\BaseController
{
    private $model;

    public function __construct()
    {
        $this->model = new \App\Models\Attributes();
        include_once "heartbeat/app/Controllers/Models.php";

        session()->set('activate', "admin");

        define('VIEWFOLDER','Admin/Attribute/');
        define('ITEM','Attribute');
        define('ITEMS','Attributes');
        define('DBTABLE','attributes');
        define('VARIABLE','data');
        define('ROUTE','admin/attributes');
    }

    /* -------------------------- List -------------------------- */

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
        // select columns in the exact order the table header expects
        $builder = $db->table(DBTABLE . ' a')
            ->select('a.attributeId, p.name AS productName, a.attributeName, a.attributeType, a.isRequired, a.modified_at')
            ->join('products p', 'p.productId = a.productId', 'left');
    
        return DataTable::of($builder)
            ->addNumbering() // SL#
            ->edit('productName', function($row) {
                return !empty($row->productName) ? $row->productName : '-';
            })
            ->edit('attributeName', function($row) {
                return $row->attributeName;
            })
            ->edit('attributeType', function($row) {
                return $row->attributeType;
            })
            ->edit('isRequired', function($row) {
                return $row->isRequired == 1 ? "Yes" : "No";
            })
            ->edit('modified_at', function($row) {
                return date("d/M/Y H:i:s", strtotime($row->modified_at));
            })
            ->add('action', function ($row) {
                return '<div class="hstack gap-3 flex-wrap justify-content-center">' .
            
                (hasPermission(ROUTE.'/edit') ? '
                <a href="' . site_url(ROUTE . "/edit/" . $row->attributeId) . '" class="link-success fs-18"><i class="ri-edit-2-line"></i></a>' : '') .
            
                (hasPermission(ROUTE.'/delete') ? '
                <a href="' . site_url(ROUTE . "/delete/" . $row->attributeId) . '" class="link-danger fs-18"><i class="ri-delete-bin-line"></i></a>' : '') .
            
                '</div>';
            })
            
            ->hide('attributeId')
            ->toJson();
    }
    
    /* -------------------------- New / Create -------------------------- */

    public function new()
    {
        $const = array(
            'route' => ROUTE,
            'variable'=> VARIABLE,
            'item'=> ITEM,
            'items'=> ITEMS,
            'viewfolder'=> VIEWFOLDER,
        );

     // products for selection on edit
$productModel = new \App\Models\Products();
$products = $productModel->orderBy('name','ASC')->findAll();
$productOptions = [];
foreach ($products as $p) {
    if (is_array($p)) {
        $productOptions[$p['productId']] = $p['name'];
    } elseif (is_object($p)) {
        $productOptions[$p->productId] = $p->name;
    }
}

        $data = new Entity();
        return view(VIEWFOLDER."new", [
            VARIABLE => $data,
            'const'  => $const,
            'productOptions' => $productOptions
        ]);
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

        $post = $this->request->getPost();

        // Normalize options: allow textarea (newline separated) or array from repeated inputs
        $optsRaw = $this->request->getVar('options') ?? null;
        $optionsNormalized = $this->normalizeOptions($optsRaw);

        $row = [
            'productId'     => isset($post['productId']) ? (int)$post['productId'] : null,
            'attributeName' => trim((string)($post['attributeName'] ?? '')),
            'attributeType' => trim((string)($post['attributeType'] ?? 'text')),
            'isRequired'    => isset($post['isRequired']) ? (int)$post['isRequired'] : 0,
            'attributeOrder'=> isset($post['attributeOrder']) ? (int)$post['attributeOrder'] : 0,
            'options'       => $optionsNormalized
        ];

        $data = new Entity($row);

        if($this->model->insert($data))
        {
            return redirect()->to(ROUTE)->with('success', ITEM.' created successfully');
        }
        else
        {
            return redirect()->back()->with('error', $this->model->errors())->withInput();
        }
    }

    /* -------------------------- Edit / Update -------------------------- */

    public function edit($attributeId)
    {
        $data = $this->check($attributeId);

        // products for selection on edit
        $productModel = new \App\Models\Products();
        $products = $productModel->orderBy('name','ASC')->findAll();
        $productOptions = [];
        foreach ($products as $p) {
            if (is_array($p)) {
                $productOptions[$p['productId']] = $p['name'];
            } elseif (is_object($p)) {
                $productOptions[$p->productId] = $p->name;
            }
        }

        $const = array(
            'route' => ROUTE,
            'variable'=> VARIABLE,
            'item'=> ITEM,
            'items'=> ITEMS,
            'viewfolder'=> VIEWFOLDER,
            'identifier' => $data->attributeName,
            'id' => $data->attributeId
        );

        return view(VIEWFOLDER."edit", [VARIABLE => $data, 'const' => $const, 'productOptions' => $productOptions]);
    }

    public function update($attributeId)
    {
        if($_SERVER['REQUEST_METHOD'] != 'POST')
        {
            $response = service("response");
            $response->setStatusCode(403);
            $response->setBody("You do not have permission to access this page directly");
            return $response;
        }

        $post = $this->request->getPost();
        $data = $this->check($attributeId);

        // Normalize options
        $optsRaw = $this->request->getVar('options') ?? null;
        $optionsNormalized = $this->normalizeOptions($optsRaw);

        $data->fill([
            'productId'     => isset($post['productId']) ? (int)$post['productId'] : (int)$data->productId,
            'attributeName' => trim((string)($post['attributeName'] ?? $data->attributeName)),
            'attributeType' => trim((string)($post['attributeType'] ?? $data->attributeType)),
            'isRequired'    => isset($post['isRequired']) ? (int)$post['isRequired'] : (int)$data->isRequired,
            'attributeOrder'=> isset($post['attributeOrder']) ? (int)$post['attributeOrder'] : (int)$data->attributeOrder,
            'options'       => $optionsNormalized
        ]);

        $data->modified_at = date("Y-m-d H:i:s");

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

    /* -------------------------- Delete -------------------------- */

    public function delete($attributeId)
    {
        if($_SERVER['REQUEST_METHOD'] != 'POST')
        {
            $data = $this->check($attributeId);

            $const = array(
                'route' => ROUTE,
                'variable'=> VARIABLE,
                'item'=> ITEM,
                'items'=> ITEMS,
                'viewfolder'=> VIEWFOLDER,
                'identifier' => $data->attributeName,
                'id' => $data->attributeId
            );

            return view(VIEWFOLDER."delete", [VARIABLE => $data, 'const' => $const]);
        }
        else
        {
            $data = $this->check($attributeId);
            if ($this->model->delete($attributeId))
            {
                return redirect()->to(ROUTE)->with('success', ITEM.' deleted successfully');
            }
            else
            {
                return redirect()->back()->with('error', $this->model->errors())->with('warning', 'Something went wrong. Please check the form fields.')->withInput();
            }
        }
    }

    /* -------------------------- Helpers -------------------------- */

    public function check($attributeId)
    {
        $data = $this->model->findById($attributeId);
        if($data===null)
        {
            throw new \CodeIgniter\Exceptions\PageNotFoundException(ITEM." with the ID : $attributeId not found");
        }
        return $data;
    }

    /**
     * Normalize options input into DB format: ["opt1"|"opt2"]
     * Accepts:
     *  - null / empty => null
     *  - string with pipes or newlines
     *  - array of strings
     */
    private function normalizeOptions($raw)
    {
        if ($raw === null) {
            return null;
        }

        // if it's array (ex: multiple inputs), filter blanks
        if (is_array($raw)) {
            $arr = array_values(array_filter(array_map('trim', $raw), function($v){ return $v !== ''; }));
        } else {
            // if string, accept either newline or pipe separated values
            $s = (string)$raw;
            // replace CR with LF
            $s = str_replace("\r", "\n", $s);
            // if contains newline, split by newline, otherwise split by pipe if present
            if (strpos($s, "\n") !== false) {
                $parts = array_map('trim', explode("\n", $s));
            } elseif (strpos($s, "|") !== false) {
                $parts = array_map('trim', explode("|", $s));
            } else {
                // single option
                $parts = [trim($s)];
            }
            $arr = array_values(array_filter($parts, function($v){ return $v !== ''; }));
        }

        if (empty($arr)) {
            return null;
        }

        // build format ["opt1"|"opt2"]
        $escaped = array_map(function($v){
            // strip stray quotes, then escape double quotes by replacing with single quote
            $v = str_replace('"', "'", $v);
            return $v;
        }, $arr);

        return '["' . implode('"|"', $escaped) . '"]';
    }
}