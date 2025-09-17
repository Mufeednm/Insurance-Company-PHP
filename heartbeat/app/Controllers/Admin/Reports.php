<?php

namespace App\Controllers\Admin;

class Reports extends \App\Controllers\BaseController
{
    public function __construct()
    {
        $this->current_user = service('auth')->getCurrentUser();
        $this->policies     = new \App\Models\Policies();
        $this->products     = new \App\Models\Products();
        define('VIEWFOLDER', 'Admin/Report/');
        define('ROUTE', 'admin/reports');
        define('ITEM', 'Report');
        session()->set('activate', "reports");
    }

    public function index()
    {
        $const = [
            'viewfolder' => VIEWFOLDER,
            'items'      => ITEM,
            'route'      => ROUTE
        ];

        $tableArray = [];
        $summary    = [
            'totalPolicies'    => 0,
            'activePolicies'   => 0,
            'lapsedPolicies'   => 0,
            'cancelledPolicies'=> 0,
        ];

        // GET preferred so URL holds filters
        $inputGet  = $this->request->getGet();
        $inputPost = $this->request->getPost();

        $productId   = $inputGet['productId']   ?? $inputPost['productId']   ?? '';
        $reportRange = $inputGet['reportRange'] ?? $inputPost['reportRange'] ?? '';

        // run query only when product or date filter exists
        if (!empty($productId) || !empty($reportRange)) {

            $builder = $this->policies
                ->select('policies.policyId, policies.policyNumber, policies.customerName, policies.customerphone, policies.status, policies.created_at, products.productId, products.name as productName')
                ->join('products','policies.productId = products.productId','left');

            if (!empty($productId)) {
                $builder->where('policies.productId', $productId);
            }

            if (!empty($reportRange)) {
                $dateSplit = preg_split('/\s+to\s+/i', trim($reportRange));
                $from = date("Y-m-d", strtotime($dateSplit[0]));
                $to   = (count($dateSplit) === 2) ? date("Y-m-d", strtotime($dateSplit[1])) : $from;
                $builder->where('DATE(policies.created_at) >=', $from)
                        ->where('DATE(policies.created_at) <=', $to);
            }

            $rows = $builder->get()->getResultArray();

            foreach ($rows as $r) {
                $tableArray[] = [
                    'policyId'      => $r['policyId'] ?? '',
                    'policyNumber'  => $r['policyNumber'] ?? '',
                    'customerName'  => $r['customerName'] ?? '',
                    'customerphone' => $r['customerphone'] ?? '',
                    'status'        => $r['status'] ?? '',
                    'productName'   => $r['productName'] ?? '',
                    'created_at'    => $r['created_at'] ?? '',
                ];
            }

            // ---- Summary counts ----
            $summary['totalPolicies'] = count($rows);
            $summary['activePolicies'] = count(array_filter($rows, fn($x) => strtolower($x['status']) === 'active'));
            $summary['lapsedPolicies'] = count(array_filter($rows, fn($x) => strtolower($x['status']) === 'lapsed'));
            $summary['cancelledPolicies'] = count(array_filter($rows, fn($x) => strtolower($x['status']) === 'cancelled'));
        }

        $data = [
            'table'    => $tableArray,
            'post'     => array_merge($inputPost ?? [], $inputGet ?? []),
            'products' => $this->products->findAll(),
            'summary'  => $summary
        ];

        return view(VIEWFOLDER . 'index', ['const' => $const, 'data' => $data]);
    }
}
