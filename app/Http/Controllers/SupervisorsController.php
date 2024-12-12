<?php

namespace App\Http\Controllers;

use App\Models\House;
use Illuminate\Support\Facades\Auth;


use Illuminate\Http\Request;
use App\Models\Supervisor;

class SupervisorsController extends Controller
{
    //For viewing houses
    public function addSupervisors()
    {
        if (Auth::user()->user_role != 1) {
            return redirect()->back()->with('error', 'Huna ruhusa ya kuunda mkataba.');
        }

        $houses = House::all();

        return view('supervisors.add-supervisor', compact('houses'));
    }


    //For saving supervisors details
    public function saveSupervisors(Request $request)
    {
        $validatedData = $request->validate([
            'supervisor_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:15|unique:supervisors,phone_number',
        ]);

        try {
            Supervisor::create([
                'supervisor_name' => $validatedData['supervisor_name'],
                'phone_number' => $validatedData['phone_number'],
            ]);

            return redirect()->back()->with('success', 'Msimamizi amesajiliwa kikamilifu.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Kuna tatizo: ' . $e->getMessage());
        }
    }

    // For viewing the list of supervisors available
    public function viewAllSuperVisors()
    {
        $supervisors = Supervisor::with('houses')->get();

        return view('supervisors.view-supervisors', compact('supervisors'));
    }
}
