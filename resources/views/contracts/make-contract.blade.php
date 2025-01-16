<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tengeneza Mikataba | Usimamizi wa Nyumba</title>
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
                        <h5 class="mb-1" style="text-align: center;">Tengeneza mkataba kwa mpangaji</h5>
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

                    <form action="{{ route('contract.save') }}" method="POST" enctype="multipart/form-data" onsubmit="disableButton('make-contract')">
                        @csrf
                        <div class="form-group">
                            <label for="tenant_id" class="font-weight-bold">1. Chagua mpangaji:</label>
                            <select name="tenant_id" id="tenant_id" class="form-control" required>
                                <option value="">Bonyeza hapa kuchagua mpangaji.....</option>
                                @foreach($tenants as $tenant)
                                <option value="{{ $tenant->id }}">{{ $tenant->tenant_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="house_id" class="font-weight-bold">2. Chagua nyumba:</label>
                            <select name="house_id" id="house_id" class="form-control" required>
                                <option value="">Bonyeza hapa kuchagua nyumba.....</option>
                                @foreach($houses as $house)
                                <option value="{{ $house->id }}">{{ $house->house_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="room_id" class="font-weight-bold">3. Chagua chumba:</label>
                            <select name="room_id" id="room_id" class="form-control" required>
                                <option value="">Chagua nyumba kwanza na kisha chumba</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="rent_per_month" class="font-weight-bold">4. Kodi kwa mwezi:</label>
                            <input type="number" id="rent_per_month" class="form-control" readonly>
                        </div>

                        <div class="form-group">
                            <label for="contract_interval" class="font-weight-bold">5. Muda wa mkataba (Miezi):</label>
                            <input type="number" name="duration" id="contract_interval" min="1" class="form-control" placeholder="Jaza muda wa mkataba (Kwa miezi)" required>
                        </div>

                        <div class="form-group">
                            <label for="start_date" class="font-weight-bold">6. Tarehe ya kuanza kwa mkataba:</label>
                            <input type="date" name="start_date" id="start_date" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="end_date" class="font-weight-bold">7. Tarehe ya kumaliza kwa mkataba:</label>
                            <input type="date" id="end_date" class="form-control" readonly>
                        </div>

                        <div class="form-group">
                            <label for="total_rent" class="font-weight-bold">8. Jumla ya kiasi cha kodi:</label>
                            <input type="number" id="total_rent" class="form-control" readonly value="100000"> <!-- Example value -->
                        </div>

                        <div class="form-group">
                            <label for="amount_paid" class="font-weight-bold">9. Malipo ya awali:</label>
                            <input type="number" name="amount_paid" id="amount_paid" class="form-control" required>
                            <div id="amount-error" style="color:red;display:none;">Kiasi kinacholipwa hakiwezi kuzidi jumla ya kodi...!!!</div>
                        </div>

                        <div class="form-group">
                            <label for="amount_remaining" class="font-weight-bold">10. Kiasi kinachobaki:</label>
                            <input type="number" id="amount_remaining" class="form-control" readonly>
                        </div>

                        <div class="text-left">
                            <button type="submit" class="btn btn-success btn-sm" id="make-contract">
                                <i class="fas fa-check"></i> Tengeneza mkataba
                            </button>
                        </div>



                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function disableButton(buttonId) {
        const button = document.getElementById(buttonId);
        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Inatengeneza...';
    }

    document.getElementById('house_id').addEventListener('change', function() {
        const houseId = this.value;

        if (houseId) {
            fetch(`/get-rooms/${houseId}`)
                .then(response => response.json())
                .then(data => {
                    const roomSelect = document.getElementById('room_id');
                    roomSelect.innerHTML = '<option value="">Bonyeza hapa kuchagua chumba.....</option>';
                    data.rooms.forEach(room => {
                        roomSelect.innerHTML += `<option value="${room.id}" data-rent="${room.rent}">${room.room_name}</option>`;
                    });
                });
        }
    });

    document.getElementById('room_id').addEventListener('change', function() {
        const selectedRoom = this.options[this.selectedIndex];
        const rent = selectedRoom.getAttribute('data-rent');
        document.getElementById('rent_per_month').value = rent;

        updateRentDetails();
    });

    document.getElementById('contract_interval').addEventListener('input', updateRentDetails);
    document.getElementById('start_date').addEventListener('input', updateEndDate);
    document.getElementById('amount_paid').addEventListener('input', updateRentDetails);

    function updateEndDate() {
        const startDate = new Date(document.getElementById('start_date').value);
        const months = parseInt(document.getElementById('contract_interval').value);
        if (!isNaN(startDate) && months) {
            const endDate = new Date(startDate);
            endDate.setMonth(startDate.getMonth() + months);
            document.getElementById('end_date').value = endDate.toISOString().split('T')[0];
        }
    }

    function updateRentDetails() {
        const rentPerMonth = parseFloat(document.getElementById('rent_per_month').value) || 0;
        const contractInterval = parseInt(document.getElementById('contract_interval').value) || 0;
        const totalRent = rentPerMonth * contractInterval;
        document.getElementById('total_rent').value = totalRent;

        const amountPaid = parseFloat(document.getElementById('amount_paid').value) || 0;
        const amountRemaining = totalRent - amountPaid;
        document.getElementById('amount_remaining').value = amountRemaining;

        const amountError = document.getElementById('amount-error');
        const submitButton = document.getElementById('register-contract');
        if (amountPaid > totalRent) {
            amountError.style.display = 'block';
            submitButton.disabled = true;
        } else {
            amountError.style.display = 'none';
            submitButton.disabled = false;
        }
    }
</script>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        const totalRentInput = document.getElementById('total_rent');
        const amountPaidInput = document.getElementById('amount_paid');
        const amountRemainingInput = document.getElementById('amount_remaining');
        const makeContractButton = document.getElementById('make-contract');
        const amountError = document.getElementById('amount-error');

        amountPaidInput.addEventListener('input', function() {
            const totalRent = parseFloat(totalRentInput.value) || 0;
            const amountPaid = parseFloat(amountPaidInput.value) || 0;

            const amountRemaining = totalRent - amountPaid;
            amountRemainingInput.value = amountRemaining > 0 ? amountRemaining : 0;

            if (amountPaid > totalRent) {
                amountError.style.display = 'block';
                makeContractButton.disabled = true;
            } else {
                amountError.style.display = 'none';
                makeContractButton.disabled = false;
            }
        });
    });
</script>

@include('components.footer')