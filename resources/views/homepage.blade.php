<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nyumbani | Usimamizi wa Nyumba</title>
    <link href="{{ asset('css/homepage.css') }}" rel="stylesheet">
    <link href="{{ asset('css/styles.css') }}" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
</head>

<body>

    @include('components.navbar')

    <div class="clearfix">
        <div class="content">
            <div class="animated fadeIn">
                <div class="card mb-4" style="margin-bottom: -30px !important;">

                    <div class="cardheader">
                        <div class="card-header">
                            <h5 class="mb-1" style="text-align: center;">Dashibodi</h5>
                        </div>
                    </div>

                    <div class="container mt-4">
                        <div class="row">
                            <div class="col-md-6 col-sm-12 mb-4">
                                <div class="link-container">
                                    <div class="icon">
                                        <i class="fas fa-calendar-alt"></i>
                                    </div>
                                    <div class="link-text">Mikataba inayokaribia kuisha (Imebakiza wiki)</div>
                                    <table class="table table-striped table-bordered">
                                        <thead>
                                            <tr class="table-success">
                                                <th>Jina</th>
                                                <th>Nyumba</th>
                                                <th>Chumba</th>
                                                <th>Hadi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($contractsEndingSoon as $contract)
                                            <tr>
                                            <td>{{ $contract->tenant->tenant_name }}</td>
                                                <td>{{ $contract->room->house->house_name }}</td>
                                                <td>{{ $contract->room->room_name }}</td>
                                                <td>{{ \Carbon\Carbon::parse($contract->end_date)->format('Y-m-d') }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="col-md-6 col-sm-12 mb-4">
                                <div class="link-container">
                                    <div class="icon">
                                        <i class="fas fa-exclamation-triangle"></i>
                                    </div>
                                    <div class="link-text">Wapangaji ambao hawajamaliza kulipa kodi</div>
                                    <ul class="list-group mt-2">
                                        @foreach ($tenantsWithIncompletePayments as $index => $contract)
                                        <li class="list-group-item">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <strong>{{ $index + 1 }}. {{ $contract->tenant->tenant_name }}</strong>
                                                </div>
                                                <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#details-{{ $contract->id }}">
                                                    <i class="fas fa-chevron-down"></i>
                                                </button>
                                            </div>
                                            <div class="collapse" id="details-{{ $contract->id }}">
                                                <div class="mt-2">
                                                    <ul class="details-list">
                                                        <li><strong>Nyumba:</strong> {{ $contract->room->house->house_name }}</li>
                                                        <li><strong>Chumba:</strong> {{ $contract->room->room_name }}</li>
                                                        <li><strong>Kiasi baki:</strong> {{ number_format($contract->amount_remaining) }}</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 mb-4">
                                <div class="link-container">
                                    <div class="icon">
                                        <i class="fas fa-file-contract"></i>
                                    </div>
                                    <div class="col-12 mb-4">
                                        <div class="link-text">Jumla ya mikataba kwa kila mpangaji</div>

                                        <canvas id="contractsChart"></canvas>
                                    </div>
                                </div>
                                                <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                                                <script>
                                                const ctx = document.getElementById('contractsChart').getContext('2d');
                                                const tenantNames = @json($tenantsWithContractCount->pluck('tenant.tenant_name'));
                                                const contractCounts = @json($tenantsWithContractCount->pluck('contract_count'));

                                                const contractsChart = new Chart(ctx, {
                                                type: 'bar',
                                                data: {
                                                labels: tenantNames,
                                                datasets: [{
                                                label: 'Idadi ya mikataba',
                                                data: contractCounts,
                                                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                                                borderColor: 'rgba(54, 162, 235, 1)',
                                                borderWidth: 1
                                                }]
                                                },
                                                options: {
                                                scales: {
                                                y: {
                                                beginAtZero: true
                                                }
                                                }
                                                }
                                                });
                                                </script>
                                    </div>
                                </div>

                        <div class="row">
                            <div class="col-12 mb-4">
                                <div class="link-container">
                                    <div class="icon">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                    <div class="link-text">Wadaiwa sugu</div>
                                    <ul class="list-group mt-2">
                                        @foreach ($overdueRentPayments as $overdue)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-1"><strong>Mpangaji:</strong> {{ $overdue->tenant->tenant_name }}</h6>
                                                <p class="mb-0"><strong>Nyumba:</strong> {{ $overdue->room->house->house_name }}</p>
                                                <p class="mb-0"><strong>Chumba:</strong> {{ $overdue->room->room_name }}</p>
                                            </div>
                                            <span class="badge bg-danger rounded-pill" style="font-size: 0.8rem; padding: 0.6em 1.2em;">{{ number_format($overdue->amount_remaining) }}</span>
                                        </li>
                                        <li class="list-group-item">
                                            <p><strong>Uliisha:</strong> {{ $overdue->end_date }}</p>
                                        </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 mb-4">
                                <div class="link-container">
                                    <div class="icon">
                                        <i class="fas fa-door-open fa-2x text-dark"></i>
                                    </div>
                                    <h3 class="link-text mt-2">Vyumba visivyo na wapangaji</h3>
                                    <p class="text-muted">Idadi ya vyumba vilivyo wazi: {{ number_format($vacantRooms->count()) }}</p>
                                    <div class="card-deck">
                                        @foreach ($vacantRooms as $room)
                                        <div class="card text-center card-hover mb-3">
                                            <div class="card-body">
                                                <h5 class="card-title">{{ $room->house->house_name }}</h5>
                                                <p class="card-text">Chumba: {{ $room->room_name }}</p>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 mb-4">
                                <div class="link-container">
                                    <div class="icon">
                                        <i class="fas fa-medal"></i>
                                    </div>
                                    <div class="link-text mb-3">Wapangaji (5) wenye mikataba mirefu</div>
                                    <div class="accordion" id="tenantsAccordion">
                                        @foreach ($longestTenureTenants as $tenant)
                                        <div class="card">
                                            <div class="card-header" id="heading-{{ $tenant->id }}">
                                                <h5 class="mb-0">
                                                    <button class="btn btn-success" type="button" data-toggle="collapse" data-target="#collapse-{{ $tenant->id }}" aria-expanded="true" aria-controls="collapse-{{ $tenant->id }}">
                                                        {{ $tenant->tenant->tenant_name }}
                                                    </button>
                                                </h5>
                                            </div>
                                            <div id="collapse-{{ $tenant->id }}" class="collapse" aria-labelledby="heading-{{ $tenant->id }}" data-parent="#tenantsAccordion">
                                                <div class="card-body">
                                                    <p><strong>Kuanzia:</strong> {{ $tenant->start_date }}</p>
                                                    <p><strong>Kuisha:</strong> {{ $tenant->end_date }}</p>
                                                    <p><strong>Nyumba:</strong> {{ $tenant->room->house->house_name }}</p>
                                                    <p><strong>Chumba:</strong> {{ $tenant->room->room_name }}</p>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-4">
                                <div class="card text-center card-hover bg-vacant">
                                    <div class="card-body">
                                        <h5 class="card-title">Jumla ya vyumba ambavyo vipo wazi</h5>
                                        <p class="card-text display-4">{{ $vacantRooms->count() }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-4">
                                <div class="card text-center card-hover bg-overdue">
                                    <div class="card-body">
                                        <h5 class="card-title">Jumla ya madeni yaliyopitiliza muda</h5>
                                        <p class="card-text display-4">{{ $overdueRentPayments->count() }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-4">
                                <div class="card text-center card-hover bg-tenants">
                                    <div class="card-body">
                                        <h5 class="card-title">Jumla ya wapangaji walipo kwa sasa</h5>
                                        <p class="card-text display-4">{{ $totalTenants }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>    
                    </div>

                </div>
            </div>
        </div>
    </div>

    @include('components.footer')
