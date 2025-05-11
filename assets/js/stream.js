document.addEventListener("DOMContentLoaded", function () {
    const video = document.getElementById("tvPlayer");
    const channelList = document.getElementById("channel-list");
    const liveButton = document.getElementById("liveButton");
    const currentPage = window.location.pathname;

    let hls;
    let currentUrl = "";

    // Fungsi untuk memuat data channel dari JSON
    async function loadChannelData() {
        try {
            const response = await fetch('/DexTV/data/data_tv.json');
            const data = await response.json();
            return data.categories;
        } catch (error) {
            console.error("Error loading channel data:", error);
            return null;
        }
    }

    // Fungsi untuk menampilkan loading
    function showLoading() {
        const loadingOverlay = document.getElementById("loadingOverlay");
        if (loadingOverlay) loadingOverlay.style.display = "block";
    }

    function hideLoading() {
        const loadingOverlay = document.getElementById("loadingOverlay");
        if (loadingOverlay) loadingOverlay.style.display = "none";
    }

    // Event listeners untuk video loading
    if (video) {
        video.addEventListener("waiting", showLoading);
        video.addEventListener("playing", hideLoading);
    }

    // Live button functionality
    if (liveButton) {
        liveButton.textContent = "LIVE";
        liveButton.style.backgroundColor = "gray";

        liveButton.addEventListener("click", function () {
            if (liveButton.style.backgroundColor === "gray") {
                liveButton.textContent = "ON AIR";
                liveButton.style.backgroundColor = "red";
            } else {
                liveButton.textContent = "LIVE";
                liveButton.style.backgroundColor = "gray";
            }
        });
    }

    // Fungsi untuk memutar channel
    function playChannel(url) {
        if (!video) return;

        if (hls) {
            hls.destroy();
        }

        showLoading();

        if (Hls.isSupported()) {
            hls = new Hls();
            hls.loadSource(url);
            hls.attachMedia(video);
            hls.on(Hls.Events.MANIFEST_PARSED, function() {
                hideLoading();
                video.play();
            });
        } else if (video.canPlayType('application/vnd.apple.mpegurl')) {
            video.src = url;
            video.addEventListener('loadedmetadata', function() {
                hideLoading();
                video.play();
            });
        }
    }

    // Load channels berdasarkan kategori
    async function loadChannels(category = 'all') {
        const categories = await loadChannelData();
        if (!categories) return;

        channelList.innerHTML = '';

        if (category === 'all') {
            // Get first channel from first category for all view
            const firstCategory = Object.values(categories)[0];
            if (firstCategory?.channels?.length > 0) {
                const firstChannel = firstCategory.channels[0];
                playChannel(firstChannel.url);
            }
            
            Object.values(categories).forEach(cat => {
                cat.channels.forEach(channel => {
                    addChannelToList(channel);
                });
            });
        } else if (categories[category]) {
            // Auto play first channel of selected category
            const channels = categories[category].channels;
            if (channels.length > 0) {
                playChannel(channels[0].url);
            }
            
            channels.forEach(channel => {
                addChannelToList(channel);
            });
        }
    }

    // Fungsi helper untuk menambah channel ke list
    function addChannelToList(channel) {
        const div = document.createElement('div');
        div.className = 'channel p-3 m-2 rounded';
        div.innerHTML = `<h5 class="mb-0">${channel.name}</h5>`;
        div.onclick = () => playChannel(channel.url);
        channelList.appendChild(div);
    }

    // Initialize based on current page
    const pageCategory = currentPage.split('/').pop().replace('.php', '');
    loadChannels(pageCategory === 'index' ? 'all' : pageCategory);
});
