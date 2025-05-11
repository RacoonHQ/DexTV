<!-- Add Channel Modal -->
<div class="modal fade" id="addChannelModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header border-bottom border-dark">
                <h5 class="modal-title"><i class="fas fa-plus-circle me-2"></i>Add New Channel</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addChannelForm">
                    <div class="mb-4">
                        <label class="form-label text-muted">Channel Name*</label>
                        <div class="input-group">
                            <span class="input-group-text bg-dark border-0"><i class="fas fa-tv"></i></span>
                            <input type="text" class="form-control custom-input" name="name" placeholder="Channel name" required>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label text-muted">Stream URL*</label>
                        <div class="input-group">
                            <span class="input-group-text bg-dark border-0"><i class="fas fa-link"></i></span>
                            <input type="url" class="form-control custom-input" name="url" placeholder="https://example.com/stream.m3u8" required>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label text-muted">Logo (Optional)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-dark border-0"><i class="fas fa-image"></i></span>
                            <input type="text" class="form-control custom-input" name="logo" placeholder="logo.png">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-top border-dark">
                <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary custom-btn" id="saveChannel">Save Channel</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Channel Modal -->
<div class="modal fade" id="editChannelModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header border-bottom border-dark">
                <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Channel</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editChannelForm">
                    <input type="hidden" name="id">
                    <div class="mb-4">
                        <label class="form-label text-muted">Channel Name</label>
                        <div class="input-group">
                            <span class="input-group-text bg-dark border-0"><i class="fas fa-tv"></i></span>
                            <input type="text" class="form-control custom-input" name="name" required>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label text-muted">Stream URL</label>
                        <div class="input-group">
                            <span class="input-group-text bg-dark border-0"><i class="fas fa-link"></i></span>
                            <input type="url" class="form-control custom-input" name="url" required>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label text-muted">Logo</label>
                        <div class="input-group">
                            <span class="input-group-text bg-dark border-0"><i class="fas fa-image"></i></span>
                            <input type="text" class="form-control custom-input" name="logo">
                        </div>
                    </div>
                    <input type="hidden" name="status"> <!-- Keep this to maintain current status -->
                </form>
            </div>
            <div class="modal-footer border-top border-dark">
                <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary custom-btn" id="updateChannel">Update Channel</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteChannelModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0">
            <div class="modal-header border-0">
                <h5 class="modal-title"><i class="fas fa-trash-alt text-danger me-2"></i>Delete Channel</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-4">
                <p class="mb-1">Are you sure you want to delete this channel?</p>
                <p class="text-muted small">This action cannot be undone.</p>
                <input type="hidden" id="deleteChannelId">
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap & jQuery Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
let isSubmitting = false;
const modalStates = new WeakMap();

function initModal(modalId) {
    const modal = document.getElementById(modalId);
    if (!modalStates.has(modal)) {
        const state = {
            isOpen: false,
            form: modal.querySelector('form'),
            submitBtn: modal.querySelector('.custom-btn'),
        };
        modalStates.set(modal, state);
        
        // Reset form on modal close
        modal.addEventListener('hidden.bs.modal', () => {
            state.form.reset();
            isSubmitting = false;
        });
    }
    return modalStates.get(modal);
}

