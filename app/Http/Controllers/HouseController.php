<?php

namespace App\Http\Controllers;

use App\Models\House;
use App\Models\Supervisor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class HouseController extends Controller
{
    // For displaying the house registration form
    public function createHouse()
    {
        if (Auth::user()->user_role != 1) {
            return redirect()->back()->with('error', 'Huna ruhusa ya kuongeza nyumba.');
        }

        $supervisors = Supervisor::all();

        return view('houses.add-house', compact('supervisors'));
    }

    // For saving the house details
    public function saveHouse(Request $request)
    {
        if (Auth::user()->user_role != 1) {
            return redirect()->back()->with('error', 'Huna ruhusa ya kusajili nyumba.');
        }

        $validator = Validator::make($request->all(), [
            'house_name' => 'required|string|max:255',
            'house_location' => 'required|string|max:255',
            'street_name' => 'required|string|max:255',
            'plot_number' => 'required|string|max:255',
            'house_owner' => 'required|string|max:255',
            'phone_number' => 'required|string|max:15|regex:/^(?:255)[0-9]{9}$/',
            'supervisor_id' => 'nullable|string|max:255',

        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            House::create([
                'house_name' => $request->house_name,
                'house_location' => $request->house_location,
                'street_name' => $request->street_name,
                'plot_number' => $request->plot_number,
                'house_owner' => $request->house_owner,
                'phone_number' => $request->phone_number,
                'supervisor_id' => $request->supervisor_id,
            ]);

            return redirect()->back()->with('success', 'Umefanikisha kusajili nyumba.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Imeshindikana kusajili nyumba: Tafadhali jaribu tena ' . $e->getMessage())->withInput();
        }
    }

    // For viewing all the houses available
    public function viewHouses()
    {
        $houses = House::with('houses')->get();
        $supervisors = Supervisor::all();

        return view('houses.view-house', compact('houses', 'supervisors'));
    }

    //For updating the house supervisor
    public function updateHouse(Request $request)
    {
        $validatedData = $request->validate([
            'house_id' => 'required|exists:houses,id',
            'house_name' => 'required|string|max:255',
            'house_owner' => 'required|string|max:255',
            'house_location' => 'required|string|max:255',
            'supervisor_id' => 'nullable|exists:supervisors,id',
        ]);

        try {
            $house = House::findOrFail($validatedData['house_id']);
            $house->house_name = $validatedData['house_name'];
            $house->house_owner = $validatedData['house_owner'];
            $house->house_location = $validatedData['house_location'];
            $house->supervisor_id = $validatedData['supervisor_id'];
            $house->save();

            return redirect()->back()->with('success', 'Taarifa za nyumba zimehifadhiwa kwa mafanikio.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Kuna tatizo: ' . $e->getMessage());
        }
    }
}
