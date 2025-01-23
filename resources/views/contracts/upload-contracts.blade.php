<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pakia Mikataba | Usimamizi wa Nyumba</title>
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
    <link href="{{ asset('css/styles.css') }}" rel="stylesheet">
</head>

@include('components.navbar')

<div class="clearfix">
    <div class="content">
        <div class="animated fadeIn">
            <div class="card mb-4" style="margin-bottom: -30px !important;">

                <div class="cardheader">
                    <div class="card-header">
                        <h5 class="mb-1" style="text-align: center;">Pakia mikataba iliyosainiwa</h5>
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

                    <div class="d-flex justify-content-left mb-3">
                        <a href="{{ url('/completed-contracts') }}" class="btn btn-success btn-sm"><i class="fas fa-eye"></i> Mikataba iliyokamilika</a>
                    </div>

                    <form action="{{ route('contracts.store') }}" method="POST" enctype="multipart/form-data" onsubmit="disableButton('upload-contract')">
                        @csrf
                        <div class="form-group">
                            <label for="house_id" class="font-weight-bold">1. Chagua jina la nyumba:</label>
                            <select id="house_id" name="house_id" class="form-control" required>
                                <option value="">Bonyeza hapa kuchagua nyumba.....</option>
                                @foreach ($houses as $house)
                                <option value="{{ $house->id }}">{{ $house->house_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="room_id" class="font-weight-bold">2. Chagua jina la chumba:</label>
                            <select id="room_id" name="room_id" class="form-control" required>
                                <option value="">Chagua nyumba kwanza na kisha chumba</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="tenant_id" class="font-weight-bold">3. Hakiki jina la mpangaji</label>
                            <input type="text" id="tenant_name" class="form-control" readonly required>
                            <input type="hidden" id="tenant_id" name="tenant_id">
                        </div>
                        <div class="form-group">
                            <label for="uploaded_file" class="font-weight-bold">4. Pakia mkataba uliyosainiwa</label>
                            <input type="file" id="uploaded_file" name="uploaded_file" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-dark btn-sm" id="upload-contract"><i class="fas fa-save"></i> Kamilisha kupakia</button>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('house_id').addEventListener('change', function() {
        let houseId = this.value;
        fetch(`/get-rooms/${houseId}`)
            .then(response => response.json())
            .then(data => {
                let roomSelect = document.getElementById('room_id');
                roomSelect.innerHTML = '<option value="">Chagua nyumba kisha chumba kwanza</option>';
                data.rooms.forEach(room => {
                    let tenant = room.contract?.tenant;
                    roomSelect.innerHTML += `<option value="${room.id}" 
                                              data-tenant="${tenant ? tenant.id : ''}" 
                                              data-tenant-name="${tenant ? tenant.tenant_name : 'Chumba ulichochagua hakina mpangaji'}">
                                              ${room.room_name}
                                          </option>`;
                });
            });
    });

    document.getElementById('room_id').addEventListener('change', function() {
        let tenantId = this.options[this.selectedIndex].getAttribute('data-tenant');
        let tenantName = this.options[this.selectedIndex].getAttribute('data-tenant-name');
        document.getElementById('tenant_name').value = tenantName || 'Chumba ulichochagua hakina mpangaji';
        document.getElementById('tenant_id').value = tenantId || '';
    });
</script>

<!--Script to make validation on the phone_number -->
<script>
    function disableButton(buttonId) {
        const button = document.getElementById(buttonId);
        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Inapakia...';
    }
</script>

@include('components.footer')