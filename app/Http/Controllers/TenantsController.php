<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TenantsController extends Controller
{
    // For saving a new tenant record (Create)
    public function saveTenant(Request $request)
    {
        if (Auth::user()->user_role != 1) {
            return redirect()->back()->with('error', 'Huna ruhusa ya kuunda mpangaji.');
        }

        $request->validate([
            'tenant_name' => 'required|string|max:255',
            'business_name' => 'nullable|string|max:255',
            'phone_number' => 'required|string|max:15|regex:/^(?:255)[0-9]{9}$/',
        ], [
            'phone_number.regex' => 'Namba ya simu lazima iwe na muundo sahihi: Mfano: 255656345149',
        ]);

        if (Tenant::where('phone_number', $request->phone_number)->exists()) {
            return redirect()->back()->with('error', 'Namba hii tayari inatumika. Tafadhali tumia namba nyingine.');
        }

        try {
            $tenant = Tenant::create([
                'tenant_name' => $request->tenant_name,
                'phone_number' => $request->phone_number,
                'business_name' => $request->business_name,
                'id_type' => $request->id_type,
                'id_number' => $request->id_number,
            ]);

            return redirect()->back()->with('success', 'Umefanikiwa kumsajili ' . $tenant->tenant_name . ' kama mpangaji.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Imeshindwa kusajili. Tafadhali jaribu tena.');
        }
    }

    // For viewing the list of tenants available
    public function viewAllTenants()
    {
        $tenants = Tenant::orderBy('tenant_name')->get();

        return view('tenants.view-tenants', compact('tenants'));
    }

    // For updating tenant details (Update)
    public function updateTenantDetails(Request $request, $id)
    {
        if (Auth::user()->user_role != 1) {
            return redirect()->back()->with('error', 'Huna ruhusa ya kubadili taarifa za mpangaji.');
        }

        $request->validate([
            'tenant_name' => 'required|string|max:255',
            'tenant_phone' => 'required|string|max:15|regex:/^(?:255)[0-9]{9}$/',
            'id_type' => 'nullable|string|max:255',
            'id_number' => 'nullable|string|max:255',
        ], [
            'tenant_phone.regex' => 'Namba ya simu lazima iwe na muundo sahihi: Mfano: 255656345149',
        ]);

        if (Tenant::where('phone_number', $request->tenant_phone)->where('id', '!=', $id)->exists()) {
            return redirect()->back()->with('error', 'Namba ya simu tayari imeshatumika. Tafadhali tumia namba nyingine.');
        }

        try {
            $tenant = Tenant::findOrFail($id);
            $tenant->update([
                'tenant_name' => $request->tenant_name,
                'phone_number' => $request->tenant_phone,
                'id_type' => $request->id_type,
                'id_number' => $request->id_number,
            ]);

            return redirect()->route('tenants.view-tenants')->with('success', 'Umefanikiwa kubadili taarifa za mpangaji huyu - ' . $tenant->tenant_name . '.');
        } catch (\Exception $e) {
            return redirect()->route('tenants.view-tenants')->with('error', 'Imeshindwa kubadili taarifa za mpangaji. Tafadhali jaribu tena.');
        }
    }

    // For deleting tenant details (Delete)
    public function deleteTenantDetails($id)
    {
        if (Auth::user()->user_role != 1) {
            return redirect()->back()->with('error', 'Huna ruhusa ya kufuta taarifa za mpangaji.');
        }

        try {
            $tenant = Tenant::findOrFail($id);
            $tenant->delete();

            return redirect()->route('tenants.view-tenants')->with('success', 'Umefanikiwa kufuta taarifa za mpangaji huyu - ' . $tenant->tenant_name . '.');
        } catch (\Exception $e) {
            return redirect()->route('tenants.view-tenants')->with('error', 'Changamoto imetokea katika kufuta taarifa za mpangaji. Tafadhali jaribu tena.');
        }
    }
}
