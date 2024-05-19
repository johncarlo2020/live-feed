@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1 class="mb-4">Upload Multiple Videos</h1>
    <form method="POST" action="{{ route('upload_videos') }}"  enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label for="videos" class="form-label">Upload Videos</label>
            <input type="file" class="form-control" id="videos" name="videos[]" accept="video/*" multiple required>
        </div>


        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Preview</th>
                        <th>Video Name</th>
                        <th>Select Size</th>
                        <th>Playtime (seconds)</th>
                    </tr>
                </thead>
                <tbody id="videoOptions"></tbody>
            </table>
        </div>

        <button type="submit" class="btn btn-primary">Upload</button>
    </form>
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

<script>
    // JavaScript for dynamically adding video options with preview
    document.getElementById('videos').addEventListener('change', function(e) {
        var videos = e.target.files;
        var videoOptions = document.getElementById('videoOptions');
        videoOptions.innerHTML = ''; // Clear previous options

        for (var i = 0; i < videos.length; i++) {
            var videoName = videos[i].name;
            var videoOption = document.createElement('tr');

            // Video preview
            var previewCell = document.createElement('td');
            var videoPreview = document.createElement('div');
            videoPreview.classList.add('video-preview', 'medium', 'preview-' + i); // Set initial size to "medium"
            videoPreview.setAttribute('data-preview-index', i);
            videoPreview.innerHTML = `
                <video controls width="100%" height="100%" autoplay muted>
                    <source src="${URL.createObjectURL(videos[i])}" type="video/mp4">
                </video>
            `;
            previewCell.appendChild(videoPreview);
            videoOption.appendChild(previewCell);

            // Video name
            var nameCell = document.createElement('td');
            nameCell.textContent = videoName;
            videoOption.appendChild(nameCell);

            // Size selection
            var sizeSelectCell = document.createElement('td');
            var sizeSelect = document.createElement('select');
            sizeSelect.classList.add('form-select', 'size-select');
            sizeSelect.setAttribute('name', 'size[]');
            sizeSelect.setAttribute('data-preview-index', i);
            sizeSelect.required = true;
            var sizeOptions = ['Small', 'Medium', 'Large'];
            for (var j = 0; j < sizeOptions.length; j++) {
                var option = document.createElement('option');
                option.value = sizeOptions[j].toLowerCase();
                option.textContent = sizeOptions[j];
                sizeSelect.appendChild(option);
            }
            sizeSelectCell.appendChild(sizeSelect);
            videoOption.appendChild(sizeSelectCell);

            // Playtime input
            var playtimeCell = document.createElement('td');
            var playtimeInput = document.createElement('input');
            playtimeInput.classList.add('form-control', 'playtime-input');
            playtimeInput.setAttribute('type', 'number');
            playtimeInput.setAttribute('name', 'playtime[]');
            playtimeInput.setAttribute('min', '1');
            playtimeInput.setAttribute('data-preview-index', i);
            playtimeInput.required = true;
            playtimeInput.placeholder = 'Playtime (seconds)';
            playtimeCell.appendChild(playtimeInput);
            videoOption.appendChild(playtimeCell);

            // Append the row to the table body
            videoOptions.appendChild(videoOption);
        }
    });

    // Update video preview size when the bubble size changes
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('size-select')) {
            var previewIndex = e.target.getAttribute('data-preview-index');
            var selectedSize = e.target.value;
            var preview = document.querySelector('.preview-' + previewIndex);
            preview.classList.remove('small', 'medium', 'large');
            preview.classList.add(selectedSize);
        }
    });
</script>

@endsection
