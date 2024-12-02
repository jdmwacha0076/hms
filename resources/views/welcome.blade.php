<head>
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Karibu | Usimamizi wa Nyumba</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link href="{{ asset('css/welcome.css') }}" rel="stylesheet">
</head>

<body class="d-flex align-items-center justify-content-center min-vh-100 p-3">

    <div class="card-custom text-center w-100">
        <div class="icon rotating">
            <i class="fas fa-home fa-2x"></i>
        </div>

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

        <br><br><br>
        
        <div class="d-flex flex-column flex-md-row justify-content-center">
            <a href="{{ route('login') }}" class="btn btn-primary-custom py-2 px-4 rounded-lg mb-3 mb-md-0 mr-md-3">
                <strong><i class="fas fa-sign-in-alt mr-2"></i> Ingia </strong>
            </a>
            <a href="{{ route('register') }}" class="btn btn-primary-custom py-2 px-4 rounded-lg mb-3 mb-md-0 mr-md-3">
                <strong><i class="fas fa-sign-in-alt mr-2"></i> Sajili </strong>
            </a>
        </div>

        <br>

    </div>

</body>