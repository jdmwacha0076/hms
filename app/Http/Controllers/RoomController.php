<?php

namespace App\Http\Controllers;

use App\Models\House;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class RoomController extends Controller
{
    // For viewing all houses and their rooms
    public function createRoom()
    {
        $houses = House::all();

        return view('rooms.add-room', compact('houses'));
    }

    // For saving a new room record
    public function saveRoom(Request $request)
    {
        if (Auth::user()->user_role != 1) {
            return redirect()->back()->with('error', 'Huna ruhusa ya kuunda chumba.');
        }

        $validator = Validator::make($request->all(), [
            'house_id' => 'required|exists:houses,id',
            'room_name' => 'required|string|max:255',
            'rent' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            Room::create([
                'house_id' => $request->house_id,
                'room_name' => $request->room_name,
                'rent' => $request->rent,
            ]);

            return redirect()->back()->with('success', 'Umefanikisha kusajili chumba');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Imeshindikana kusajili chumba: ' . $e->getMessage())->withInput();
        }
    }

    // For viewing all the rooms available
    public function viewRooms()
    {
        $houses = House::with('rooms')->get();

        return view('rooms.view-rooms', compact('houses'));
    }

    // For updating the room details
    public function updateRoomDetails(Request $request, $room_id)
    {
        if (Auth::user()->user_role != 1) {
            return redirect()->back()->with('error', 'Huna ruhusa ya kubadili taarifa za chumba.');
        }

        $validator = Validator::make($request->all(), [
            'room_name' => 'required|string|max:255',
            'rent' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $room = Room::findOrFail($room_id);
        $room->update([
            'room_name' => $request->room_name,
            'rent' => $request->rent,
        ]);

        return redirect()->back()->with('success', 'Umefanikisha kubadili taarifa za chumba hiki.');
    }
}
