@extends('layouts.app')

@section('content')
<style>
    .video-preview {
        width: 100px; /* Default width */
        height: 100px; /* Default height */
        border-radius: 50%; /* Rounded shape */
        overflow: hidden; /* Hide overflow */
        display: flex;
        justify-content: center;
        align-items: center;
        background-color: #f0f0f0; /* Default background color */
    }

    /* Adjust size for small preview */
    .video-preview.medium {
        width: 100px;
        height: 100px;
    }

    /* Adjust size for large preview */
    .video-preview.large {
        width: 200px;
        height: 200px;
    }

    .video-preview video {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
</style>
<div class="container">
    <div class="row mb-2">
        <div class="col">
            <button id="queue-control" class="btn btn-primary"></button> <!-- Start/Stop Queue Button -->
        </div>
    </div>
    <div class="row">
        <div class="col-md">
            <h2>Waitlist</h2>
            <div class="waiting mb-2">

            </div>
        </div>
        <div class="col-md">
            <h2>Now Playing</h2>
            <div class="mb-2  nowPlaying">
            </div>
        </div>
    </div>
</div>
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

<script>
var pusher = new Pusher('60de59064bcf7cfb6d63', {cluster: 'ap1'});
const videos = @json($videos);
var isQueueRunning = {{ $queue ? 'true' : 'false' }};
console.log(isQueueRunning);
updateQueueControlText();

// Function to update the text of the queue control button
function updateQueueControlText() {
    var queueControl = $('#queue-control');
    if (isQueueRunning) {
        queueControl.text('Stop Queue');
    } else {
        queueControl.text('Start Queue');
    }
}
function startQueue() {
        // Your start queue logic here
        console.log("Queue started");
        // Change button text to "Stop Queue"
        $('#queue-control').text("Stop Queue");
        // Update queue state
        isQueueRunning = true;
                        $('.nowPlaying').empty();

        $.ajax({
                    url: '{{ route('start') }}',
                    type: 'POST',
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    data: {
                        value: true
                    },
                    success: function(data) {
                        // data['playing'].forEach(function(video) {
                        //         setTimeout(function() {
                        //             AddCardToPlaying(video);
                        //             removeCardFromWaiting(video['id']);
                        //     }, 3000); // Wait for 3 seconds (3000 milliseconds) before executing

                        //     });
                    },
                    error: function(xhr, status, error) {
                        // Handle error response if needed
                    }
                });
    }

    // Stop Queue function
    function stopQueue() {
        // Your stop queue logic here
        console.log("Queue stopped");
        // Change button text to "Start Queue"
        $('#queue-control').text("Start Queue");
        // Update queue state
        isQueueRunning = false;
    }

videos.forEach(element => {
           AddCardToWaiting(element);
        });
function updateProgressBar(videoId, playtimeSeconds) {
    var interval = setInterval(function() {
        // Get the current progress value
        var currentValue = $('#progress-bar-' + videoId).attr('aria-valuenow');
        // Increment the progress value by 1 second
        currentValue++;
        // Update the progress bar width
        $('#progress-bar-' + videoId).css('width', (currentValue / playtimeSeconds) * 100 + '%');
        // Update the aria-valuenow attribute
        $('#progress-bar-' + videoId).attr('aria-valuenow', currentValue);

        // Check if progress bar reached the end
        if (currentValue >= playtimeSeconds) {
            clearInterval(interval); // Stop the interval
            removeCardFromPlaying(videoId);
            if(isQueueRunning == true){
                var firstCardId = $('.waiting .card:first').data('id');
                console.log(firstCardId);
                moveVideoToPlaying(firstCardId);

            } // Move the video to the "done" section
        }
    }, 1000); // Update every second (1000 milliseconds)
}



function removeCardFromWaiting(dataId) {
    $('.waiting .card[data-id="' + dataId + '"]').remove();
}
function removeCardFromPlaying(dataId) {
    $('.nowPlaying .card[data-id="' + dataId + '"]').remove();
}
function moveVideoToPlaying(videoId) {
    if (typeof videoId !== 'undefined') {
        $.ajax({
                    url: '{{ route('play') }}',
                    type: 'POST',
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    data: {
                        value: videoId
                    },
                    success: function(video) {

                        //updateProgressBar(video['id'],video['playtime']);
                    },
                    error: function(xhr, status, error) {
                        // Handle error response if needed
                    }
                });
    } else {
        // videoId is undefined
        // Your code here
    }


}

            var channel4 = pusher.subscribe('playing-channel');
            channel4.bind('playing-event', function(data) {
            console.log(data['collection']);
            var vid =  "{{ asset('storage') }}/app/" + data['collection']['name'];
            var name = vid.replace('/public',"");
            const isVideo = true; // or false if it's not a video, adjust accordingly
            var seconds = data['collection']['playtime'];

            AddCardToPlaying(data['collection']);
            removeCardFromWaiting(data['collection']['id']);

        });

function AddCardToPlaying(video)
    {
        // Calculate the percentage of progress
        var playtimeSeconds = video['playtime'] * 60;

        // Calculate the percentage of progress
        var progressPercentage = (video['playtime'] / playtimeSeconds) * 100;
        var assetDirectory = "{{ asset('') }}";
        var videoPath = assetDirectory + 'storage/app/' + video['name'];
        var result = videoPath.replace('/public', "");
        // Create the card HTML dynamically
        var cardHtml = '<div class="card-body row">';
        // First Column - Video Source (Small Rounded Preview)
        cardHtml += '<div class="col-md-4">';
        cardHtml += '<div class="video-preview medium">';
        cardHtml += '<video controls class="rounded-circle" width="100" height="100">';
        cardHtml += '<source  src="' + result + '" type="video/mp4">';
        cardHtml += 'Your browser does not support the video tag.';
        cardHtml += '</video>';
        cardHtml += '</div>';
        cardHtml += '</div>';


        // Second Column - Name, Loader, Dropdown, and Minute
        cardHtml += '<div class="col-md-8 ml-1">';
        cardHtml += '<h5 class="card-title">' + video['name'] + '</h5>';
        cardHtml += '<div class="progress">';
        cardHtml += '<div id="progress-bar-' + video['id'] + '" class="progress-bar bg-success" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: ' + progressPercentage + '%"></div>';
        cardHtml += '</div>';
        cardHtml += '<div class="d-flex align-items-center mt-2">';
        cardHtml += '<h4 class="m-0 me-3"> <span class="badge bg-primary"><i class="far fa-clock"></i> ' + video['playtime'] + ' min</span></h4>';
        cardHtml += '<div class="dropdown">';
        cardHtml += '<button class="btn btn-secondary dropdown-toggle btn-sm" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">Size</button>';
        cardHtml += '<ul class="dropdown-menu dropdown-menu-sm" aria-labelledby="dropdownMenuButton1">';
        cardHtml += '<li><a class="dropdown-item" href="#">Small</a></li>';
        cardHtml += '<li><a class="dropdown-item" href="#">Medium</a></li>';
        cardHtml += '<li><a class="dropdown-item" href="#">Large</a></li>';
        cardHtml += '</ul>';
        cardHtml += '</div>';
        cardHtml += '</div>';
        cardHtml += '</div>';
        cardHtml += '</div>';

        // Append the card HTML to the container
        $('.nowPlaying').append('<div data-id="'+video.id+'" id="card-' + video.id + '" class="card m-2">' + cardHtml + '</div>');
        updateProgressBar(video['id'], playtimeSeconds);
    }
function AddCardToWaiting(video)
    {
        // Calculate the percentage of progress
        var playtimeSeconds = video['playtime'] * 60;

        // Calculate the percentage of progress
        var progressPercentage = (video['playtime'] / playtimeSeconds) * 100;
        var assetDirectory = "{{ asset('') }}";
        var videoPath = assetDirectory + 'storage/app/' + video['name'];
        var result = videoPath.replace('/public', "");
        // Create the card HTML dynamically
        var cardHtml = '<div class="card-body row">';
        // First Column - Video Source (Small Rounded Preview)
        cardHtml += '<div class="col-md-4">';
        cardHtml += '<div class="video-preview medium">';
        cardHtml += '<video controls class="rounded-circle" width="100" height="100">';
        cardHtml += '<source  src="' + result + '" type="video/mp4">';
        cardHtml += 'Your browser does not support the video tag.';
        cardHtml += '</video>';
        cardHtml += '</div>';
        cardHtml += '</div>';


        // Second Column - Name, Loader, Dropdown, and Minute
        cardHtml += '<div class="col-md-8 ml-1">';
        cardHtml += '<h5 class="card-title">' + video['name'] + '</h5>';
        cardHtml += '<div class="progress">';
        cardHtml += '<div id="progress-bar-' + video['id'] + '" class="progress-bar bg-success" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: ' + progressPercentage + '%"></div>';
        cardHtml += '</div>';
        cardHtml += '<div class="d-flex align-items-center mt-2">';
        cardHtml += '<h4 class="m-0 me-3"> <span class="badge bg-primary"><i class="far fa-clock"></i> ' + video['playtime'] + ' min</span></h4>';
        cardHtml += '<div class="dropdown">';
        cardHtml += '<button class="btn btn-secondary dropdown-toggle btn-sm" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">Size</button>';
        cardHtml += '<ul class="dropdown-menu dropdown-menu-sm" aria-labelledby="dropdownMenuButton1">';
        cardHtml += '<li><a class="dropdown-item" href="#">Small</a></li>';
        cardHtml += '<li><a class="dropdown-item" href="#">Medium</a></li>';
        cardHtml += '<li><a class="dropdown-item" href="#">Large</a></li>';
        cardHtml += '</ul>';
        cardHtml += '</div>';
        cardHtml += '</div>';
        cardHtml += '</div>';
        cardHtml += '</div>';

        // Append the card HTML to the container
        $('.waiting').append('<div data-id="'+video.id+'" id="card-' + video.id + '" class="card m-2">' + cardHtml + '</div>');
    }

    var channel = pusher.subscribe('mode-channel');
        channel.bind('mode-event', function(data) {
            var mode = data['collection'];
            if(mode == true){

                $.ajax({
                    url: '{{ route('favoriteShow') }}',
                    type: 'POST',
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    data: {
                        value: true
                    },
                    success: function(data) {
                        data.forEach(function(video) {
                            AddCardToPlaying(video);
                    });
                    },
                    error: function(xhr, status, error) {
                        // Handle error response if needed
                    }
                });
            }else{
                $('.nowPlaying').empty();
            }

        });
        var channel1 = pusher.subscribe('upload-channel');
            channel1.bind('upload-event', function(data) {
                var vid =  "{{ asset('storage') }}/app/" + data['collection']['name'];
                var name = vid.replace('/public',"");
                const isVideo = true; // or false if it's not a video, adjust accordingly
                var seconds = data['collection']['playtime'];
                AddCardToWaiting(data['collection']);


            });

        var channel3 = pusher.subscribe('start-channel');
        channel3.bind('start-event', function(data) {
            data['collection'].forEach(function(element, index) {
                setTimeout(function() {
                    AddCardToPlaying(element);
                    removeCardFromWaiting(element['id']);
                }, index * 3000); // Delay each iteration by multiplying index with 3000 (3 seconds)
            });
        });



        $(document).ready(function() {
            // Add click event handler to the button
            $('#queue-control').click(function() {
                // Toggle the text of the button
                if (isQueueRunning) {
                    stopQueue();
                } else {
                    startQueue();
                }
            });
        });
</script>
@endsection
