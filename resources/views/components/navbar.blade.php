<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

    <style>
        .bg-custom-color {
            background-color: #40E0D0;
        }

        .navbar-brand {
            color: black !important;
            font-weight: bold;
        }

        .navbar-brand .fa-cubes {
            color: #000000;
            margin-right: 5px;
            font-size: 28px;
        }

        .navbar-nav .nav-link {
            color: black !important;
            font-weight: bold;
            font-size: 18px;
        }

        .navbar-nav .nav-item.dropdown .dropdown-menu .dropdown-item {
            color: #333;
            font-weight: normal;
        }

        .navbar-toggler-icon-custom {
            background-image: url('data:image/svg+xml;charset=utf8,%3Csvg viewBox="0 0 30 30" xmlns="http://www.w3.org/2000/svg"%3E%3Cpath stroke="rgba%280, 0, 0, 0.7%29" stroke-width="2" stroke-linecap="round" stroke-miterlimit="10" d="M4 7h22M4 15h22M4 23h22"/%3E%3C/svg%3E');
        }

        .btn-dark {
            background-color: #000 !important;
            border-color: #000 !important;
        }

        .dropdown-menu-right {
            right: 0;
            left: auto;
        }

        .nav-link:hover {
            color: #fff !important;
            background-color: #000;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .dropdown-item:hover {
            background-color: #40E0D0;
            color: #fff;
        }

        @media (max-width: 992px) {
            .dropdown-menu {
                position: static;
                float: none;
                box-shadow: none;
            }
        }

        .nav-link.user-name {
            background-color: #f8f9fa !important;
            border: 1px solid #ddd !important;
            border-radius: 20px;
            padding: 5px 15px !important;
            color: #333 !important;
        }

        .nav-link.user-name:hover {
            background-color: #e9ecef !important;
            color: #000 !important;
        }
    </style>

</head>

<body>
    <nav class="navbar navbar-expand-lg bg-custom-color shadow-md">

        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-home"></i> Kiwango Complex
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon navbar-toggler-icon-custom"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/homepage') }}">Nyumbani</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="contractsDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Mikataba
                        </a>
                        <div class="dropdown-menu" aria-labelledby="contractsDropdown">
                            <a class="dropdown-item" href="{{ url('/make-contract') }}">Tengeneza mkataba</a>
                            <a class="dropdown-item" href="{{ url('/view-contracts') }}">Tazama mikataba</a>
                            <a class="dropdown-item" href="{{ url('/export-contracts') }}">Pakua mikataba</a>
                            <a class="dropdown-item" href="{{ url('/upload-contracts') }}">Pakia mikataba</a>
                            <a class="dropdown-item" href="{{ url('/completed-contracts') }}">Mikataba iliyokamilika</a>
                        </div>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="housesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Nyumba
                        </a>
                        <div class="dropdown-menu" aria-labelledby="housesDropdown">
                            <a class="dropdown-item" href="{{ url('/add-house') }}">Sajili nyumba</a>
                            <a class="dropdown-item" href="{{ url('/view-house') }}">Tazama nyumba</a>
                        </div>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="roomsDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Chumba
                        </a>
                        <div class="dropdown-menu" aria-labelledby="roomsDropdown">
                            <a class="dropdown-item" href="{{ url('/add-room') }}">Sajili chumba</a>
                            <a class="dropdown-item" href="{{ url('/view-rooms') }}">Tazama vyumba</a>
                        </div>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="tenantsDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Wapangaji
                        </a>
                        <div class="dropdown-menu" aria-labelledby="tenantsDropdown">
                            <a class="dropdown-item" href="{{ url('/add-tenant') }}">Sajili mpangaji</a>
                            <a class="dropdown-item" href="{{ url('/view-tenants') }}">Tazama wapangaji</a>
                        </div>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="supervisorsDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Wasimamizi
                        </a>
                        <div class="dropdown-menu" aria-labelledby="supervisorsDropdown">
                            <a class="dropdown-item" href="{{ url('/add-supervisors') }}">Sajili msimamizi</a>
                            <a class="dropdown-item" href="{{ url('/view-supervisors') }}">Tazama wasimamizi</a>
                        </div>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle user-name" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            {{ Auth::user()->name }}
                        </a>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
                            <a class="dropdown-item" href="{{ url('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"> Toka nje</a>
                            <a class="dropdown-item" href="{{ url('/user-management') }}">Usimamizi wa watumiaji</a>
                            <form id="logout-form" action="{{ url('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </div>
                    </li>
                </ul>
            </div>
        </div>

    </nav>

    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>