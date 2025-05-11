<div class="chat-container">
    <button class="chat-toggle-btn">
        <i class="fas fa-comment-dots"></i>
    </button>
    
    <div class="chat-box hidden">
        <div class="chat-header">
            <h5>Send Message to Admin</h5>
            <button class="chat-close-btn">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="chatForm" class="chat-form">
            <textarea class="chat-input custom-input" placeholder="Type your message..." required></textarea>
            <button type="submit" class="btn btn-send w-100">
                <i class="fas fa-paper-plane me-2"></i>Send
            </button>
        </form>
    </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', function() {
    const chatToggleBtn = document.querySelector('.chat-toggle-btn');
    const chatBox = document.querySelector('.chat-box');
    const chatCloseBtn = document.querySelector('.chat-close-btn');
    const chatForm = document.getElementById('chatForm');

    chatToggleBtn.addEventListener('click', () => {
        chatBox.classList.toggle('hidden');
    });

    chatCloseBtn.addEventListener('click', () => {
        chatBox.classList.add('hidden');
    });

    chatForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const message = chatForm.querySelector('.chat-input').value.trim();
        
        if (!message) {
            showAlert('Please enter a message', 'warning');
            return;
        }
        
        fetch('/DexTV/admin/api/chat.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                message: message,
                timestamp: new Date().toISOString()
            })
        })
        .then(handleFetchError)
        .then(data => {
            chatForm.reset();
            chatBox.classList.add('hidden');
            showAlert('Message sent successfully');
        })
        .catch(error => console.error('Error:', error));
    });
});
</script>
