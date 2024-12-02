<head>
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ingia | Usimamizi wa Nyumba</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link href="{{ asset('css/welcome.css') }}" rel="stylesheet">
</head>

<body class="d-flex align-items-center justify-content-center min-vh-100 p-3">
    <div class="card-custom text-center w-100">

        <div class="mb-4">
            <i class="fas fa-user-circle text-black" style="font-size: 4rem;"></i>
        </div>

        <h1 class="h3 font-weight-bold text-black mb-4"> Ingia</h1>

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

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="form-group mb-4">
                <input type="email" name="email" id="email" placeholder="Jaza barua pepe" class="form-control form-control-custom">
            </div>
            <div class="form-group mb-4">
                <input type="password" name="password" id="password" placeholder="Jaza neno siri" class="form-control form-control-custom">
            </div>
            <div class="form-group mb-4">
                <button type="submit" class="btn btn-primary-custom w-100 py-2">
                    <i class="fas fa-sign-in-alt mr-2"></i> Ingia katika mfumo
                </button>
            </div>
        </form>

    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>