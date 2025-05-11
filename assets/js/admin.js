document.addEventListener('DOMContentLoaded', function() {
    // Add Channel
    document.getElementById('saveChannel').addEventListener('click', function() {
        const form = document.getElementById('addChannelForm');
        const name = form.querySelector('[name="name"]').value.trim();
        const url = form.querySelector('[name="url"]').value.trim();

        // Validasi form
        if (!name || !url) {
            showAlert('Channel name and URL are required', 'warning');
            return false;
        }
        
        const data = {
            name: name,
            url: url,
            logo: form.querySelector('[name="logo"]').value.trim(),
            status: 'active',
            category: new URLSearchParams(window.location.search).get('category') || 'news'
        };
        
        fetch('/DexTV/admin/api/channel.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        })
        .then(handleFetchError)
        .then(result => {
            if (result.success) {
                const modal = bootstrap.Modal.getInstance(document.getElementById('addChannelModal'));
                modal.hide();
                showActionAlert({
                    title: 'Success',
                    message: `Channel "${data.name}" has been added`,
                    type: 'success',
                    onClose: () => window.location.reload() // Reload setelah alert tertutup
                });
            }
        })
        .catch(error => showAlert('Failed to add channel', 'error'));
    });

    // Edit Channel
    document.querySelectorAll('.edit-channel').forEach(button => {
        button.addEventListener('click', function() {
            const channelId = this.dataset.id;
            // Populate form with channel data
            const row = this.closest('tr');
            const form = document.getElementById('editChannelForm');
            form.querySelector('[name="id"]').value = channelId;
            form.querySelector('[name="name"]').value = row.cells[1].textContent;
            form.querySelector('[name="url"]').value = row.cells[2].textContent;
            form.querySelector('[name="logo"]').value = row.cells[4].textContent; // Assuming logo is in the 5th column
            form.querySelector('[name="status"]').value = row.cells[3].querySelector('.badge').textContent.toLowerCase();
            
            new bootstrap.Modal(document.getElementById('editChannelModal')).show();
        });
    });

    // Update Channel
    document.getElementById('updateChannel').addEventListener('click', function() {
        const form = document.getElementById('editChannelForm');
        const name = form.querySelector('[name="name"]').value.trim();
        const url = form.querySelector('[name="url"]').value.trim();

        if (!name) {
            showAlert('Channel name is required', 'warning');
            return;
        }

        if (!url) {
            showAlert('Stream URL is required', 'warning');
            return;
        }

        const data = {
            id: form.querySelector('[name="id"]').value,
            name: name,
            url: url,
            logo: form.querySelector('[name="logo"]').value.trim(),
            status: form.querySelector('[name="status"]').value,
            category: new URLSearchParams(window.location.search).get('category') || 'news'
        };

        fetch('/DexTV/admin/api/channel.php', {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        })
        .then(handleFetchError)
        .then(result => {
            if (result.success) {
                const modal = bootstrap.Modal.getInstance(document.getElementById('editChannelModal'));
                modal.hide();
                showActionAlert({
                    title: 'Channel Updated',
                    message: `Successfully updated channel "${data.name}"`,
                    type: 'success'
                });
            }
        })
        .catch(error => showAlert('Failed to update channel', 'error'));
    });

    // Delete Channel
    document.querySelectorAll('.delete-channel').forEach(button => {
        button.addEventListener('click', function() {
            const channelId = this.dataset.id;
            showConfirmDialog({
                title: 'Delete Channel',
                message: 'Are you sure you want to delete this channel?',
                onConfirm: async () => {
                    try {
                        const response = await fetch(`/DexTV/admin/api/channel.php?id=${channelId}`, {
                            method: 'DELETE'
                        });
                        const data = await response.json();
                        if (data.success) {
                            location.reload();
                        }
                    } catch (error) {
                        showAlert('Failed to delete channel', 'error');
                    }
                }
            });
        });
    });

    // Confirm Delete
    document.getElementById('confirmDelete').addEventListener('click', function() {
        const channelId = document.getElementById('deleteChannelId').value;
        
        fetch(`/DexTV/admin/api/channel.php?id=${channelId}`, {
            method: 'DELETE'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    });
});

function handleFetchError(response) {
    if (!response.ok) {
        throw new Error('Network response was not ok');
    }
    return response.json();
}

function showAlert(message, type = 'success') {
    const alertBox = document.createElement('div');
    alertBox.className = `alert alert-${type}`;
    alertBox.textContent = message;
    document.body.appendChild(alertBox);
    setTimeout(() => alertBox.remove(), 3000);
}

function showConfirmDialog({ title, message, onConfirm }) {
    const confirmDialog = document.createElement('div');
    confirmDialog.className = 'modal fade';
    confirmDialog.id = 'confirmDialog';
    confirmDialog.tabIndex = -1;
    confirmDialog.innerHTML = `
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">${title}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>${message}</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirmButton">Confirm</button>
                </div>
            </div>
        </div>
    `;
    document.body.appendChild(confirmDialog);
    const modal = new bootstrap.Modal(confirmDialog);
    modal.show();

    confirmDialog.querySelector('#confirmButton').addEventListener('click', () => {
        onConfirm();
        modal.hide();
        confirmDialog.remove();
    });
}

function showFormDialog({ title, message, type, form, onConfirm }) {
    const formDialog = document.createElement('div');
    formDialog.className = 'modal fade';
    formDialog.id = 'formDialog';
    formDialog.tabIndex = -1;
    formDialog.innerHTML = `
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">${title}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>${message}</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-${type}" id="formConfirmButton">Confirm</button>
                </div>
            </div>
        </div>
    `;
    document.body.appendChild(formDialog);
    const modal = new bootstrap.Modal(formDialog);
    modal.show();

    formDialog.querySelector('#formConfirmButton').addEventListener('click', () => {
        onConfirm();
        modal.hide();
        formDialog.remove();
    });
}

function showActionAlert({ title, message, type, onClose }) {
    const alertBox = document.createElement('div');
    alertBox.className = `alert alert-${type}`;
    alertBox.innerHTML = `<strong>${title}</strong> ${message}`;
    document.body.appendChild(alertBox);
    setTimeout(() => {
        alertBox.remove();
        if (onClose) onClose();
    }, 3000);
}
