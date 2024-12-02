<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tazama Mikataba | Usimamizi wa Nyumba</title>
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
    <link href="{{ asset('css/styles.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>

@include('components.navbar')

<div class="clearfix">
    <div class="content">
        <div class="animated fadeIn">
            <div class="card mb-4" style="margin-bottom: -30px !important;">

                <div class="cardheader">
                    <div class="card-header">
                        <h5 class="mb-1" style="text-align: center;">Tazama mikataba</h5>
                    </div>
                </div>

                <div class="panel-body" style="padding: 10px;">
                    <form action="{{ route('contracts.view') }}" method="GET" id="filterForm">

                        <div class="row">

                            <div class="form-group col-md-2">
                                <label for="tenant" class="font-weight-bold">Chagua jina la mpangaji:</label>
                                <select name="tenant_id" id="tenant_id" class="form-control">
                                    <option value="">Bonyeza hapa kuchagua mpangaji.....</option>
                                    @foreach($tenants as $tenant)
                                    <option value="{{ $tenant->id }}" {{ request('tenant_id') == $tenant->id ? 'selected' : '' }}>{{ $tenant->tenant_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-md-2">
                                <label for="start_date" class="font-weight-bold">Tarehe ya mwanzo:</label>
                                <input type="date" name="start_date" id="start_date" class="form-control" value="{{ request('start_date') }}">
                            </div>

                            <div class="form-group col-md-2">
                                <label for="end_date" class="font-weight-bold">Tarehe ya mwisho:</label>
                                <input type="date" name="end_date" id="end_date" class="form-control" value="{{ request('end_date') }}">
                            </div>

                            <div class="form-group col-md-2">
                                <label for="house" class="font-weight-bold">Chagua jina la nyumba:</label>
                                <select name="house_id" id="house_id" class="form-control">
                                    <option value="">Bonyeza hapa kuchagua nyumba.....</option>
                                    @foreach($houses as $house)
                                    <option value="{{ $house->id }}" {{ request('house_id') == $house->id ? 'selected' : '' }}>{{ $house->house_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-md-2">
                                <label for="room" class="font-weight-bold">Chagua jina la chumba:</label>
                                <select name="room_id" id="room_id" class="form-control" disabled>
                                    <option value="">Chagua nyumba kwanza na kisha chumba</option>
                                </select>
                            </div>

                        </div>

                        <div class="row mt-3">
                            <div class="col-md-12 text-center">
                                <button type="submit" class="btn btn-success"><i class="fas fa-search"></i> Tafuta</button>
                            </div>
                        </div>
                        
                    </form>

                    <div>
                        @if(session('error'))
                        <div class="alert alert-danger">
                            {!! session('error') !!}
                        </div>
                        @endif

                        @if(session('success'))
                        <div class="alert alert-success">
                            {!! session('success') !!}
                        </div>
                        @endif
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr class="table-success">
                                    <th>&emsp;Na:</th>
                                    <th>&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;Mpangaji</th>
                                    <th>&emsp;&emsp;&emsp;&emsp;&emsp;Nyumba</th>
                                    <th>&emsp;&emsp;&emsp;&emsp;&emsp;Chumba</th>
                                    <th>&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;Hali</th>
                                    <th>&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;Kuanza</th>
                                    <th>&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;Kumaliza</th>
                                    <th>&emsp;&emsp;&emsp;&emsp;Muda</th>
                                    <th>&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;Pokea</th>
                                    <th>&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;Baki</th>
                                    <th>&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;Jumla</th>
                                    <th>&emsp;&emsp;&emsp;&emsp;&emsp;Lipa</th>
                                    <th>&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;Tazama</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($contracts as $contract)
                                <tr @if(\Carbon\Carbon::now()->greaterThan($contract->end_date) && $contract->amount_remaining != 0)
                                    style="background-color: red; color: white;"
                                    @endif>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $contract->tenant->tenant_name ?? 'N/A' }}</td>
                                    <td>{{ $contract->house->house_name ?? 'N/A' }}</td>
                                    <td>{{ $contract->room->room_name ?? 'N/A' }}</td>
                                    <td>
                                        <form action="{{ route('contracts.updateStatus', $contract->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <select name="contract_status" class="form-control" onchange="this.form.submit()">
                                                <option value="BADO" {{ $contract->contract_status == 'BADO' ? 'selected' : '' }}>BADO</option>
                                                <option value="UNAENDELEA" {{ $contract->contract_status == 'UNAENDELEA' ? 'selected' : '' }}>UNAENDELEA</option>
                                                <option value="UMEISHA" {{ $contract->contract_status == 'UMEISHA' ? 'selected' : '' }}>UMEISHA</option>
                                            </select>
                                        </form>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($contract->start_date)->format('d-m-Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($contract->end_date)->format('d-m-Y') }}</td>
                                    <td>Miezi {{ $contract->contract_interval }}</td>
                                    <td>{{ number_format($contract->amount_paid) }}</td>
                                    <td>{{ number_format($contract->amount_remaining) }}</td>
                                    <td>{{ number_format($contract->total) }}</td>
                                    <td>
                                        <button class="btn btn-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#editContractModal{{ $contract->id }}"><i class="fas fa-edit"></i> Lipa</button>
                                    </td>
                                    <td>
                                        <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#viewPaymentTrendModal{{ $contract->id }}"><i class="fas fa-eye"></i> Tazama</button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Modals for each contract -->
                    @foreach($contracts as $contract)
                    <div class="modal fade" id="editContractModal{{ $contract->id }}" tabindex="-1" aria-labelledby="editContractLabel{{ $contract->id }}" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editContractLabel{{ $contract->id }}">Punguza deni kwa mkataba wa - {{ $contract->tenant->tenant_name }}</h5>
                                </div>
                                <form action="{{ route('contract.update', $contract->id) }}" method="POST" onsubmit="disableButton('submitBtn{{ $contract->id }}')">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label for="amount_remaining" class="font-weight-bold">1. Jumla ya deni lake:</label>
                                            <input
                                                type="text"
                                                class="form-control"
                                                id="amount_remaining{{ $contract->id }}"
                                                value="{{ number_format($contract->amount_remaining) }}"
                                                readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="amount_paid" class="font-weight-bold">2. Kiasi anacholipa kwa sasa:</label>
                                            <input
                                                type="number"
                                                name="amount_paid"
                                                class="form-control"
                                                id="amount_paid{{ $contract->id }}"
                                                min="0"
                                                placeholder="Jaza kiasi"
                                                required
                                                oninput="validateAmount('{{ $contract->id }}', '{{ $contract->amount_remaining }}')">
                                        </div>
                                        <div id="error-message{{ $contract->id }}" class="text-danger d-none">Kiasi cha kulipa hakiwezi kuzidi deni la sasa.</div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fas fa-times"></i> Funga</button>
                                        <button type="submit" class="btn btn-success" id="submitBtn{{ $contract->id }}"><i class="fas fa-check"></i> Punguza</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="modal fade" id="viewPaymentTrendModal{{ $contract->id }}" tabindex="-1" aria-labelledby="viewPaymentTrendLabel{{ $contract->id }}" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="viewPaymentTrendLabel{{ $contract->id }}">Mwenendo wa malipo ya - {{ $contract->tenant->tenant_name }}</h5>
                                </div>
                                <div class="modal-body">
                                    <p><strong>Jumla ya kodi:</strong> {{ number_format($contract->total) }} </p>
                                    <p><strong>Kiasi kilicholipwa:</strong> {{ number_format($contract->amount_paid) }} </p>
                                    <p><strong>Kiasi kinachobaki:</strong> {{ number_format($contract->amount_remaining) }} </p>

                                    <h6>Mwelekeo wa malipo</h6>
                                    <ul class="list-group">
                                        @foreach($contract->payments as $payment)
                                        <li class="list-group-item">
                                            <strong>Tarehe:</strong> {{ \Carbon\Carbon::parse($payment->payment_date)->format('d-m-Y') }} <br>
                                            <strong>Kiasi kilicholipwa:</strong> {{ number_format($payment->amount) }} <br>
                                            <strong>Kiasi kinachobaki:</strong> {{ number_format($payment->amount_remaining) }}
                                        </li>
                                        @endforeach
                                    </ul>

                                    @if($contract->payments->isEmpty())
                                    <p class="text-center">Hakuna historia ya malipo.</p>
                                    @endif
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fas fa-times"></i> Funga</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach

                    @if(empty($contracts))
                    <p class="text-center">Hakuna mikataba iliyopatikana.</p>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript to handle modal validation -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
<script>
    function validateAmount(contractId, remainingDebt) {
        const amountInput = document.getElementById(`amount_paid${contractId}`);
        const errorMessage = document.getElementById(`error-message${contractId}`);
        const submitBtn = document.getElementById(`submitBtn${contractId}`);
        const amountPaid = parseFloat(amountInput.value);
        const debt = parseFloat(remainingDebt);

        if (amountPaid > debt) {
            errorMessage.classList.remove('d-none');
            submitBtn.disabled = true;
        } else {
            errorMessage.classList.add('d-none');
            submitBtn.disabled = false;
        }
    }
</script>

<script>
    function disableButton(buttonId) {
        const button = document.getElementById(buttonId);
        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Inapunguza...';
    }
</script>