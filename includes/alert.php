<div class="alert-container"></div>

<script>
function showAlert(message, type = 'success') {
    const container = document.querySelector('.alert-container');
    const icons = {
        success: 'fas fa-check-circle',
        error: 'fas fa-times-circle',
        warning: 'fas fa-exclamation-circle'
    };

    const alert = document.createElement('div');
    alert.className = `custom-alert ${type}`;
    alert.innerHTML = `
        <i class="${icons[type]}"></i>
        <span>${message}</span>
    `;

    container.appendChild(alert);
    
    // Add show class after a small delay for animation
    setTimeout(() => alert.classList.add('show'), 100);

    // Remove alert after 3 seconds
    setTimeout(() => {
        alert.classList.remove('show');
        setTimeout(() => alert.remove(), 300);
    }, 3000);
}

// Global error handler for fetch requests
window.handleFetchError = async (response) => {
    const data = await response.json();
    if (!response.ok) {
        showAlert(data.error || 'An error occurred', 'error');
        throw new Error(data.error || 'An error occurred');
    }
    return data;
};

// Add confirmation dialog function
function showConfirmDialog({ title, message, confirmText = 'Delete', cancelText = 'Cancel', type = 'warning', onConfirm }) {
    const alert = document.createElement('div');
    alert.className = `custom-alert ${type}`;
    alert.innerHTML = `
        <i class="fas fa-exclamation-circle"></i>
        <div>
            <h6 class="mb-1">${title}</h6>
            <p class="mb-2">${message}</p>
            <div class="d-flex gap-2">
                <button class="btn btn-sm btn-outline-light px-3 cancel-btn">${cancelText}</button>
                <button class="btn btn-sm btn-danger px-3 confirm-btn">${confirmText}</button>
            </div>
        </div>
    `;
    
    document.querySelector('.alert-container').appendChild(alert);
    setTimeout(() => alert.classList.add('show'), 100);

    alert.querySelector('.cancel-btn').addEventListener('click', () => {
        alert.classList.remove('show');
        setTimeout(() => alert.remove(), 300);
    });

    alert.querySelector('.confirm-btn').addEventListener('click', async () => {
        await onConfirm();
        alert.classList.remove('show');
        setTimeout(() => alert.remove(), 300);
    });
}

// Add form confirmation dialog function
function showFormDialog({ title, message, type = 'warning', form, onConfirm }) {
    const alert = document.createElement('div');
    alert.className = `custom-alert ${type}`;
    alert.innerHTML = `
        <i class="fas fa-exclamation-circle"></i>
        <div>
            <h6 class="mb-1">${title}</h6>
            <p class="mb-2">${message}</p>
            <div class="text-muted small mb-3">
                <strong>Channel:</strong> ${form.querySelector('[name="name"]').value}<br>
                <strong>URL:</strong> ${form.querySelector('[name="url"]').value}
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-sm btn-outline-light px-3 cancel-btn">Cancel</button>
                <button class="btn btn-sm btn-accent px-3 confirm-btn">Confirm</button>
            </div>
        </div>
    `;
    
    document.querySelector('.alert-container').appendChild(alert);
    setTimeout(() => alert.classList.add('show'), 100);

    alert.querySelector('.cancel-btn').addEventListener('click', () => {
        alert.classList.remove('show');
        setTimeout(() => alert.remove(), 300);
    });

    alert.querySelector('.confirm-btn').addEventListener('click', async () => {
        await onConfirm();
        alert.classList.remove('show');
        setTimeout(() => alert.remove(), 300);
    });
}

// Add function for action confirmation alert
function showActionAlert({ title, message, type = 'success', onClose = null }) {
    const alert = document.createElement('div');
    alert.className = `custom-alert ${type}`;
    alert.innerHTML = `
        <i class="fas fa-check-circle"></i>
        <div>
            <h6 class="mb-1">${title}</h6>
            <p class="mb-2">${message}</p>
            <div class="text-muted small mb-3">
                <strong>Status:</strong> Success
            </div>
        </div>
    `;
    
    document.querySelector('.alert-container').appendChild(alert);
    setTimeout(() => alert.classList.add('show'), 100);
    setTimeout(() => {
        alert.classList.remove('show');
        setTimeout(() => {
            alert.remove();
            if (onClose) onClose(); // Execute callback if provided
        }, 300);
    }, 2000);
}

// Add CSS for new button type
document.head.insertAdjacentHTML('beforeend', `
<style>
.btn-accent {
    background: var(--gradient-accent);
    color: var(--color-text);
    border: none;
    box-shadow: var(--shadow-soft);
}

.btn-accent:hover {
    background: var(--gradient-hover);
    transform: translateY(-1px);
    box-shadow: var(--shadow-strong);
    color: var(--color-text);
}
</style>
`);
</script>
