<?php

namespace App\Http\Controllers;

use Dompdf\Dompdf;
use Dompdf\Options;
use App\Models\Room;
use App\Models\House;
use App\Models\Tenant;
use App\Models\Contract;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\UploadedContract;


class ExportContractController extends Controller
{
    //For view contracts to export
    public function exportContracts(Request $request)
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

        return view('contracts.export-contracts', compact('contracts', 'houses', 'tenants'));
    }

    //For getting the room list from the selected house
    public function getRooms($houseId)
    {
        $rooms = Room::where('house_id', $houseId)->get();
        return response()->json(['rooms' => $rooms]);
    }

    //For exporting contracts
    public function downloadContract($id)
    {
        $contract = Contract::with(['tenant', 'room', 'house.supervisor'])->findOrFail($id);

        $contract_interval = \Carbon\Carbon::parse($contract->start_date)->diffInMonths($contract->end_date);

        $data = [
            'tenant_name' => $contract->tenant->tenant_name,
            'tenant_phone' => $contract->tenant->phone_number,
            'id_type' => $contract->tenant->id_type,
            'id_number' => $contract->tenant->id_number,
            'start_date' => \Carbon\Carbon::parse($contract->start_date)->format('d-m-Y'),
            'end_date' => \Carbon\Carbon::parse($contract->end_date)->format('d-m-Y'),
            'amount_paid' => $contract->amount_paid,
            'amount_remaining' => $contract->amount_remaining,
            'total' => $contract->total,
            'rent_per_month' => $contract->room->rent,
            'contract_interval' => $contract_interval,
            'house_owner' => $contract->house->house_owner,
            'house_location' => $contract->house->house_location,
            'street_name' => $contract->house->street_name,
            'plot_number' => $contract->house->plot_number,
            'phone_number' => $contract->house->phone_number,
            'supervisor_name' => $contract->house->supervisor ? $contract->house->supervisor->supervisor_name : 'N/A',
            'supervisor_phone_number' => $contract->house->supervisor ? $contract->house->supervisor->phone_number : 'N/A',
        ];

        $fileName = 'Mkataba_wa_upangishaji_kwa_' . str_replace(' ', '_', $contract->tenant->tenant_name) .
            '_kuanzia_' . \Carbon\Carbon::parse($contract->start_date)->format('d-m-Y') .
            '_hadi_' . \Carbon\Carbon::parse($contract->end_date)->format('d-m-Y') .
            '_umetolewa_tarehe_' . now()->format('d-m-Y') . '.pdf';

        $pdf = Pdf::loadView('pdf.contract', $data);

        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'isPhpEnabled' => true,
            'defaultFont' => 'Arial',
        ]);

        return $pdf->download($fileName);
    }

    //For exporting the signed contract into pdf format
    public function downloadSignedContract($id)
    {
        $contract = UploadedContract::findOrFail($id);

        $filePath = public_path('storage/' . $contract->file_path);

        if (file_exists($filePath)) {
            $options = new Options();
            $options->set('defaultFont', 'Courier');
            $dompdf = new Dompdf($options);

            $html = '<html><body>';
            $html .= '<img src="data:image/png;base64,' . base64_encode(file_get_contents($filePath)) . '" style="width:100%;height:auto;"/>';
            $html .= '</body></html>';

            $dompdf->loadHtml($html);

            $dompdf->setPaper('A4', 'portrait');

            $dompdf->render();

            return $dompdf->stream($contract->tenant->tenant_name . '_mkataba_wa_upangishaji.pdf', [
                'Attachment' => true
            ]);
        } else {
            return redirect()->back()->with('error', 'Changamoto imetokea. Tafadhali jaribu tena kupakua.');
        }
    }
}
