<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usimamizi wa Watumiaji | Usimamizi wa Nyumba</title>
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
                        <h5 class="mb-1" style="text-align: center;"><i class=""></i>Tazama watumiaji wote</h5>
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

                    <div class="d-flex justify-content-left mb-3">
                        <td><a href="{{ url('/add-user') }}" class="btn btn-success"><i class="fas fa-user-plus"></i> Ongeza mtumiaji</a></td>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr class="table-success">
                                    <th>Na:</th>
                                    <th>&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;Jina</th>
                                    <th>&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;Barua Pepe</th>
                                    <th>&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;Jukumu</th>
                                    <th>&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;Usajili</th>
                                    <th>&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;Badili</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $user)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        {{ $user->user_role == 1 ? 'Admin' : ($user->user_role == 2 ? 'Msomaji' : 'N/A') }}
                                    </td>
                                    <td>{{ $user->created_at }}</td>
                                    <td>
                                        <button type="button" class="btn btn-success" data-toggle="modal" data-target="#updateform" data-userid="{{ $user->id }}" data-name="{{ $user->name }}" data-email="{{ $user->email }}" @if($user->id == $loggedInUserId || $user->status == 'disabled') disabled @endif>
                                            <i class="fas fa-edit"></i> Badili
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal and script to update the user name and email -->
<div class="modal fade" id="updateform" tabindex="-1" aria-labelledby="updateformLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateformLabel">Badili jina la mtumiaji</h5>
            </div>
            <div class="modal-body">
                <form action="{{ route('update-user') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id" id="id">

                    <div class="form-group">
                        <label for="name" class="font-weight-bold">Badilisha jina:</label>
                        <input type="text" name="name" id="name" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="email" class="font-weight-bold"> Badilisha barua pepe:</label>
                        <input type="text" name="email" id="email" class="form-control" readonly required>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-dark" data-dismiss="modal"><i class="fas fa-times"></i> Funga</button>
                        <button type="submit" class="btn btn-success"><i class="fas fa-check"></i> Badili</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $('#updateform').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        var userId = button.data('userid');
        var userName = button.data('name');
        var userEmail = button.data('email');

        var modal = $(this);
        modal.find('.modal-body #id').val(userId);
        modal.find('.modal-body #name').val(userName);
        modal.find('.modal-body #email').val(userEmail);
    });
</script>

@include('components.footer')