<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pakua Mikataba | Usimamizi wa Nyumba</title>
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
                        <h5 class="mb-1" style="text-align: center;">Pakua mikataba</h5>
                    </div>
                </div>

                <div class="panel-body" style="padding: 10px;">

                    <form action="{{ route('contracts.export') }}" method="GET" id="filterForm">
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
                                    <option value="">Chagua kwanza nyumba na kisha chumba</option>
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
                                    <th>&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;Pakua</th>
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
                                    <td>{{ number_format($contract->amount_paid) }} </td>
                                    <td>{{ number_format($contract->amount_remaining) }} </td>
                                    <td>{{ number_format($contract->total) }} </td>
                                    <td>
                                        @if($contract->contract_status === 'BADO')
                                        <button class="btn btn-secondary btn-sm" disabled><i class="fas fa-file-pdf"></i> Pakua mkataba</button>
                                        @else
                                        <a href="{{ route('download.contract', $contract->id) }}" class="btn btn-secondary btn-sm"><i class="fas fa-file-pdf"></i> Pakua mkataba</a>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if(empty($contracts))
                    <p class="text-center">Hakuna mikataba iliyopatikana.</p>
                    @endif

                    <script>
                        document.getElementById('house_id').addEventListener('change', function() {
                            var houseId = this.value;
                            var roomSelect = document.getElementById('room_id');

                            if (houseId) {
                                fetch(`/get-rooms/${houseId}`)
                                    .then(response => response.json())
                                    .then(data => {
                                        roomSelect.innerHTML = '<option value="">Chagua Chumba</option>';
                                        data.rooms.forEach(function(room) {
                                            roomSelect.innerHTML += `<option value="${room.id}">${room.room_name}</option>`;
                                        });
                                        roomSelect.disabled = false;
                                    });
                            } else {
                                roomSelect.innerHTML = '<option value="">Chagua Chumba</option>';
                                roomSelect.disabled = true;
                            }
                        });
                    </script>

                </div>
            </div>
        </div>
    </div>
</div>