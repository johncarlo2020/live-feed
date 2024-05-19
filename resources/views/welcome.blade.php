<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
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
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script>
        function getRandomPosition() {
            const windowWidth = window.innerWidth;
            const windowHeight = window.innerHeight;
            const maxWidth = windowWidth - 100; // Adjusted to bubble width
            const maxHeight = windowHeight - 100; // Adjusted to bubble height
            const randomX = Math.floor(Math.random() * maxWidth);
            const randomY = Math.floor(Math.random() * maxHeight);
            return { x: randomX, y: randomY };
        }

        function getRandomVelocity() {
            return (Math.random() - 0.5) * 2; // Random velocity between -1 and 1
        }

        function getRandomColor() {
            const letters = '0123456789ABCDEF';
            let color = '#';
            for (let i = 0; i < 6; i++) {
                color += letters[Math.floor(Math.random() * 16)];
            }
            return color;
        }

        function createBubble(size, isVideo,file,time) {
            const bubbleWrapper = document.createElement('div');
            bubbleWrapper.className = 'bubbleWrapper';
            const position = getRandomPosition();
            bubbleWrapper.style.left = position.x + 'px';
            bubbleWrapper.style.top = position.y + 'px';

            if (isVideo) {
                const bubbleVideoWrapper = document.createElement('div');
                bubbleVideoWrapper.className = 'bubbleVideoWrapper';
                bubbleVideoWrapper.style.width = size + 'px';
                bubbleVideoWrapper.style.height = size + 'px';

                const bubbleVideo = document.createElement('video');
                bubbleVideo.className = 'bubbleVideo';
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
                    }, bubbleVideo.duration * time); // Remove the bubble after the video duration
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

                // Collision detection with window boundaries
                if (x + bubbleWrapper.clientWidth >= window.innerWidth || x <= 0) {
                    dx = -dx;
                    collision = true;
                }
                if (y + bubbleWrapper.clientHeight >= window.innerHeight || y <= 0) {
                    dy = -dy;
                    collision = true;
                }

                // Collision detection with other bubbles
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
            return Math.floor(Math.random() * 3 + 1) * 50; // 3 sizes: small, medium, large
        }

        const numberOfBubbles = 10; // Set the number of bubbles to 10

        const videos = @json($videos);
        console.log(videos);

        videos.forEach(element => {
           var vid =  "{{ asset('storage') }}/app/" + element['name'];
           var name = vid.replace('/public',"");
            const isVideo = true; // or false if it's not a video, adjust accordingly
            var seconds = element['playtime'] * 60000;
            createBubble(150, isVideo,  name, seconds);
        });

        var pusher = new Pusher('60de59064bcf7cfb6d63', {
        cluster: 'ap1'
        });

        var channel = pusher.subscribe('upload-channel');
        channel.bind('upload-event', function(data) {
            console.log(data['collection']);
           var vid =  "{{ asset('storage') }}/app/" + data['collection']['name'];
           var name = vid.replace('/public',"");
            const isVideo = true; // or false if it's not a video, adjust accordingly
            var seconds = data['collection']['playtime'] * 60000;
        createBubble(150, isVideo,  name, seconds);

        });

    </script>
</body>
</html>
