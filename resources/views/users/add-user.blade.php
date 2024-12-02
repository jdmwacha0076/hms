<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ongeza Mtumiaji| Usimamizi wa Nyumba</title>
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
                        <h5 class="mb-1" style="text-align: center;">Sajili mtumiaji</h5>
                    </div>
                </div>

                <div class="panel-body" style="padding: 10px;">
                    <form action="{{ route('add-user') }}" method="POST" onsubmit="disableUpdateButton('add-user')">
                        @csrf

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

                            @if($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif
                        </div>

                        <div class="form-group">
                            <label for="name" class="font-weight-bold"> 1. Jaza jina la mtumiaji:</label>
                            <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}"
                                required>
                        </div>

                        <div class="form-group">
                            <label for="email" class="font-weight-bold"> 2. Jaza barua pepe:</label>
                            <input type="text" name="email" id="email" class="form-control" value="{{ old('email') }}"
                                required>
                        </div>

                        <div class="form-group mb-4">
                            <label for="phone_number" class="font-weight-bold"> 3. Jaza namba ya simu:</label>
                            <input type="text" name="phone_number" id="phone" class="form-control form-control-custom" placeholder="Jaza namba ya simu" pattern="[0-9]{12}">
                            <small id="phone_number_error" class="form-text"></small>
                        </div>

                        <div class="form-group mb-4">
                            <select id="user_role" class="form-control form-control-custom @error('user_role') is-invalid @enderror" name="user_role" required>
                                <option value="" selected disabled>Chagua aina ya mtumiaji</option>
                                <option value="1">Admin</option>
                                <option value="2">Msomaji</option>
                            </select>
                            @error('user_role')
                            <span class="invalid-feedback" btn btn-success="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="password" class="font-weight-bold"> 4. Jaza neno siri:</label>
                            <input type="password" name="password" id="password"
                                class="form-control form-control-custom" required>
                        </div>

                        <div class="form-group">
                            <label for="password_confirmation" class="font-weight-bold"> 5. Rudia neno siri:</label>
                            <input type="password" name="password_confirmation" id="password_confirmation"
                                class="form-control form-control-custom" required>
                        </div>

                        <div class="text-left">
                            <button type="submit" class="btn btn-dark w-100 py-2" id="add-user">
                                <i class="fas fa-user-plus mr-2"></i> Kamilisha usajili wa mtumiaji
                            </button>
                        </div>
                        </orm>

                </div>

            </div>
        </div>
    </div>
</div>

<!--Script to ensure that the first numbers starts with 0 -->
<script>
    document.getElementById("phone").addEventListener("input", function(event) {
        var phoneNumber = event.target.value;
        var errorMessage = document.getElementById("phone_number_error");

        if (phoneNumber.charAt(0) === '0') {
            phoneNumber = '255' + phoneNumber.substring(1);
            event.target.value = phoneNumber;
        }

        if (phoneNumber.length !== 12) {
            errorMessage.textContent = "Namba ya simu lazima iwe na tarakimu 12. Mfano: 255656345149";
            errorMessage.style.color = "red";
        } else {
            errorMessage.textContent = "";
        }
    });
</script>

<script>
    function disableUpdateButton(buttonId) {
        const button = document.getElementById(buttonId);
        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Inamsajili...';
    }
</script>

@include('components.footer')