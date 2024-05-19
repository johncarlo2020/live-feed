@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <table id="myTable" class="display">
        <thead>
            <tr>
                <th>Name</th>
                <th>Size</th>
                <th>Playtime</th>
                <th>Type</th>


            </tr>
        </thead>
        <tbody>
            @foreach ( $videos as $video)
            <tr>
                <td>
                    <div class="video-preview medium">
                        <video controls width="100%" height="100%" autoplay muted>
                            <source src="{{
                            str_replace('/public/', '/', asset('storage/app/' . $video->name))
                            }}" type="video/mp4">
                        </video>
                    </div>
                </td>
                <td>{{ $video->size }}     </td>
                <td>{{ $video->playtime }} </td>
                <td>{{ $video->type }} </td>

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
    $(document).ready( function () {
        $('#myTable').DataTable();
    } );
   </script>
@endsection
