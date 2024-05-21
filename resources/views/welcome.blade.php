<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Laravel</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <!-- Styles -->
    <style>
        body {
            font-family: 'Nunito';
            margin: 0;
            overflow: hidden; /* Hide overflow to prevent scrollbars */
            position: relative; /* Ensure relative positioning for absolute children */
            background-image: url({{ asset('images/bg.png') }}); /* Set background image */
            background-size: cover; /* Cover the entire background */
            background-position: center;
        }
        .bubbleWrapper {
            position: absolute; /* Position absolutely within the parent */
        }
        .bubble {
            width: 50px; /* Bubble size */
            height: 50px; /* Bubble size */
            border-radius: 50%; /* Circular shape */
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.5); /* Add shadow for a more realistic effect */
        }
        .bubbleVideoWrapper {
            position: relative; /* Position relatively within the parent */
            width: 100%; /* Set width to 100% */
            height: 100%; /* Set height to 100% */
            border-radius: 50%;
            overflow: hidden; /* Clip video to the shape of the bubble */
            position: relative;
        }
        .bubbleVideo {
            width: 100%; /* Ensure the video fills the bubble wrapper */
            height: 100%; /* Ensure the video fills the bubble wrapper */
            object-fit: cover; /* Maintain aspect ratio and fill the container */
            border-radius: 50%; /* Apply border radius to match bubble shape */
        }
        .bubbleOverlay {
            position: absolute; /* Position absolutely within the video wrapper */
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none; /* Make sure it doesn't interfere with mouse events on the video */
            opacity: 0.7; /* Set opacity to 70% */
        }
    </style>
