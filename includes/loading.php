<?php
function renderChannelPage($category) {
    requireLogin();
    $channels = getChannels($category);
?>
    <div class="container-fluid p-0">
        <div id="tv-container">
            <div id="video-wrapper">
                <?php include 'player.php'; ?>
                <div id="loadingOverlay" class="position-absolute top-50 start-50 translate-middle text-center">
                    <div class="spinner-border text-light" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Please wait...</p>
                </div>
            </div>
            <div id="channel-list">
                <?php foreach($channels as $channel): ?>
                    <div class="channel p-3 m-2 rounded" 
                         data-channel-id="<?php echo htmlspecialchars($channel['id']); ?>"
                         onclick="playChannel('<?php echo htmlspecialchars($channel['url']); ?>', this)">
                        <h5 class="mb-0"><?php echo htmlspecialchars($channel['name']); ?></h5>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
<?php
}
?>
