document.addEventListener("DOMContentLoaded", function () {
    var video = document.getElementById("tvPlayer");
    var loadingText = document.createElement("div"); 
    loadingText.id = "loadingMessage";
    loadingText.innerText = "Mohon Tunggu Sebentar...";
    loadingText.style.position = "absolute";
    loadingText.style.top = "50%";
    loadingText.style.left = "50%";
    loadingText.style.transform = "translate(-50%, -50%)";
    loadingText.style.background = "rgba(0, 0, 0, 0.7)";
    loadingText.style.color = "white";
    loadingText.style.padding = "10px 20px";
    loadingText.style.borderRadius = "5px";
    loadingText.style.display = "none";
    document.getElementById("video-wrapper").appendChild(loadingText);

    window.playChannel = function (url) {
        loadingText.style.display = "block"; // Tampilkan pesan loading
        
        if (Hls.isSupported()) {
            var hls = new Hls();
            hls.loadSource(url);
            hls.attachMedia(video);
            hls.on(Hls.Events.MANIFEST_PARSED, function () {
                loadingText.style.display = "none"; // Sembunyikan pesan saat video siap
                video.play();
            });
        } else if (video.canPlayType("application/vnd.apple.mpegurl")) {
            video.src = url;
            video.addEventListener("loadeddata", function () {
                loadingText.style.display = "none"; // Sembunyikan pesan saat video siap
                video.play();
            }, { once: true });
        }
    };
});
