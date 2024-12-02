<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mikitaba Iliyosainiwa | Usimamizi wa Nyumba</title>
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
    <link href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link href="{{ asset('css/styles.css') }}" rel="stylesheet">
</head>

@include('components.navbar')

<div class="clearfix">
    <div class="content">
        <div class="animated fadeIn">
            <div class="card mb-4" style="margin-bottom: -30px !important;">

                <div class="cardheader">
                    <div class="card-header">
                        <h5 class="mb-1" style="text-align: center;">Tazama mikataba iliyosainiwa</h5>
                    </div>
                </div>

                <div class="panel-body" style="padding: 10px;">
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

                    <form method="GET" action="{{ route('contracts.show') }}" class="mb-3">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="house_id" class="font-weight-bold">Chagua jina la nyumba:</label>
                                <select name="house_id" id="house_id" class="form-control" required>
                                    <option value="">Bonyeza hapa kuchagua nyumba.....</option>
                                    @foreach($houses as $house)
                                    <option value="{{ $house->id }}">{{ $house->house_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="room_id" class="font-weight-bold">Chagua jina la chumba:</label>
                                <select name="room_id" id="room_id" class="form-control" disabled>
                                    <option value="">Chagua chumba baada ya nyumba</option>
                                </select>
                            </div>
                            <div class="col-md-4" style="margin-top: 32px;">
                                <button type="submit" class="btn btn-success"><i class="fas fa-search"></i> Tafuta</button>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-striped table-bordered" id="completed-contracts">
                            <thead>
                                <tr class="table-success">
                                    <th>Na:</th>
                                    <th>&emsp;&emsp;&emsp;&emsp;&emsp;Nyumba</th>
                                    <th>&emsp;&emsp;&emsp;Chumba</th>
                                    <th>&emsp;&emsp;&emsp;&emsp;&emsp;Mpangaji</th>
                                    <th>&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;Tazama</th>
                                    <th>&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;Pakua</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($contracts as $contract)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $contract->house->house_name }}</td>
                                    <td>{{ $contract->room->room_name }}</td>
                                    <td>{{ $contract->tenant->tenant_name }}</td>
                                    <td>
                                        <a href="{{ asset('storage/' . $contract->file_path) }}" class="btn btn-secondary btn-sm" target="_blank"><i class="fas fa-eye"></i>
                                            Tazama
                                        </a>
                                    </td>
                                    <td>
                                        <a href="{{ route('contracts.download', $contract->id) }}" class="btn btn-info btn-sm"><i class="fas fa-file-pdf"></i>
                                            Pakua PDF
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <script>
                    document.getElementById('house_id').addEventListener('change', function() {
                        var houseId = this.value;
                        var roomSelect = document.getElementById('room_id');

                        if (houseId) {
                            fetch(`/get-rooms/${houseId}`)
                                .then(response => response.json())
                                .then(data => {
                                    roomSelect.innerHTML = '<option value="">Chagua chumba</option>';
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

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>
<script>
    $(document).ready(function() {
        $('#view-tenants').DataTable({
            "paging": false,
            "searching": true
        });
    });
</script>

@include('components.footer')