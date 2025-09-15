<?php

namespace App\Controllers\Admin;

use App\Entities\Entity;
use \Hermawan\DataTables\DataTable;

class Policies extends \App\Controllers\BaseController
{
    public function __construct()
    {
        $this->model = new \App\Models\Policies(); // table: policies
        include_once "heartbeat/app/Controllers/Models.php";

        define('VIEWFOLDER', 'Admin/Policy/');
        define('ITEM', 'Policy');
        define('ITEMS', 'Policies');
        define('DBTABLE', 'policies');
        define('VARIABLE', 'data');
        define('ROUTE', 'admin/policies');
        

        session()->set('activate', "admin");
        session()->set('child', "policies");
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
        ->select(
            DBTABLE . '.policyId, '
            
            . 'products.name as productName, '
            . DBTABLE . '.status, '
            . DBTABLE . '.modified_at, '
            . DBTABLE . '.startDate, '
            . DBTABLE . '.endDate'
        )
        ->join('products', 'products.productId = ' . DBTABLE . '.productId', 'left');
    
    
        return DataTable::of($builder)
            ->addNumbering() // produces DT_RowIndex
            ->add('action', function ($row) {
                return '<div class="hstack gap-3 flex-wrap justify-content-center">' .
                    (hasPermission(ROUTE . '/edit') ? '
                    <a href="' . site_url(ROUTE . "/edit/" . $row->policyId) . '" class="link-success fs-18"><i class="ri-edit-2-line"></i></a>' : '') .
                    (hasPermission(ROUTE . '/delete') ? '
                    <a href="' . site_url(ROUTE . "/delete/" . $row->policyId) . '" class="link-danger fs-18"><i class="ri-delete-bin-line"></i></a>' : '') .
                    '</div>';
            })
            ->edit('status', function ($row) {
                return ($row->status === 'Active')
                    ? '<span class="badge rounded-pill bg-success">Active</span>'
                    : '<span class="badge rounded-pill bg-danger">Expired</span>';
            })
            ->edit('modified_at', function ($row) {
                return date("d/m/Y H:i a", strtotime($row->modified_at));
            })
            ->hide('policyId')
            ->toJson();
    }
    
    /* -------------------------- New / Create -------------------------- */

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

        $productModel = new \App\Models\Products();
        $productOptions = [];
        foreach ($productModel->findAll() as $p) {
            $productOptions[$p->productId] = $p->name;
        }

        return view(VIEWFOLDER . "new", [
            VARIABLE        => $data,
            'const'         => $const,
            'productOptions'=> $productOptions,
            'attributes'    => [] // load later via AJAX
        ]);
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            $response = service("response");
            $response->setStatusCode(403)->setBody("You do not have permission to access this page directly");
            return $response;
        }
    
        $post = $this->request->getPost();
    
        // Basic policy row
        $policyRow = [
            'productId' => isset($post['productId']) ? (int)$post['productId'] : null,
            'startDate' => $post['startDate'] ?? null,
            'endDate'   => $post['endDate'] ?? null,
            'status'    => $post['status'] ?? 'Active',
            'isReminder'    => $post['isReminder'] ?? 'No',
        ];
      
    
        if (empty($policyRow['productId'])) {
            return redirect()->back()->with('error', ['Please select a product'])->withInput();
        }
    
        $attributesPosted = $post['attributes'] ?? [];
    
        // Load attribute definitions for this product
        $attrModel = new \App\Models\Attributes();
        $attrsForProduct = $attrModel->where('productId', $policyRow['productId'])
                                    ->orderBy('attributeOrder', 'ASC')
                                    ->findAll();
    
        $attrIndex = [];
        foreach ($attrsForProduct as $a) {
            $attrIndex[(int)$a->attributeId] = $a;
        }
    
        // Validate required attributes
        $validationErrors = [];
        foreach ($attrIndex as $aid => $attrDef) {
            if ((int)$attrDef->isRequired === 1) {
                if (!array_key_exists($aid, $attributesPosted)) {
                    $validationErrors[] = "Attribute \"{$attrDef->attributeName}\" is required.";
                    continue;
                }
                $val = $attributesPosted[$aid];
                if (is_array($val)) {
                    $ok = false;
                    foreach ($val as $it) {
                        if (trim((string)$it) !== '') { $ok = true; break; }
                    }
                    if (!$ok) $validationErrors[] = "Attribute \"{$attrDef->attributeName}\" is required.";
                } else {
                    if (trim((string)$val) === '') {
                        $validationErrors[] = "Attribute \"{$attrDef->attributeName}\" is required.";
                    }
                }
            }
        }
    
        if (!empty($validationErrors)) {
            return redirect()->back()->with('error', $validationErrors)->withInput();
        }
    
        $db = db_connect();
        $db->transStart();
    
        // Insert policy
        $policyEntity = new Entity($policyRow);
        if (!$this->model->insert($policyEntity)) {
            $errs = $this->model->errors();
            // log
            log_message('error', "Policy insert failed: " . json_encode($errs));
            $db->transComplete();
            return redirect()->back()
                ->with('error', $errs ?: ['Unable to create policy'])
                ->with('warning', 'Please check the form for errors')
                ->withInput();
        }
    
        $policyId = (int)$this->model->getInsertID();
    
        $avModel  = new \App\Models\AttributeValues();
        $pavModel = new \App\Models\PolicyAttributeValues();
    
        $errors = []; // collect non-fatal attribute errors
    
        foreach ($attributesPosted as $attributeId => $value) {
            $aid = (int)$attributeId;
    
            // ensure attribute belongs to the product
            if (!isset($attrIndex[$aid])) {
                $errors[] = "Attribute [{$aid}] does not belong to the selected product.";
                continue;
            }
    
            $valueStr = is_array($value) ? implode('|', array_map('trim', $value)) : trim((string)$value);
    
            $avRow = [
                'policyId'    => $policyId,
                'attributeId' => $aid,
                'value'       => $valueStr,
            ];
         
            if (!$avModel->insert($avRow)) {
                $aErrs = $avModel->errors();
                $dbErr  = $db->error(); // capture DB driver error as well
                $msg = "AttributeValues insert failed for attribute {$aid}: model_errors=" . json_encode($aErrs) . " db_error=" . json_encode($dbErr);
                log_message('error', $msg);
                $errors[] = "Attribute [{$aid}] failed: " . ($aErrs ? implode(', ', (array)$aErrs) : json_encode($dbErr));
                continue;
            }
    
            $attributeValueId = (int)$avModel->getInsertID();
    
            if (!$pavModel->insert([
                'policyId' => $policyId,
                'attributeValueId' => $attributeValueId
            ])) {
                $pErrs = $pavModel->errors();
                $dbErr  = $db->error();
                $msg = "PolicyAttributeValues insert failed link policy {$policyId} -> value {$attributeValueId}: model_errors=" . json_encode($pErrs) . " db_error=" . json_encode($dbErr);
                log_message('error', $msg);
                $errors[] = "Failed linking attribute value {$attributeValueId}";
                continue;
            }
        }
    
        $db->transComplete();
    
        // transaction status
        if ($db->transStatus() === false) {
            // fetch last DB error (driver)
            $dberr = $db->error();
            log_message('error', "Transaction failed while saving policy {$policyId}. DB error: " . json_encode($dberr));
            return redirect()->back()->with('error', ['Transaction failed: unable to save policy', json_encode($dberr)])->withInput();
        }
    
        if (!empty($errors)) {
            // policy saved but some attribute inserts/links failed
            return redirect()->to(ROUTE)->with('success', ITEM . ' created, but some attributes failed')->with('error', $errors);
        }
    
        return redirect()->to(ROUTE)->with('success', ITEM . ' created successfully');
    }
    
    
    
