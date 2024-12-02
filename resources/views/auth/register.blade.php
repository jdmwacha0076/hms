<head>
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jisajili | Usimamizi wa Nyumba</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link href="{{ asset('css/welcome.css') }}" rel="stylesheet">
</head>

<body class="d-flex align-items-center justify-content-center min-vh-100 p-3">
    <div class="card-custom text-center w-100">

        <div class="mb-4">
            <i class="fas fa-user-plus text-black" style="font-size: 4rem;"></i>
        </div>

        <h1 class="h3 font-weight-bold text-black mb-4">Fanya Usajili</h1>

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
                @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
                @endforeach
            </div>
            @endif
        </div>

        <form method="POST" action="{{ route('register') }}">
            @csrf
            <div class="form-group mb-4">
                <input type="text" name="name" id="name" placeholder="Jaza jina" class="form-control form-control-custom">
            </div>
            <div class="form-group mb-4">
                <input type="email" name="email" id="email" placeholder="Jaza barua pepe" class="form-control form-control-custom">
            </div>
            <div class="form-group mb-4">
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
            <div class="form-group mb-4">
                <input type="password" name="password" id="password" placeholder="Jaza neno siri" class="form-control form-control-custom">
            </div>
            <div class="form-group mb-4">
                <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Rudia neno siri" class="form-control form-control-custom">
            </div>
            <div class="form-group mb-4">
                <button type="submit" class="btn btn-primary-custom w-100 py-2">
                    <i class="fas fa-user-plus mr-2"></i> Kamilisha usajili
                </button>
            </div>
        </form>

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

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
