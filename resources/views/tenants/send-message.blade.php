<head>
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tume Ujumbe | Usimamizi wa Nyumba</title>
    <link href="{{ asset('css/styles.css') }}" rel="stylesheet">
</head>

@include('components.navbar')

<div class="clearfix">
    <div class="content">
        <div class="animated fadeIn">
            <div class="card mb-4" style="margin-bottom: -30px !important;">

                <div class="cardheader">
                    <div class="card-header">
                        <h5 class="mb-1" style="text-align: center;"><i class=""></i>Tuma ujumbe kwa wateja</h5>
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

                        @if($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                    </div>

                    <div id="statusMessage"></div>

                    <form id="messageForm">
                        @csrf
                        <div class="form-group">
                            <label for="message" class="font-weight-bold">Andika ujumbe wako hapa:</label>
                            <textarea id="message" name="message" class="form-control" rows="5" required></textarea>
                        </div>

                        <div class="form-group text-center">
                            <button type="button" class="btn btn-dark" data-toggle="modal" data-target="#reviewModal">
                                <i class="fas fa-paper-plane"></i> Tuma ujumbe
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal and script to review the message -->
<div class="modal fade" id="reviewModal" tabindex="-1" role="dialog" aria-labelledby="reviewModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reviewModalLabel">Tafadhali soma na hakiki ujumbe kabla ya kutuma</h5>
            </div>
            <div class="modal-body">
                <p><strong>Ujumbe unaotaka kutuma:</strong></p>
                <p id="reviewMessage"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-dark" data-dismiss="modal"><i class="fas fa-times"></i> Badili</button>
                <button type="button" class="btn btn-success" id="confirmSendButton"><i class="fas fa-check"></i> Tuma</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        $('#reviewModal').on('show.bs.modal', function() {
            const message = document.getElementById('message').value;
            document.getElementById('reviewMessage').innerText = message;
        });

        document.getElementById('confirmSendButton').addEventListener('click', function() {
            const message = document.getElementById('message').value;
            $('#reviewModal').modal('hide');
            sendMessagesToAllMembers(message);
        });
    });

    function sendMessagesToAllMembers(message) {
        const api_key = "{{ $apiKey }}";
        const secret_key = "{{ $secretKey }}";

        let successCount = 0;
        let failureCount = 0;
        let totalMessages = 0;

        $.ajax({
            type: 'GET',
            url: '/customers/phone-numbers-names',
            success: function(response) {
                if (response.members && response.members.length > 0) {
                    totalMessages = response.members.length;

                    response.members.forEach((member, index) => {
                        const personalizedMessage = `Habari ndugu ${member.customer_name}\n\n${message}`;
                        const postData = {
                            'source_addr': 'BOBTechWave',
                            'encoding': 0,
                            'schedule_time': '',
                            'message': personalizedMessage,
                            'recipients': [{
                                'recipient_id': index + 1,
                                'dest_addr': member.customer_phone
                            }]
                        };

                        sendSMS(postData, api_key, secret_key, () => {
                            successCount++;
                            checkCompletion();
                        }, () => {
                            failureCount++;
                            checkCompletion();
                        });
                    });
                } else {
                    alert('Hakuna namba za simu zilizopatikana');
                }
            },
            error: function(error) {
                console.error(error);
                alert('Imeshindwa kupata namba za simu za wateja.');
            }
        });

        function checkCompletion() {
            if (successCount + failureCount === totalMessages) {
                const statusMessage = `Ujumbe umetumwa kikamilifu kwa wateja ${successCount}.\n Na umeshindwa kutuma ujumbe kwa wateja ${failureCount}.`;
                document.getElementById('statusMessage').innerText = statusMessage;
                document.getElementById('statusMessage').className = 'alert alert-success';
            }
        }
    }

    function sendSMS(postData, api_key, secret_key, onSuccess, onFailure) {
        $.ajax({
            type: 'POST',
            url: 'https://apisms.beem.africa/v1/send',
            beforeSend: function(xhr) {
                xhr.setRequestHeader('Authorization', 'Basic ' + btoa(api_key + ':' + secret_key));
                xhr.setRequestHeader('Content-Type', 'application/json');
            },
            data: JSON.stringify(postData),
            success: function(response) {
                if (onSuccess) onSuccess();
            },
            error: function(error) {
                if (onFailure) onFailure();
            }
        });
    }
</script>

</body>

@include('components.footer')