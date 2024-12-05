<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tazama Wapangaji | Usimamizi wa Nyumba</title>
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
                        <h5 class="mb-1" style="text-align: center;">Tazama wapangaji wako</h5>
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
                        <table class="table table-striped table-bordered" id="view-tenants">
                            <thead>
                                <tr class="table-success">
                                    <th>Na:</th>
                                    <th>&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;Jina</th>
                                    <th>&emsp;&emsp;&emsp;&emsp;&emsp;Simu</th>
                                    <th>&emsp;&emsp;ID</th>
                                    <th>&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;Namba</th>
                                    <th>&emsp;&emsp;&emsp;&emsp;&emsp;Badili</th>
                                    <th>&emsp;&emsp;&emsp;&emsp;&emsp;Futa</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tenants as $index => $tenant)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $tenant->tenant_name }}</td>
                                    <td>{{ $tenant->phone_number }}</td>
                                    <td>{{ $tenant->id_type }}</td>
                                    <td>{{ $tenant->id_number }}</td>
                                    <td>
                                        <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#editModal"
                                            data-id="{{ $tenant->id }}"
                                            data-name="{{ $tenant->tenant_name }}"
                                            data-phone="{{ $tenant->phone_number }}"
                                            data-id-type="{{ $tenant->id_type }}"
                                            data-id-number="{{ $tenant->id_number }}">
                                            <i class="fas fa-edit"></i> Badili
                                        </button>

                                    </td>
                                    <td>
                                        <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteModal"
                                            data-id="{{ $tenant->id }}" data-name="{{ $tenant->tenant_name }}"><i class="fas fa-trash"></i> Futa</button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">Hakuna wapangaji waliosajiliwa.</td>
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

<!-- Edit Modal for tenant details -->
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
                    <input type="hidden" name="id" id="edittenantId">
                    <div class="form-group">
                        <label for="tenantName" class="font-weight-bold">1. Jina la mpangaji:</label>
                        <input type="text" class="form-control" id="tenantName" name="tenant_name" required>
                    </div>
                    <div class="form-group">
                        <label for="tenantPhone" class="font-weight-bold">2. Namba ya simu:</label>
                        <input type="text" class="form-control" id="phone" name="tenant_phone" required pattern="[0-9]{12}">
                        <small id="phone_number_error" class="form-text"></small>
                    </div>
                    <div class="form-group">
                        <label for="idType" class="font-weight-bold">3. Aina ya Kitambulisho:</label>
                        <input type="text" class="form-control" id="idType" name="id_type" required>
                    </div>
                    <div class="form-group">
                        <label for="idNumber" class="font-weight-bold">4. Namba ya Kitambulisho:</label>
                        <input type="text" class="form-control" id="idNumber" name="id_number" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Funga</button>
                    <button type="submit" class="btn btn-success"><i class="fas fa-check"></i> Badili</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Delete Modal for tenant details -->
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
                    <input type="hidden" name="id" id="deletetenantId">
                    <p>Una uhakika kuwa unataka kufuta taarifa za mpangaji huyu?</p>
                    <p><strong>Jina la mpangaji:</strong> <span id="deletetenantName"></span></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Funga</button>
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
        var idType = button.data('id-type');
        var idNumber = button.data('id-number');

        var modal = $(this);
        modal.find('#edittenantId').val(id);
        modal.find('#tenantName').val(name);
        modal.find('#phone').val(phone);
        modal.find('#idType').val(idType);
        modal.find('#idNumber').val(idNumber);

        modal.find('form').attr('action', "{{ url('/tenant') }}/" + id);
    });

    $('#deleteModal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        var id = button.data('id');
        var name = button.data('name');

        var modal = $(this);
        modal.find('#deletetenantId').val(id);
        modal.find('#deletetenantName').text(name);

        modal.find('form').attr('action', "{{ url('/tenant') }}/" + id);
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
        $('#view-tenants').DataTable({
            "paging": false,
            "searching": true
        });
    });
</script>

@include('components.footer')