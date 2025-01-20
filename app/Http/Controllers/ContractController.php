<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\User;
use App\Models\House;
use App\Models\Tenant;
use App\Models\Contract;
use Illuminate\Http\Request;
use App\Models\UploadedContract;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Jobs\SendSmsJob;

class ContractController extends Controller
{
    //For viewing house, rooms, tenants and supervisors 
    public function createContract()
    {
        if (Auth::user()->user_role != 1) {
            return redirect()->back()->with('error', 'Huna ruhusa ya kuunda mkataba.');
        }

        $houses = House::all();
        $tenants = Tenant::all();

        return view('contracts.make-contract', compact('houses', 'tenants'));
    }

    //For getting the room list from the selected house
    public function getRooms($houseId)
    {
        $rooms = Room::where('house_id', $houseId)->get();
        return response()->json(['rooms' => $rooms]);
    }

    //For saving a new contract
    public function saveContract(Request $request)
    {
        if (Auth::user()->user_role != 1) {
            return redirect()->back()->with('error', 'Huna ruhusa ya kuunda mkataba.');
        }

        $validatedData = $request->validate([
            'tenant_id' => 'required|exists:tenants,id',
            'house_id' => 'required|exists:houses,id',
            'room_id' => 'required|exists:rooms,id',
            'duration' => 'required|integer|min:1',
            'start_date' => 'required|date',
            'amount_paid' => 'required|numeric|min:0',
        ]);

        $room = Room::find($validatedData['room_id']);
        if (!$room) {
            return redirect()->back()->with('error', 'Chumba hakipatikani.');
        }

        if ($room->house_id != $validatedData['house_id']) {
            return redirect()->back()->with('error', 'Chumba hiki hakihusiani na nyumba iliyochaguliwa.');
        }

        // Check for an active contract for the same room
        $existingActiveContract = Contract::where('room_id', $validatedData['room_id'])
            ->where('contract_status', 'UNAENDELEA')
            ->first();

        if ($existingActiveContract) {
            return redirect()->back()->with('error', 'Chumba hiki tayari kina mkataba unaoendelea. Tafadhali chagua chumba kingine.');
        }

        $totalRent = $room->rent * $validatedData['duration'];
        $amountRemaining = $totalRent - $validatedData['amount_paid'];
        $endDate = \Carbon\Carbon::parse($validatedData['start_date'])->addMonths((int) $validatedData['duration']);

        try {
            $contract = Contract::create([
                'tenant_id' => $validatedData['tenant_id'],
                'house_id' => $validatedData['house_id'],
                'room_id' => $validatedData['room_id'],
                'start_date' => $validatedData['start_date'],
                'end_date' => $endDate,
                'contract_interval' => (int) $validatedData['duration'],
                'amount_paid' => $validatedData['amount_paid'],
                'amount_remaining' => $amountRemaining,
                'total' => $totalRent,
                'contract_status' => 'UNAENDELEA',
            ]);

            if ($validatedData['amount_paid'] > 0) {
                $contract->payments()->create([
                    'amount' => $validatedData['amount_paid'],
                    'amount_remaining' => $amountRemaining,
                    'payment_date' => now(),
                ]);
            }

            $tenantName = $contract->tenant->tenant_name;
            $tenantPhone = $contract->tenant->phone_number;
            $businessName = $contract->tenant->business_name;
            $houseName = $contract->house->house_name;
            $roomName = $contract->room->room_name;
            $createdBy = Auth::user()->name;

            $message = <<<SMS
Ndugu, Pokea taarifa za mkataba mpya:
Aliyetengeneza: $createdBy
Mpangaji: $tenantName
Biashara: $businessName
Nyumba: $houseName
Chumba: $roomName
Tarehe ya kuanza: {$endDate->format('d-m-Y')}
Tarehe ya mwisho: {$endDate->format('d-m-Y')}
Muda: Miezi {$validatedData['duration']}
Jumla ya kodi: $totalRent TZS
Kiasi kilicholipwa: {$validatedData['amount_paid']} TZS
Kiasi kilichobaki: $amountRemaining TZS
SMS;

            $smsJob = new SendSmsJob([$tenantPhone], $message);
            $smsJob->handle();

            return redirect()->back()->with('success', "Umefanikiwa kutengeneza mkataba wa $tenantName.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Zingatia: Kuna changamoto imetokea - ' . $e->getMessage());
        }
    }

    //For viewing contracts
    public function viewContracts(Request $request)
    {
        $tenants = Tenant::all();
        $houses = House::all();

        $query = Contract::query();

        if ($request->filled('start_date')) {
            $query->where('start_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->where('end_date', '<=', $request->end_date);
        }

        if ($request->filled('house_id')) {
            $query->where('house_id', $request->house_id);
        }

        if ($request->filled('room_id')) {
            $query->where('room_id', $request->room_id);
        }

        if ($request->filled('tenant_id')) {
            $query->where('tenant_id', $request->tenant_id);
        }

        $contracts = $query->get();

        return view('contracts.view-contracts', compact('contracts', 'houses', 'tenants'));
    }

    //For paying debt contract
    public function updateContract(Request $request, $id)
    {
        if (Auth::user()->user_role != 1) {
            return redirect()->back()->with('error', 'Huna ruhusa ya kubadili mkataba.');
        }

        $contract = Contract::findOrFail($id);

        $amountPaid = $request->input('amount_paid');
        $amountReduced = number_format($contract->amount_paid, 0);
        $contract->amount_paid += $amountPaid;
        $contract->amount_remaining = $contract->total - $contract->amount_paid;
        $contract->save();

        $contract->payments()->create([
            'amount' => $amountPaid,
            'payment_date' => now(),
            'amount_remaining' => $contract->amount_remaining,
        ]);

        $tenantName = $contract->tenant->tenant_name;
        $businessName = $contract->tenant->business_name;
        $houseName = $contract->house->house_name;
        $roomName = $contract->room->room_name;
        $amountRemaining = number_format($contract->amount_remaining);
        $totalRent = number_format($contract->total);
        $startDate = $contract->start_date;
        $endDate = $contract->end_date;
        $contractInterval = $contract->contract_interval;
        $updatedBy = Auth::user()->name;

        $message = "Kodi imelipwa:\n"
            . "Aliyejaza: $updatedBy\n"
            . "Mpangaji: $tenantName\n"
            . "Biashara: $businessName\n"
            . "Nyumba: $houseName\n"
            . "Chumba: $roomName\n"
            . "Leo kalipa: $amountPaid\n"
            . "Ameshalipa: $amountReduced\n"
            . "Imebaki: $amountRemaining\n"
            . "Jumla: $totalRent\n"
            . "Muda: Miezi $contractInterval\n"
            . "Anza: $startDate\n"
            . "Mwisho: $endDate.";

        $phoneNumbers = User::pluck('phone_number')->toArray();

        $smsJob = new SendSmsJob($phoneNumbers, $message);
        $smsJob->handle();

        return redirect()->back()->with('success', "Umefanikiwa kupunguza kodi katika mkataba wa $tenantName.");
    }

    //For viewing contract payment trend
    public function viewPaymentTrend($id)
    {
        $contract = Contract::with('payments')->findOrFail($id);

        return view('contracts.payment-trend', compact('contract'));
    }

    //For showing view to upload signed contract
    public function showUploadForm()
    {
        if (Auth::user()->user_role != 1) {
            return redirect()->back()->with('error', 'Huna ruhusa ya kupakia mkataba.');
        }

        $houses = House::with('rooms.tenant')->get();
        return view('contracts.upload-contracts', compact('houses'));
    }

    //For showing rooms after house selection
    public function getRoomsContracts($houseId)
    {
        $rooms = Room::where('house_id', $houseId)
            ->with(['contract' => function ($query) {
                $query->select('room_id', 'tenant_id')->with('tenant:id,tenant_name');
            }])
            ->get();

        return response()->json(['rooms' => $rooms]);
    }

    //For saving an uploaded signed contract
    public function saveSignedContracts(Request $request)
    {
        if (Auth::user()->user_role != 1) {
            return redirect()->back()->with('error', 'Huna ruhusa ya kupakia mkataba.');
        }

        $request->validate([
            'house_id' => 'required|exists:houses,id',
            'room_id' => 'required|exists:rooms,id',
            'tenant_id' => 'required|exists:tenants,id',
            'uploaded_file' => 'required|image|mimes:jpeg,png,jpg,gif|max:20480'
        ]);

        try {
            $filePath = $request->file('uploaded_file')->store('contracts', 'public');

            $data = [
                'house_id' => $request->house_id,
                'room_id' => $request->room_id,
                'tenant_id' => $request->tenant_id,
                'file_path' => $filePath,
            ];

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();

                Storage::disk('public')->put('images/' . $imageName, file_get_contents($image));

                $data['image_path'] = 'images/' . $imageName;
            }

            UploadedContract::create($data);

            $tenantName = Tenant::find($request->tenant_id)->tenant_name;

            return redirect()->back()->with('success', "Umefanikiwa kupakia mkataba wa $tenantName.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Zingatia: Kuna changamoto imetokea - ' . $e->getMessage());
        }
    }

    //For displaying the signed contracts
    public function showSignedContracts(Request $request)
    {
        $houses = House::all();

        $contracts = UploadedContract::with(['house', 'room', 'tenant']);

        if ($request->has('house_name') && $request->house_name != '') {
            $contracts = $contracts->whereHas('house', function ($query) use ($request) {
                $query->where('house_name', $request->house_name);
            });
        }

        if ($request->has('room_name') && $request->room_name != '') {
            $contracts = $contracts->whereHas('room', function ($query) use ($request) {
                $query->where('room_name', $request->room_name);
            });
        }

        $contracts = $contracts->get();

        $roomsByHouse = [];
        foreach ($houses as $house) {
            $roomsByHouse[$house->house_name] = $house->rooms;
        }

        return view('contracts.completed-contracts', compact('contracts', 'houses', 'roomsByHouse'));
    }

    // For updating the contract order status
    public function updateStatus(Request $request, $id)
    {
        if (Auth::user()->user_role != 1) {
            return redirect()->back()->with('error', 'Huna ruhusa ya kubadili hali ya mkataba.');
        }

        $request->validate([
            'contract_status' => 'required|in:BADO,UNAENDELEA,UMEISHA',
        ]);

        $contract = Contract::findOrFail($id);

        if ($contract->amount_remaining != 0) {
            return redirect()->back()->with('error', 'Mkataba hauwezi kubadilishwa kwa sababu bado una kiasi kilichobaki.');
        }

        if ($contract->end_date >= now()) {
            return redirect()->back()->with('error', 'Mkataba hauwezi kubadilishwa kwa sababu muda wa kumalizika bado haujapita.');
        }

        $oldStatus = $contract->contract_status;

        $contract->contract_status = $request->input('contract_status');
        $contract->save();

        return redirect()->back()->with('success', "Umefanikiwa kubadili hali ya mkataba kutoka '$oldStatus' na kuwa '{$contract->contract_status}'.");
    }
}
