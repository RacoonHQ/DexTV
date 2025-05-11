<div id="video-wrapper">
    <div class="video-container">
        <video id="tvPlayer" class="w-100 h-100 shadow rounded" autoplay playsinline controlsList="nodownload nofullscreen noremoteplayback">
            <source src="path/to/video-1080.mp4" type="video/mp4" data-resolution="1080">
            <source src="path/to/video-720.mp4" type="video/mp4" data-resolution="720">
            <source src="path/to/video-420.mp4" type="video/mp4" data-resolution="420">
        </video>
        <div class="video-controls">
            <button class="play-pause-btn">
                <i class="fas fa-play"></i>
            </button>
            <div class="progress-bar">
                <div class="progress"></div>
            </div>
            <span class="time-display">00:00 / 00:00</span>
            <button class="volume-btn">
                <i class="fas fa-volume-up"></i>
            </button>
            <input type="range" class="volume-slider" min="0" max="1" step="0.1" value="1">
            <button class="fullscreen-btn">
                <i class="fas fa-expand"></i>
            </button>
            <div class="menu-btn-container">
                <button class="menu-btn">
                    <i class="fas fa-ellipsis-v"></i>
                </button>
                <div class="menu-dropdown hidden">
                    <div class="menu-item">
                        <span>Playback Speed</span>
                        <select id="playbackSpeed">
                            <option value="0.5">0.5x</option>
                            <option value="1" selected>1x</option>
                            <option value="1.5">1.5x</option>
                            <option value="2">2x</option>
                        </select>
                    </div>
                    <div class="menu-item">
                        <span>Resolution</span>
                        <select id="videoResolution">
                            <option value="auto" selected>Auto</option>
                            <option value="1080">1080p</option>
                            <option value="720">720p</option>
                            <option value="420">420p</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const menuBtn = document.querySelector('.menu-btn');
    const menuDropdown = document.querySelector('.menu-dropdown');
    const playbackSpeed = document.getElementById('playbackSpeed');
    const videoResolution = document.getElementById('videoResolution');
    const videoPlayer = document.getElementById('tvPlayer');

    // Ensure menu is hidden initially
    menuDropdown.style.display = 'none';

    // Toggle menu visibility on button click
    menuBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        if (menuDropdown.style.display === 'none') {
            menuDropdown.style.display = 'block';
            
            // Calculate position to avoid edge overflow
            const dropdownRect = menuDropdown.getBoundingClientRect();
            const viewportWidth = window.innerWidth;
            
            if (dropdownRect.right > viewportWidth) {
                menuDropdown.style.right = '0';
            }
        } else {
            menuDropdown.style.display = 'none';
        }
    });

    // Close menu when clicking outside
    document.addEventListener('click', (e) => {
        if (!menuBtn.contains(e.target) && !menuDropdown.contains(e.target)) {
            menuDropdown.style.display = 'none';
        }
    });

    // Keep menu open when clicking inside it
    menuDropdown.addEventListener('click', (e) => {
        e.stopPropagation();
    });

    // Change playback speed
    playbackSpeed.addEventListener('change', (e) => {
        videoPlayer.playbackRate = parseFloat(e.target.value);
    });

    // Change resolution without reloading
    videoResolution.addEventListener('change', (e) => {
        const selectedResolution = e.target.value;
        const currentTime = videoPlayer.currentTime;
        const isPlaying = !videoPlayer.paused;

        if (selectedResolution === 'auto') {
            // Let the player automatically choose the best resolution
            videoPlayer.src = videoPlayer.currentSrc;
        } else {
            const sources = videoPlayer.querySelectorAll('source');
            sources.forEach(source => {
                if (source.getAttribute('data-resolution') === selectedResolution) {
                    videoPlayer.src = source.getAttribute('src');
                }
            });
        }

        videoPlayer.currentTime = currentTime;
        if (isPlaying) {
            videoPlayer.play();
        }
    });

    // Modified playChannel function to handle loading state and active channel
    function playChannel(url, channelElement) {
        if (!url) return;
        
        // Show loading overlay
        const loadingOverlay = document.getElementById('loadingOverlay');
        if (loadingOverlay) {
            loadingOverlay.style.display = 'flex';
        }
        
        // Remove active class from all channels
        document.querySelectorAll('.channel').forEach(ch => {
            ch.classList.remove('active');
        });
        
        // Add active class to selected channel
        if (channelElement) {
            channelElement.classList.add('active');
        }
        
        videoPlayer.src = url;
        videoPlayer.play()
            .then(() => {
                if (loadingOverlay) {
                    loadingOverlay.style.display = 'none';
                }
            })
            .catch(error => {
                console.error('Playback failed:', error);
                if (loadingOverlay) {
                    loadingOverlay.innerHTML = '<p>Channel tidak tersedia</p>';
                    setTimeout(() => {
                        loadingOverlay.style.display = 'none';
                    }, 2000);
                }
            });

        videoPlayer.addEventListener('waiting', () => {
            if (loadingOverlay) loadingOverlay.style.display = 'flex';
        });

        videoPlayer.addEventListener('playing', () => {
            if (loadingOverlay) loadingOverlay.style.display = 'none';
        });
    }

    // Make playChannel available globally
    window.playChannel = playChannel;

    async function initializeCategory(category) {
        const response = await fetch(`<?php echo SITE_URL; ?>/api/channels.php?category=${category}`);
        const data = await response.json();
        
        if (data.activeChannel) {
            playChannel(data.activeChannel.url);
            console.log(`Playing working channel: ${data.activeChannel.name}`);
        } else {
            console.log('No working channels found');
            // Show error message to user
            const loadingOverlay = document.getElementById('loadingOverlay');
            if (loadingOverlay) {
                loadingOverlay.innerHTML = '<p>No working channels found. Please try again later.</p>';
                loadingOverlay.style.display = 'block';
            }
        }
    }

    // Make function available globally
    window.initializeCategory = initializeCategory;
</script>
