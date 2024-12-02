<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sajili Nyumba | Usimamizi wa Nyumba</title>
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
                        <h5 class="mb-1" style="text-align: center;">Sajili nyumba</h5>
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
                        <a href="{{ url('/view-rooms') }}" class="btn btn-success"><i class="fas fa-eye"></i> Tazama nyumba</a>
                    </div>

                    <form method="POST" action="{{ route('house.save') }}" onsubmit="disableButton('add-house')">
                        @csrf
                        <div class="form-group">
                            <label for="house_name" class="font-weight-bold">1. Jina la nyumba:</label>
                            <input type="text" class="form-control" name="house_name" placeholder="Jaza jina la nyumba kwa hapa" required>
                        </div>

                        <div class="form-group">
                            <label for="house_location" class="font-weight-bold">2. Eneo la nyumba:</label>
                            <input type="text" class="form-control" name="house_location" placeholder="Jaza eneo ambalo nyumba ipo" required>
                        </div>

                        <div class="form-group">
                            <label for="street_name" class="font-weight-bold">3. Mtaa nyumba ilipo:</label>
                            <input type="text" class="form-control" name="street_name" placeholder="Jaza jina la mtaa" required>
                        </div>

                        <div class="form-group">
                            <label for="plot_number" class="font-weight-bold">4. Kiwanja nyumba ilipo:</label>
                            <input type="text" class="form-control" name="plot_number" placeholder="Jaza eneo la kiwanja" required>
                        </div>

                        <div class="form-group">
                            <label for="house_owner" class="font-weight-bold">5. Mmiliki/Msimamizi wa nyumba:</label>
                            <input type="text" class="form-control" name="house_owner" placeholder="Jaza jina la mpangishaji" required>
                        </div>

                        <div class="form-group">
                            <label for="phone_number" class="font-weight-bold">5. Namba ya simu ya mmiliki/msimamizi:</label>
                            <input type="text" id="phone_number" class="form-control" name="phone_number" placeholder="Jaza namba ya mpangishaji" required>
                            <span id="phone_number_error"></span>
                        </div>

                        <div class="form-group">
                            <label for="supervisor_id" class="font-weight-bold">6. Chagua msimamizi msaidizi:</label>
                            <select name="supervisor_id" id="supervisor_id" class="form-control">
                                <option value="">Bonyeza hapa kuchagua msimamizi.....</option>
                                @foreach($supervisors as $supervisor)
                                <option value="{{ $supervisor->id }}">{{ $supervisor->supervisor_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="text-left">
                            <button type="submit" class="btn btn-dark" id="add-house"><i class="fas fa-save"></i> Kamilisha usajili wa nyumba</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<!--Script to make validation on the phone_number -->
<script>
    document.getElementById("phone_number").addEventListener("input", function(event) {
        var phoneNumber = event.target.value;
        var errorMessage = document.getElementById("phone_number_error");
        const button = document.getElementById('add-house');

        if (phoneNumber.charAt(0) === '0') {
            phoneNumber = '255' + phoneNumber.substring(1);
            event.target.value = phoneNumber;
        }

        if (!phoneNumber.startsWith('255')) {
            button.disabled = true;
            errorMessage.textContent = "Namba ya simu lazima ianze na 255.";
            errorMessage.style.color = "red";
        } else if (phoneNumber.length !== 12) {
            button.disabled = true;
            errorMessage.textContent = "Namba ya simu lazima iwe na tarakimu 12. Mfano: 255656345149";
            errorMessage.style.color = "red";
        } else {
            button.disabled = false;
            errorMessage.textContent = "";
        }
    });

    function disableButton(buttonId) {
        const button = document.getElementById(buttonId);
        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Inasajili...';
    }
</script>

@include('components.footer')