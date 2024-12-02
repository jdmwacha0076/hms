<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Contract;
use Illuminate\Support\Facades\DB;

class HomePageController extends Controller
{
    //For displaying data at the homepage
    public function showDashboard()
    {
        $contractsEndingSoon = Contract::with('room.house', 'tenant')
            ->whereBetween('end_date', [now(), now()->addWeek()])
            ->where('contract_status', '!=', 'BADO')
            ->get();

        $tenantsWithIncompletePayments = Contract::with('tenant', 'room.house')
            ->where('amount_remaining', '>', 0)
            ->where('contract_status', '!=', 'BADO')
            ->get();

        $tenantsWithContractCount = Contract::select('tenant_id', DB::raw('COUNT(*) as contract_count'))
            ->where('contract_status', '!=', 'BADO')
            ->groupBy('tenant_id')
            ->with('tenant')
            ->get();

        $overdueRentPayments = Contract::with('tenant', 'room.house')
            ->where('end_date', '<', now())
            ->where('amount_remaining', '>', 0)
            ->where('contract_status', '!=', 'BADO')
            ->get();

        $vacantRooms = Room::doesntHave('contracts')->with('house')->get();

        $longestTenureTenants = Contract::with('tenant', 'room.house')
            ->where('contract_status', '!=', 'BADO')
            ->orderBy('start_date', 'asc')
            ->take(5)
            ->get();

        $totalTenants = Contract::distinct('tenant_id')->count('tenant_id');

        return view('homepage', compact(
            'contractsEndingSoon',
            'tenantsWithIncompletePayments',
            'tenantsWithContractCount',
            'overdueRentPayments',
            'vacantRooms',
            'longestTenureTenants',
            'totalTenants'
        ));
    }
}
