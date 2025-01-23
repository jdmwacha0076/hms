<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tazama Vyumba | Usimamizi wa Nyumba</title>
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
                        <h5 class="mb-1" style="text-align: center;">Nyumba na vyumba vilivyopo</h5>
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
                            <thead class="table-light">
                                <tr class="table-success">
                                    <th>Na:</th>
                                    <th>&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;Nyumba</th>
                                    <th>&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;Mmiliki</th>
                                    <th>&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;Eneo</th>
                                    <th>&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;Msimamizi</th>
                                    <th>&emsp;&emsp;&emsp;&emsp;Vyumba</th>
                                    <th>&emsp;&emsp;&emsp;&emsp;Kodi</th>
                                    <th>&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;Badili</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($houses as $house)
                                @if($house->rooms->isEmpty())
                                <tr class="table-warning">
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $house->house_name }}</td>
                                    <td>{{ $house->house_owner }}</td>
                                    <td>{{ $house->house_location }}</td>
                                    <td>{{ $house->supervisor ? $house->supervisor->supervisor_name : 'N/A' }}</td>
                                    <td colspan="3" class="text-center">
                                        <span class="text-danger">Nyumba hii kwa sasa haina vyumba.</span>
                                    </td>
                                </tr>
                                @else
                                @foreach($house->rooms as $room)
                                <tr>
                                    <td>{{ $loop->parent->iteration }}</td>
                                    <td>{{ $loop->first ? $house->house_name : '' }}</td>
                                    <td>{{ $loop->first ? $house->house_owner : '' }}</td>
                                    <td>{{ $loop->first ? $house->house_location : '' }}</td>
                                    <td>{{ $loop->first ? ($house->supervisor ? $house->supervisor->supervisor_name : 'N/A') : '' }}</td>
                                    <td>{{ $room->room_name }}</td>
                                    <td>{{ number_format($room->rent) }}</td>
                                    <td>
                                        <button class="btn btn-secondary btn-sm"
                                            data-toggle="modal"
                                            data-target="#editRoomModal"
                                            data-room-id="{{ $room->id }}"
                                            data-room-name="{{ $room->room_name }}"
                                            data-rent="{{ $room->rent }}">
                                            <i class="fas fa-edit"></i> Badili
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                                @endif
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

<!-- Modal to edit the room details -->
<div class="modal fade" id="editRoomModal" tabindex="-1" role="dialog" aria-labelledby="editRoomModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editRoomModalLabel">Badili taarifa za chumba husika</h5>
            </div>
            <div class="modal-body">
                <form id="editRoomForm" method="POST" action="{{ route('rooms.update', 'room_id') }}">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="room_id" id="room_id">

                    <div class="form-group">
                        <label for="room_name" class="font-weight-bold">1. Jaza jina jipya la chumba:</label>
                        <input type="text" class="form-control" name="room_name" id="room_name" required>
                    </div>

                    <div class="form-group">
                        <label for="rent" class="font-weight-bold">2. Kodi kwa mwezi:</label>
                        <input type="number" class="form-control" name="rent" id="rent" step="0.01" required>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal"><i class="fas fa-times"></i> Funga</button>
                        <button type="submit" id="submitBtn" class="btn btn-success btn-sm">
                            <i class="fas fa-check"></i> Badili
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Script to handle modal data and prevent multiple clicks -->
<script>
    $('#editRoomModal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        var roomId = button.data('room-id');
        var roomName = button.data('room-name');
        var rent = button.data('rent');

        var modal = $(this);
        modal.find('.modal-body #room_id').val(roomId);
        modal.find('.modal-body #room_name').val(roomName);
        modal.find('.modal-body #rent').val(rent);

        modal.find('#editRoomForm').attr('action', "{{ route('rooms.update', '') }}" + '/' + roomId);
    });

    $('#editRoomForm').on('submit', function(e) {
        var submitButton = $('#submitBtn');
        submitButton.prop('disabled', true);
        submitButton.html('<i class="fas fa-spinner fa-spin"></i> Inabadili...');
    });
</script>

@include('components.footer')