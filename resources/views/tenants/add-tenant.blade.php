<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sajili Wapangaji | Usimamizi wa Nyumba</title>
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
                        <h5 class="mb-1" style="text-align: center;">Sajili mpangaji</h5>
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
                        <a href="{{ url('/view-tenants') }}" class="btn btn-success btn-sm"><i class="fas fa-eye"></i> Tazama wapangaji</a>
                    </div>

                    <form method="POST" action="{{ route('tenant.save') }}" onsubmit="disableButton('add-tenant')">
                        @csrf
                        <div class="form-group">
                            <label for="tenant_name" class="font-weight-bold">1. Jina la mpangaji:</label>
                            <input type="text" class="form-control" name="tenant_name" placeholder="Jaza jina la mpangaji kwa hapa" required>
                        </div>

                        <div class="form-group">
                            <label for="phone" class="font-weight-bold">2. Namba ya simu:</label>
                            <input type="text" id="phone" class="form-control" name="phone_number" placeholder="Jaza na namba yake ya simu" required>
                            <span id="phone_number_error"></span>
                        </div>

                        <div class="form-group">
                            <label for="business_name" class="font-weight-bold">3. Jina la biashara:</label>
                            <input type="text" class="form-control" name="business_name" placeholder="Jaza jina la biashara" required>
                        </div>

                        <div class="form-group">
                            <label for="id_type" class="font-weight-bold">4. Aina ya kitambulisho:</label>
                            <input type="text" class="form-control" name="id_type" placeholder="Jaza aina ya kitambulisho" required>
                        </div>

                        <div class="form-group">
                            <label for="id_number" class="font-weight-bold">5. Namba ya kitambulisho:</label>
                            <input type="text" class="form-control" name="id_number" placeholder="Jaza namba ya kitambulisho" required>
                        </div>

                        <div class="text-left">
                            <button type="submit" class="btn btn-dark btn-sm" id="add-tenant"><i class="fas fa-save"></i> Kamilisha usajili wa mpangaji</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!--Script to make validation on the phone_number -->
<script>
    document.getElementById("phone").addEventListener("input", function(event) {
        var phoneNumber = event.target.value;
        var errorMessage = document.getElementById("phone_number_error");
        const button = document.getElementById('add-tenant');

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
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Inamsajili...';
    }
</script>

@include('components.footer')