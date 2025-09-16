<?php

namespace App\Controllers\Admin;

class Dashboard extends \App\Controllers\BaseController
{
    protected $productsModel;
    protected $policiesModel;

    public function __construct()
    {
        $this->productsModel = new \App\Models\Products();
        $this->policiesModel = new \App\Models\Policies();

        include_once "heartbeat/app/Controllers/Models.php";
        session()->set('activate', "dashboard");
    }

    public function index()
    {
        session()->set('activate', "dashboard");

        // Totals (adjust status filters to match your schema if needed)
        $totalProducts = (int) $this->productsModel->countAllResults(false);
        $totalPolicies = (int) $this->policiesModel->countAllResults(false);

        // Example: active / expired (change field names/values to match your DB)
        $activePolicies = (int) $this->policiesModel->where('status', 'active')->countAllResults(false);
        $expiredPolicies = (int) $this->policiesModel->where('status', 'expired')->countAllResults(false);
  // Reset query for reuse
  $this->policiesModel->resetQuery();
        // Policies by day (for chart) — returns array of {date, total}
        $rows = $this->policiesModel
            ->asArray()
            ->select("DATE(created_at) as date, COUNT(policyId) as total")
            ->where('created_at IS NOT NULL')
            ->groupBy('DATE(created_at)')
            ->orderBy('date', 'ASC')
            ->findAll();
         

        $policiesByDays = [];
        foreach ($rows as $r) {
            $policiesByDays[] = [
                'date' => $r['date'],
                'total' => (int) $r['total']
            ];
        }

   // inside Dashboard::index()
$productPolicyCounts = $this->productsModel
->asArray()                       // <--- return plain arrays, not Entities
->select('products.productId, products.name, COUNT(policies.policyId) as totalPolicies')
->join('policies', 'policies.productId = products.productId', 'left')
->groupBy('products.productId')
->orderBy('products.name', 'ASC')
->findAll();





        $data = [
            'totalProducts'   => $totalProducts,
            'totalPolicies'   => $totalPolicies,
            'activePolicies'  => $activePolicies,
            'expiredPolicies' => $expiredPolicies,
            'productPolicyCounts' => $productPolicyCounts,
            'policiesByDays'  => json_encode($policiesByDays),

        ];

        return view('admin/dashboard', ['data' => $data]);
    }
}
