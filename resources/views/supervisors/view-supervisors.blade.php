<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tazama Wasimamizi | Usimamizi wa Nyumba</title>
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
    <link href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link href="{{ asset('css/styles.css') }}" rel="stylesheet">
</head>

@include('components.navbar')

<div class="clearfix">
    <div class="content">
        <div class="animated fadeIn">
            <div class="card mb-4" style="margin-bottom: -30px !important;">

                <div class="cardheader">
                    <div class="card-header">
                        <h5 class="mb-1" style="text-align: center;">Tazama wasimamizi wako</h5>
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
                        <table class="table table-striped table-bordered" id="view-supervisors">
                            <thead>
                                <tr class="table-success">
                                    <th>Na:</th>
                                    <th>Jina</th>
                                    <th>Namba</th>
                                    <th>Nyumba</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($supervisors as $supervisor)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $supervisor->supervisor_name }}</td>
                                    <td>{{ $supervisor->phone_number }}</td>
                                    <td>
                                        @if ($supervisor->houses->isEmpty())
                                        Hajapangiwa nyumba
                                        @else
                                        <ul>
                                            @foreach ($supervisor->houses as $house)
                                            <li>{{ $house->house_name }}</li>
                                            @endforeach
                                        </ul>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center">Hakuna msimamizi yoyote</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Edit Modal for supervisor details -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="" id="editForm" onsubmit="return disableSubmitButton(this)">
            @csrf
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Badili taarifa za mpangaji huyu</h5>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="editsupervisorId">
                    <div class="form-group">
                        <label for="supervisorName" class="font-weight-bold">1. Jina la mpangaji:</label>
                        <input type="text" class="form-control" id="supervisorName" name="supervisor_name" required>
                    </div>
                    <div class="form-group">
                        <label for="supervisorPhone" class="font-weight-bold">2. Namba ya simu:</label>
                        <input type="text" class="form-control" id="phone" name="supervisor_phone" required pattern="[0-9]{12}">
                        <small id="phone_number_error" class="form-text"></small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-dark" data-dismiss="modal"><i class="fas fa-times"></i> Funga</button>
                    <button type="submit" class="btn btn-success"><i class="fas fa-check"></i> Badili</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Delete Modal for supervisor details -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="" id="deleteForm" onsubmit="return disableSubmitButton(this)">
            @csrf
            @method('DELETE')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Hakiki taarifa za mpangaji kabla ya kufuta</h5>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="deletesupervisorId">
                    <p>Una uhakika kuwa unataka kufuta taarifa za mpangaji huyu?</p>
                    <p><strong>Jina la mpangaji:</strong> <span id="deletesupervisorName"></span></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-dark" data-dismiss="modal"><i class="fas fa-times"></i> Funga</button>
                    <button type="submit" class="btn btn-danger"><i class="fas fa-check"></i> Futa</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Script to disable double-clicking on submit -->
<script>
    function disableSubmitButton(form) {
        const submitButton = form.querySelector('button[type="submit"]');
        submitButton.disabled = true;
        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Inashughulikia...';
        return true;
    }
</script>

<!-- Script to populate the edit and delete modals -->
<script>
    $('#editModal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        var id = button.data('id');
        var name = button.data('name');
        var phone = button.data('phone');

        var modal = $(this);
        modal.find('#editsupervisorId').val(id);
        modal.find('#supervisorName').val(name);
        modal.find('#phone').val(phone);

        modal.find('form').attr('action', "{{ url('/supervisor') }}/" + id);
    });

    $('#deleteModal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        var id = button.data('id');
        var name = button.data('name');

        var modal = $(this);
        modal.find('#deletesupervisorId').val(id);
        modal.find('#deletesupervisorName').text(name);

        modal.find('form').attr('action', "{{ url('/supervisor') }}/" + id);
    });
</script>

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

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>
<script>
    $(document).ready(function() {
        $('#view-supervisors').DataTable({
            "paging": false,
            "searching": true
        });
    });
</script>

@include('components.footer')