    /* -------------------------- Edit / Update -------------------------- */

    public function edit($policyId)
    {
        $data = $this->check($policyId);

        $productModel = new \App\Models\Products();
        $productOptions = [];
        foreach ($productModel->findAll() as $p) {
            $productOptions[$p->productId] = $p->name;
        }

        $avModel = new \App\Models\AttributeValues();
        $avRows  = $avModel->where('policyId', $policyId)->findAll();

        $values = [];
        foreach ($avRows as $row) {
            $values[$row->attributeId] = strpos($row->value, '|') !== false ? explode('|', $row->value) : $row->value;
        }

        $attrModel = new \App\Models\Attributes();
        $attributes = $attrModel->where('productId', $data->productId)
                                ->orderBy('attributeOrder','ASC')
                                ->findAll();

        $const = [
            'route'      => ROUTE,
            'variable'   => VARIABLE,
            'item'       => ITEM,
            'items'      => ITEMS,
            'viewfolder' => VIEWFOLDER,
            'identifier' => $policyId,
            'id'         => $policyId
        ];

        return view(VIEWFOLDER . "edit", [
            VARIABLE        => $data,
            'const'         => $const,
            'productOptions'=> $productOptions,
            'attributes'    => $attributes,
            'attributeValues' => $values
        ]);
    }

