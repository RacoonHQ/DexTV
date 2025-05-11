document.addEventListener('DOMContentLoaded', function() {
    const video = document.getElementById('tvPlayer');
    const playPauseBtn = document.querySelector('.play-pause-btn');
    const progressBar = document.querySelector('.progress-bar');
    const progress = document.querySelector('.progress');
    const timeDisplay = document.querySelector('.time-display');
    const volumeBtn = document.querySelector('.volume-btn');
    const volumeSlider = document.querySelector('.volume-slider');
    const fullscreenBtn = document.querySelector('.fullscreen-btn');

    // Play/Pause
    playPauseBtn.addEventListener('click', togglePlay);
    video.addEventListener('click', togglePlay);

    function togglePlay() {
        if (video.paused) {
            video.play();
            playPauseBtn.innerHTML = '<i class="fas fa-pause"></i>';
        } else {
            video.pause();
            playPauseBtn.innerHTML = '<i class="fas fa-play"></i>';
        }
    }

    // Progress Bar
    video.addEventListener('timeupdate', updateProgress);
    progressBar.addEventListener('click', setProgress);

    function updateProgress() {
        const percent = (video.currentTime / video.duration) * 100;
        progress.style.width = percent + '%';
        
        const currentMinutes = Math.floor(video.currentTime / 60);
        const currentSeconds = Math.floor(video.currentTime % 60);
        const durationMinutes = Math.floor(video.duration / 60);
        const durationSeconds = Math.floor(video.duration % 60);
        
        timeDisplay.textContent = `${padZero(currentMinutes)}:${padZero(currentSeconds)} / ${padZero(durationMinutes)}:${padZero(durationSeconds)}`;
    }

    function setProgress(e) {
        const newTime = (e.offsetX / progressBar.offsetWidth) * video.duration;
        video.currentTime = newTime;
    }

    // Volume
    volumeBtn.addEventListener('click', toggleMute);
    volumeSlider.addEventListener('input', setVolume);

    function toggleMute() {
        video.muted = !video.muted;
        updateVolumeIcon();
    }

    function setVolume() {
        video.volume = volumeSlider.value;
        updateVolumeIcon();
    }

    function updateVolumeIcon() {
        const icon = volumeBtn.querySelector('i');
        if (video.muted || video.volume === 0) {
            icon.className = 'fas fa-volume-mute';
        } else if (video.volume < 0.5) {
            icon.className = 'fas fa-volume-down';
        } else {
            icon.className = 'fas fa-volume-up';
        }
    }

    // Fullscreen
    fullscreenBtn.addEventListener('click', toggleFullscreen);

    function toggleFullscreen() {
        if (!document.fullscreenElement) {
            video.requestFullscreen();
            fullscreenBtn.innerHTML = '<i class="fas fa-compress"></i>';
        } else {
            document.exitFullscreen();
            fullscreenBtn.innerHTML = '<i class="fas fa-expand"></i>';
        }
    }

    function padZero(num) {
        return num.toString().padStart(2, '0');
    }
});
