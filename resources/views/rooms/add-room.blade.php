<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sajili Chumba | Usimamizi wa Nyumba</title>
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
                        <h5 class="mb-1" style="text-align: center;">Sajili chumba</h5>
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
                        <a href="{{ url('/view-rooms') }}" class="btn btn-success btn-sm"><i class="fas fa-eye"></i> Tazama vyumba</a>
                    </div>

                    <form method="POST" action="{{ route('room.save') }}" onsubmit="disableButton('add-room')">
                        @csrf
                        <div class="form-group">
                            <label for="house_id" class="font-weight-bold">1. Chagua nyumba:</label>
                            <select class="form-control" name="house_id" required>
                                <option value="">Bonyeza hapa kuchagua jina la nyumba</option>
                                @foreach($houses as $house)
                                <option value="{{ $house->id }}">{{ $house->house_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="room_name" class="font-weight-bold">2. Jina la chumba:</label>
                            <input type="text" class="form-control" name="room_name" placeholder="Jaza jina la chumba kwa hapa" required>
                        </div>

                        <div class="form-group">
                            <label for="rent" class="font-weight-bold">3. Kodi kwa mwezi:</label>
                            <input type="number" class="form-control" name="rent" min="1" placeholder="Jaza kodi ya chumba kwa mwezi" required>
                        </div>

                        <div class="text-left">
                            <button type="submit" class="btn btn-dark btn-sm" id="add-room"><i class="fas fa-save"></i> Kamilisha usajili wa chumba</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Script to handle multiple submission -->
<script>
    function disableButton(buttonId) {
        const button = document.getElementById(buttonId);
        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Inasajili...';
    }
</script>

@include('components.footer')