// Save Channel
function saveChannelHandler() {
    if (isSubmitting) return;
    
    const modalState = initModal('addChannelModal');
    const form = modalState.form;
    
    if (!validateForm(form)) {
        return;
    }

    isSubmitting = true;
    modalState.submitBtn.disabled = true;

    const data = getFormData(form);
    
    fetch('/DexTV/admin/api/channel.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    })
    .then(handleFetchError)
    .then(result => {
        if (result.success) {
            showSuccessAndReload('Channel added successfully');
        }
    })
    .catch(error => {
        showAlert(error.message || 'Failed to add channel', 'error');
        isSubmitting = false;
        modalState.submitBtn.disabled = false;
    });
}

// Update Channel 
function updateChannelHandler() {
    if (isSubmitting) return;
    
    const modalState = initModal('editChannelModal');
    const form = modalState.form;
    
    if (!validateForm(form)) {
        return;
    }

    isSubmitting = true;
    modalState.submitBtn.disabled = true;

    const data = getFormData(form);
    
    fetch('/DexTV/admin/api/channel.php', {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    })
    .then(handleFetchError)
    .then(result => {
        if (result.success) {
            showSuccessAndReload('Channel updated successfully');
        }
    })
    .catch(error => {
        showAlert(error.message || 'Failed to update channel', 'error');
        isSubmitting = false;
        modalState.submitBtn.disabled = false;
    });
}

function validateForm(form) {
    const name = form.querySelector('[name="name"]').value.trim();
    const url = form.querySelector('[name="url"]').value.trim();

    if (!name || !url) {
        showAlert('Channel name and URL are required', 'warning');
        return false;
    }

    if (!isValidUrl(url)) {
        showAlert('Please enter a valid URL', 'warning');
        return false;
    }

    return true;
}

function getFormData(form) {
    return {
        id: form.querySelector('[name="id"]')?.value,
        name: form.querySelector('[name="name"]').value.trim(),
        url: form.querySelector('[name="url"]').value.trim(),
        logo: form.querySelector('[name="logo"]').value.trim(),
        status: form.querySelector('[name="status"]')?.value || 'active',
        category: new URLSearchParams(window.location.search).get('category') || 'news'
    };
}

function isValidUrl(url) {
    try {
        new URL(url);
        return true;
    } catch {
        return false;
    }
}

function showSuccessAndReload(message) {
    const modal = bootstrap.Modal.getInstance(document.querySelector('.modal.show'));
    if (modal) {
        modal.hide();
    }
    showAlert(message, 'success');
    setTimeout(() => window.location.reload(), 1000);
}

// Clean up old listeners and add new ones
document.addEventListener('DOMContentLoaded', () => {
    const saveBtn = document.getElementById('saveChannel');
    const updateBtn = document.getElementById('updateChannel');
    
    saveBtn?.removeEventListener('click', saveChannelHandler);
    updateBtn?.removeEventListener('click', updateChannelHandler);
    
    saveBtn?.addEventListener('click', saveChannelHandler);
    updateBtn?.addEventListener('click', updateChannelHandler);
});

document.addEventListener('DOMContentLoaded', function() {
    // Hapus event listener lama jika ada
    const updateBtn = document.getElementById('updateChannel');
    const oldUpdateHandler = updateBtn.getAttribute('onclick');
    if (oldUpdateHandler) {
        updateBtn.removeAttribute('onclick');
    }

    // Tambahkan event listener baru
    updateBtn.addEventListener('click', async function() {
        const form = document.getElementById('editChannelForm');
        const category = new URLSearchParams(window.location.search).get('category') || 'news';
        
        const formData = {
            id: form.querySelector('[name="id"]').value,
            name: form.querySelector('[name="name"]').value.trim(),
            url: form.querySelector('[name="url"]').value.trim(),
            logo: form.querySelector('[name="logo"]').value.trim(),
            status: form.querySelector('[name="status"]').value,
            category: category
        };

        console.log('Updating channel:', formData); // Debug log

        try {
            const response = await fetch('/DexTV/admin/api/channel.php', {
                method: 'PUT',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify(formData)
            });

            const result = await response.json();
            console.log('Server response:', result); // Debug log
            
            if (result.success) {
                const modal = bootstrap.Modal.getInstance(document.getElementById('editChannelModal'));
                modal.hide();
                showAlert('Channel updated successfully', 'success');
                window.location.reload(); // Force reload halaman
            } else {
                throw new Error(result.error || 'Failed to update channel');
            }
        } catch (error) {
            console.error('Error:', error);
            showAlert('Error updating channel: ' + error.message, 'error');
        }
    });
});
</script>
