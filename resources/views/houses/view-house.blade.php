<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tazama Nyumba | Usimamizi wa Nyumba</title>
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
                        <h5 class="mb-1" style="text-align: center;">Tazama nyumba</h5>
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

                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr class="table-success">
                                    <th>Na</th>
                                    <th>Nyumba</th>
                                    <th>Mmiliki</th>
                                    <th>Eneo</th>
                                    <th>Msimamizi</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($houses as $house)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $house->house_name }}</td>
                                    <td>{{ $house->house_owner }}</td>
                                    <td>{{ $house->house_location }}</td>
                                    <td>{{ $house->supervisor ? $house->supervisor->supervisor_name : '-' }}</td>
                                    <td>
                                        <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#editSupervisorModal"
                                            data-house-id="{{ $house->id }}"
                                            data-house-name="{{ $house->house_name }}"
                                            data-house-owner="{{ $house->house_owner }}"
                                            data-house-location="{{ $house->house_location }}"
                                            data-supervisor-id="{{ $house->supervisor ? $house->supervisor->id : '' }}">
                                            <i class="fas fa-edit"></i> Badili
                                        </button>
                                    </td>

                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @if($houses->isEmpty())
                        <tr>
                            <td colspan="5" class="text-center">Hakuna wapangaji waliosajiliwa.</td>
                        </tr>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal for editing house details-->
<div class="modal fade" id="editSupervisorModal" tabindex="-1" role="dialog" aria-labelledby="editSupervisorModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editSupervisorModalLabel">Badili taarifa za nyumba</h5>
            </div>
            <div class="modal-body">
                <form action="{{ route('houses.update') }}" method="POST" id="updateHouseForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="house_id" id="house_id">
                    <div class="form-group">
                        <label for="house_name"><strong>1. Jina la nyumba:</strong></label>
                        <input type="text" name="house_name" id="house_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="house_owner"><strong>2. Mmiliki wa nyumba:</strong></label>
                        <input type="text" name="house_owner" id="house_owner" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="house_location"><strong>3. Eneo la nyumba:</strong></label>
                        <input type="text" name="house_location" id="house_location" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="supervisor_id"><strong>4. Chagua msimamizi mpya (au acha tupu):</strong></label>
                        <select name="supervisor_id" id="supervisor_id" class="form-control">
                            <option value="">Bonyeza hapa kuchagua jina...</option>
                            @foreach($supervisors as $supervisor)
                            <option value="{{ $supervisor->id }}">{{ $supervisor->supervisor_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Funga</button>
                        <button type="submit" class="btn btn-success"><i class="fas fa-check"></i> Badili</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Script to handle modal data and prevent multiple clicks -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const editButtons = document.querySelectorAll('[data-target="#editSupervisorModal"]');
    const modalElement = document.getElementById('editSupervisorModal');

    editButtons.forEach(button => {
        button.addEventListener('click', () => {
            const modal = new bootstrap.Modal(modalElement);

            document.getElementById('house_id').value = button.getAttribute('data-house-id');
            document.getElementById('house_name').value = button.getAttribute('data-house-name') || '';
            document.getElementById('house_owner').value = button.getAttribute('data-house-owner') || '';
            document.getElementById('house_location').value = button.getAttribute('data-house-location') || '';
            document.getElementById('supervisor_id').value = button.getAttribute('data-supervisor-id') || '';

            modal.show();
        });
    });

    modalElement.addEventListener('hidden.bs.modal', () => {
        document.getElementById('updateHouseForm').reset();
    });
});

</script>

@include('components.footer')