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

        // prefer GET so URL holds filters
        $inputGet  = $this->request->getGet();
        $inputPost = $this->request->getPost();

        $productId   = $inputGet['productId']   ?? $inputPost['productId']   ?? '';
        $reportRange = $inputGet['reportRange'] ?? $inputPost['reportRange'] ?? '';

        // run query only when a filter exists
        if (!empty($productId) || !empty($reportRange)) {

            $builder = $this->policies
                ->select('policies.policyId, policies.policyNumber, policies.customerName, policies.customerphone, policies.status, policies.created_at, products.productId, products.name as productName')
                ->join('products','policies.productId = products.productId','left');

            if (!empty($productId)) {
                $builder->where('policies.productId', $productId);
            }

            if (!empty($reportRange)) {
                // support "YYYY-MM-DD to YYYY-MM-DD" or single date
                $dateSplit = preg_split('/\s+to\s+/i', trim($reportRange));
                $from = date("Y-m-d", strtotime($dateSplit[0]));
                $to   = (count($dateSplit) === 2) ? date("Y-m-d", strtotime($dateSplit[1])) : $from;
                $builder->where('DATE(policies.created_at) >=', $from)
                        ->where('DATE(policies.created_at) <=', $to);
            }

            $rows = $builder->get()->getResultArray();

            // build tableArray — normalize status & created_at
            foreach ($rows as $r) {
                $status = isset($r['status']) ? strtolower(trim((string)$r['status'])) : '';
                if ($status === '1' || $status === 'active' || $status === 'enabled') { $statusLabel = 'Active'; }
                elseif ($status === '0' || $status === 'expired') { $statusLabel = 'Expired'; }
                elseif ($status === 'lapsed') { $statusLabel = 'Lapsed'; }
                elseif ($status === 'cancelled') { $statusLabel = 'Cancelled'; }
                else { $statusLabel = ucfirst($status ?: 'Unknown'); }

                $created = $r['created_at'] ?? null;
                $createdVal = $created ? date("Y-m-d H:i:s", strtotime($created)) : '';

                $tableArray[] = [
                    'policyId'      => $r['policyId'] ?? '',
                    'policyNumber'  => $r['policyNumber'] ?? '',
                    'customerName'  => $r['customerName'] ?? '',
                    'customerphone' => $r['customerphone'] ?? '',
                    'status'        => $statusLabel,
                    'productName'   => $r['productName'] ?? '',
                    'created_at'    => $createdVal,
                ];
            }

            // ---- Summary counts (normalize on original rows) ----
            $summary['totalPolicies'] = count($rows);
            $summary['activePolicies'] = count(array_filter($rows, fn($x) => strtolower((string)($x['status'] ?? '')) === 'active'));
            $summary['lapsedPolicies'] = count(array_filter($rows, fn($x) => strtolower((string)($x['status'] ?? '')) === 'lapsed'));
            $summary['cancelledPolicies'] = count(array_filter($rows, fn($x) => strtolower((string)($x['status'] ?? '')) === 'cancelled'));
        }

        $data = [
            'table'    => $tableArray,
            // merge GET and POST so the view finds submitted values
            'post'     => array_merge($inputPost ?? [], $inputGet ?? []),
            'products' => $this->products->findAll(),
            'summary'  => $summary
        ];

        return view(VIEWFOLDER . 'index', ['const' => $const, 'data' => $data]);
    }
}