</head>
<body class="antialiased">
    <video autoplay muted loop id="bg-video">
        <source src="{{ asset('images/bg.mp4') }}" type="video/mp4">
        Your browser does not support the video tag.
    </video>
    <div class="content">
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script>
        function removeAllBubbles() {
            const bubbleWrappers = document.querySelectorAll('.bubbleWrapper');
            bubbleWrappers.forEach(bubbleWrapper => {
                bubbleWrapper.parentNode.removeChild(bubbleWrapper);
            });
        }
        function getRandomPosition() {
    const windowWidth = window.innerWidth;
    const windowHeight = window.innerHeight;
    const bubbleWidth = 100; // Adjust according to bubble size
    const bubbleHeight = 100; // Adjust according to bubble size
    const maxWidth = windowWidth - bubbleWidth;
    const maxHeight = windowHeight - bubbleHeight;
    const randomX = Math.floor(Math.random() * maxWidth);
    const randomY = Math.floor(Math.random() * maxHeight);
    return { x: randomX, y: randomY };
}


        function getRandomVelocity() {
    return (Math.random() - 0.5) * 2 * 2; // Random velocity between -3 and 3
}

        function getRandomColor() {
            const letters = '0123456789ABCDEF';
            let color = '#';
            for (let i = 0; i < 6; i++) {
                color += letters[Math.floor(Math.random() * 16)];
            }
            return color;
        }

        function createBubble(size, isVideo,file,time,mode) {
            const bubbleWrapper = document.createElement('div');
            bubbleWrapper.className = 'bubbleWrapper';
            const position = getRandomPosition();
            bubbleWrapper.style.left = position.x + 'px';
            bubbleWrapper.style.top = position.y + 'px';

            if (isVideo) {
                const bubbleVideoWrapper = document.createElement('div');
                    bubbleVideoWrapper.className = 'bubbleVideoWrapper';

                    let sizePercentage;
                    switch (size) {
                        case 'large':
                            sizePercentage = 0.2; // 10% of screen width
                            break;
                        case 'medium':
                            sizePercentage = 0.15; // 8% of screen width
                            break;
                        case 'small':
                            sizePercentage = 0.10; // 5% of screen width
                            break;
                        default:
                            sizePercentage = 0.2; // Default to large size
                    }

                    const screenWidth = window.innerWidth;
                    const bubbleSize = screenWidth * sizePercentage;

                    bubbleVideoWrapper.style.width = bubbleSize + 'px';
                    bubbleVideoWrapper.style.height = bubbleSize + 'px';


                const bubbleVideo = document.createElement('video');
                bubbleVideo.className = 'bubbleVideo';
                bubbleVideo.classList.add(mode);

                bubbleVideo.src = file;
                bubbleVideo.type = "video/mp4";

                bubbleVideo.autoplay = true; // Autoplay the video
                bubbleVideo.loop = true; // Loop the video
                bubbleVideo.preload = 'auto';
                bubbleVideo.muted = true;
                // Play the video once it's loaded
                bubbleVideo.addEventListener('loadeddata', function() {
                    bubbleVideo.play();
                    setTimeout(function() {
                bubbleWrapper.parentNode.removeChild(bubbleWrapper);
    }, 1000 * time * 60); // Remove the bubble after the video duration
                });

                bubbleVideoWrapper.appendChild(bubbleVideo);
                bubbleWrapper.appendChild(bubbleVideoWrapper);

                // Create bubble overlay
                const bubbleOverlay = document.createElement('img');
                bubbleOverlay.src = "{{ asset('videos/bubble.png') }}"; // Replace with actual path
                bubbleOverlay.className = 'bubbleOverlay';
                bubbleVideoWrapper.appendChild(bubbleOverlay);
            } else {
                const bubble = document.createElement('div');
                bubble.className = 'bubble';
                bubble.style.width = size + 'px';
                bubble.style.height = size + 'px';
                bubble.style.backgroundColor = getRandomColor();
                bubbleWrapper.appendChild(bubble);
            }


            document.body.appendChild(bubbleWrapper);

            animate(bubbleWrapper, isVideo); // Start animation for this bubble
        }

        function animate(bubbleWrapper, isVideo) {
    let x = parseInt(bubbleWrapper.style.left, 10) || 0;
    let y = parseInt(bubbleWrapper.style.top, 10) || 0;
    let dx = getRandomVelocity(); // Random initial velocity
    let dy = getRandomVelocity(); // Random initial velocity

    function moveBubble() {
        x += dx;
        y += dy;

        let collision = false; // Flag to track if a collision occurred

        // Check for collisions with screen boundaries
        if (x + bubbleWrapper.clientWidth >= window.innerWidth || x <= 0) {
            dx = -dx; // Reflect velocity along x-axis
            x = Math.max(0, Math.min(window.innerWidth - bubbleWrapper.clientWidth, x)); // Ensure bubble stays within screen
            collision = true;
        }
        if (y + bubbleWrapper.clientHeight >= window.innerHeight || y <= 0) {
            dy = -dy; // Reflect velocity along y-axis
            y = Math.max(0, Math.min(window.innerHeight - bubbleWrapper.clientHeight, y)); // Ensure bubble stays within screen
            collision = true;
        }

        // Check for collisions with other bubbles
        const allBubbles = document.querySelectorAll('.bubbleWrapper');
        allBubbles.forEach(otherBubble => {
            if (otherBubble !== bubbleWrapper) {
                const rect1 = bubbleWrapper.getBoundingClientRect();
                const rect2 = otherBubble.getBoundingClientRect();

                if (
                    rect1.right > rect2.left &&
                    rect1.left < rect2.right &&
                    rect1.bottom > rect2.top &&
                    rect1.top < rect2.bottom
                ) {
                    // Collision occurred
                    collision = true;

                    // Calculate the direction to move the bubbles away from each other
                    const dxOffset = (rect1.left + rect1.width / 2) > (rect2.left + rect2.width / 2) ? 1 : -1;
                    const dyOffset = (rect1.top + rect1.height / 2) > (rect2.top + rect2.height / 2) ? 1 : -1;

                    // Apply offset to move bubbles away from each other
                    x += dxOffset;
                    y += dyOffset;
                }
            }
        });

        // Update bubble position
        bubbleWrapper.style.left = x + 'px';
        bubbleWrapper.style.top = y + 'px';

        // If a collision occurred, change direction
        if (collision) {
            dx = getRandomVelocity();
            dy = getRandomVelocity();
        }

        requestAnimationFrame(moveBubble);
    }

    moveBubble();
}

        function getRandomSize() {
            return Math.floor(Math.random() * 3 + 1) * 150; // 3 sizes: small, medium, large
        }

        const numberOfBubbles = 10; // Set the number of bubbles to 10


        var pusher = new Pusher('60de59064bcf7cfb6d63', {
        cluster: 'ap1'
        });

        var channel = pusher.subscribe('playing-channel');
            channel.bind('playing-event', function(data) {
            console.log(data['collection']);
            var vid =  "{{ asset('storage') }}/app/" + data['collection']['name'];
            var name = vid.replace('/public',"");
            const isVideo = true; // or false if it's not a video, adjust accordingly
            var seconds = data['collection']['playtime'];

                createBubble(data['collection']['size'], isVideo,  name, seconds,'fresh');


        });


        // var channel1 = pusher.subscribe('mode-channel');
        // channel1.bind('mode-event', function(data) {
        //     console.log(data['collection']);

        //     var mode = data['collection'];
        //     removeAllBubbles();
        //     if(mode == true){

        //         $.ajax({
        //             url: '{{ route('favoriteShow') }}',
        //             type: 'POST',
        //             headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        //             data: {
        //                 value: true
        //             },
        //             success: function(response) {
        //                 response.forEach(element => {
        //                 var vid =  "{{ asset('storage') }}/app/" + element['name'];
        //                 var name = vid.replace('/public',"");
        //                     const isVideo = true; // or false if it's not a video, adjust accordingly
        //                     var seconds = element['playtime'] ;
        //                     createBubble(element['size'], isVideo,  name, seconds,'preset');
        //                 });
        //             },
        //             error: function(xhr, status, error) {
        //                 // Handle error response if needed
        //             }
        //         });




        //     }else{
        //         for(var a =0; a<5; a++){
        //             createBubble('medium', false,  name, 10000,'preset');

        //         }
        //     }

        // });

        var channel2 = pusher.subscribe('start-channel');
        channel2.bind('start-event', function(data) {
            removeAllBubbles(); // Assuming this function removes all existing bubbles
            createBubble('small', 'true', 'sample', '1000000', 'preset');
            createBubble('small', 'true', 'sample', '1000000', 'preset');
            createBubble('small', 'true', 'sample', '1000000', 'preset');
            createBubble('small', 'true', 'sample', '1000000', 'preset');
            createBubble('small', 'true', 'sample', '1000000', 'preset');


            data['collection'].forEach(function(element, index) {
                setTimeout(function() {
                    var vid = "{{ asset('storage') }}/app/" + element['name'];
                    var name = vid.replace('/public', '');
                    const isVideo = true; // or false if it's not a video, adjust accordingly
                    var seconds = element['playtime'];
                    createBubble(element['size'], isVideo, name, seconds, 'preset');
                }, index * 3000); // Delay each iteration by multiplying index with 3000 (3 seconds)
            });
        });


    </script>
</body>
</html>