    public function update($policyId)
    {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            $response = service("response");
            $response->setStatusCode(403)->setBody("You do not have permission to access this page directly");
            return $response;
        }

        $post = $this->request->getPost();
        $data = $this->check($policyId);

        $data->fill([
            'productId' => $post['productId'] ?? $data->productId,
            'startDate' => $post['startDate'] ?? $data->startDate,
            'endDate'   => $post['endDate'] ?? $data->endDate,
            'status'    => $post['status'] ?? $data->status
        ]);

        $policySaved = $data->hasChanged() ? $this->model->save($data) : true;

        if (!$policySaved) {
            return redirect()->back()->with('error', $this->model->errors())->with('warning', 'Something went wrong')->withInput();
        }

        $avModel  = new \App\Models\AttributeValues();
        $pavModel = new \App\Models\PolicyAttributeValues();

        $avModel->where('policyId', $policyId)->delete();
        $pavModel->where('policyId', $policyId)->delete();

        $attributesPosted = $post['attributes'] ?? [];
        $errors = [];

        foreach ($attributesPosted as $attributeId => $value) {
            $valueStr = is_array($value) ? implode('|', array_map('trim', $value)) : trim((string)$value);

            $avRow = [
                'policyId'    => $policyId,
                'attributeId' => (int)$attributeId,
                'value'       => $valueStr,
            ];

            if (!$avModel->insert($avRow)) {
                $errors[] = "Attribute [$attributeId]: " . implode(', ', (array)$avModel->errors());
                continue;
            }

            $attributeValueId = $avModel->getInsertID();
            $pavModel->insert([
                'policyId' => $policyId,
                'attributeValueId' => $attributeValueId
            ]);
        }

        if (!empty($errors)) {
            return redirect()->to(ROUTE)->with('success', ITEM . ' updated')->with('error', $errors)->with('warning', 'Some attribute values failed');
        }

        return redirect()->to(ROUTE)->with('success', ITEM . ' updated successfully');
    }

    /* -------------------------- Delete -------------------------- */

    public function delete($policyId)
    {
        $data = $this->check($policyId);
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            $const = [
                'route'      => ROUTE,
                'variable'   => VARIABLE,
                'item'       => ITEM,
                'items'      => ITEMS,
                'viewfolder' => VIEWFOLDER,
                'identifier' => $policyId,
                'id'         => $policyId
            ];
            return view(VIEWFOLDER . "delete", [VARIABLE => $data, 'const' => $const]);
        } else {
            $avModel  = new \App\Models\AttributeValues();
            $pavModel = new \App\Models\PolicyAttributeValues();

            $avModel->where('policyId', $policyId)->delete();
            $pavModel->where('policyId', $policyId)->delete();

            if ($this->model->delete($policyId)) {
                return redirect()->to(ROUTE)->with('success', ITEM . ' deleted successfully');
            } else {
                return redirect()->back()->with('error', $this->model->errors())->with('warning', 'Something went wrong')->withInput();
            }
        }
    }

    /* -------------------------- Attributes AJAX -------------------------- */

 

    public function attributes($productId = null)
{
    if (empty($productId)) {
        return $this->response->setStatusCode(400)->setBody('Product ID required');
    }

    $attrModel = new \App\Models\Attributes();
    $attributes = $attrModel->where('productId', (int)$productId)
                            ->orderBy('attributeOrder', 'ASC')
                            ->findAll();

    $values = []; // for new(); edit will pass actual values
    $html = view(VIEWFOLDER . '_attributes', [
        'attributes' => $attributes,
        'values'     => $values,
    ]);

    return $this->response->setHeader('Content-Type', 'text/html')->setBody($html);
}

    /* -------------------------- Helpers -------------------------- */

    public function check($policyId)
    {
        $data = $this->model->findById($policyId);
        if ($data === null) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException(ITEM . " with the ID : $policyId not found");
        }
        return $data;
    }

  
    
}
