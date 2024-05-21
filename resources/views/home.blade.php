@extends('layouts.app')

@section('content')
<style>
    /* Toggle switch styles */
    .switch {
      position: relative;
      display: inline-block;
      width: 120px;
      height: 34px;
    }

    .switch input {
      opacity: 0;
      width: 0;
      height: 0;
    }

    .slider {
      position: absolute;
      cursor: pointer;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background-color: #ccc;
      transition: .4s;
      border-radius: 34px;
    }

    .slider:before {
      position: absolute;
      content: "";
      height: 26px;
      width: 26px;
      left: 4px;
      bottom: 4px;
      background-color: white;
      transition: .4s;
      border-radius: 50%;
    }

    input:checked + .slider {
      background-color: #2196F3;
    }

    input:focus + .slider {
      box-shadow: 0 0 1px #2196F3;
    }

    input:checked + .slider:before {
      transform: translateX(85px);
    }

    /* Rounded sliders */
    .slider.round {
      border-radius: 34px;
    }

    .slider.round:before {
      border-radius: 50%;
    }

    /* Text labels */
    .switch-labels {
      display: flex;
      justify-content: space-between;
      margin-bottom: 10px;
    }
    </style>
<div class="container mt-5">


    <table id="myTable" class="display">
        <thead>
            <tr>
                <th>Name</th>
                <th>Size</th>
                <th>Playtime</th>
                <th>Type</th>
                <th>Favorite</th>
            </tr>
        </thead>
        <tbody>
            @foreach ( $videos as $video)
            <tr>
                <td>
                    <div class="video-preview medium">
                        <video controls width="100%" height="100%" autoplay muted>
                            <source src="{{
                                str_replace('/public', '', asset('storage/app/' . $video->name))
                                }}" type="video/mp4">
                        </video>
                    </div>
                </td>
                <td>{{ $video->size }}     </td>
                <td>{{ $video->playtime }} </td>
                <td>{{ $video->type }} </td>
                <td>
                    @if ($video->is_favorite == true)
                    <i class="fav btn btn-lg fa-solid fa-star" data-id="{{ $video->id }}" data-value="true"></i>
                    @else
                    <i class="fav btn btn-lg fa-regular fa-star " data-id="{{ $video->id }}" data-value="false"></i>
                    @endif
                </td>

            </tr>
            @endforeach


        </tbody>
    </table>
</div>

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
        width: 150px;
        height: 150px;
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
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

<script>
    function toggleSwitchClicked() {
        var csrfToken = $('meta[name="csrf-token"]').attr('content');
        var toggleSwitch = document.getElementById("toggleSwitch");
        var value = !toggleSwitch.checked;
        var toggleText = value ? 'Preset' : 'Fresh';

        $('.toggle').html(toggleText);

        $.ajax({
            url: '{{ route('mode') }}',
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            data: {
                value: value
            },
            success: function(response) {
                // Handle success response if needed
            },
            error: function(xhr, status, error) {
                // Handle error response if needed
            }
        });

        console.log(value);
    }


    $(document).ready( function () {
        $('#myTable').DataTable();

        $('.fav').click(function() {
            var csrfToken = $('meta[name="csrf-token"]').attr('content');
            var id = $(this).data('id');
            var value = $(this).data('value');
            $.ajax({
                    url: '{{ route('favorite') }}', // Using Laravel's route() helper function
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken, // Include the CSRF token in the headers
                    },
                    data: {
                        id: id,
                        value : value
                    },
                    success: function(response) {


                    },
                    error: function(xhr, status, error) {


                    }
                });


            // Toggle data-value attribute
            if (value == 'true') {
                $(this).removeClass('fas').addClass('far');
                $(this).data('value', 'false');

            } else {
                $(this).removeClass('far').addClass('fas');

                $(this).data('value', 'true');
                // Here you can make an AJAX request to update the favorite status in the database
            }

            // For demonstration, log the id and new value
            console.log('ID:', id, 'New Value:', $(this).data('value'));
        });
    } );
   </script>
@endsection
