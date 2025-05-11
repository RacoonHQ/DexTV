document.addEventListener("DOMContentLoaded", function() {
    const toggleIcon = document.querySelector('.toggle-icon');
    const channelList = document.getElementById("channel-list");
    const videoWrapper = document.getElementById("video-wrapper");
    let isChannelListVisible = true;

    toggleIcon.addEventListener("click", function() {
        isChannelListVisible = !isChannelListVisible;
        
        if (isChannelListVisible) {
            channelList.style.width = "20%";
            channelList.style.display = "block";
            videoWrapper.style.width = "80%";
            toggleIcon.innerHTML = '<i class="fas fa-chevron-right"></i>';
        } else {
            channelList.style.width = "0";
            channelList.style.display = "none";
            videoWrapper.style.width = "100%";
            toggleIcon.innerHTML = '<i class="fas fa-chevron-left"></i>';
        }
    });
});
