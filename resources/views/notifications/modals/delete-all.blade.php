
<!-- Delete All Notifications Modal -->
<div class="modal fade" id="deleteAllNotificationsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('notifications.destroyAll') }}" method="POST">
            @csrf
            @method('DELETE')

            <div class="modal-content p-3 border-0 shadow-lg rounded-4">
                <div class="modal-body text-center pt-4">
                    <div class="mb-3 text-danger">
                        <i class="fa-solid fa-trash-can fa-3x"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-3">Delete All Notifications</h5>
                    <p class="text-muted mb-4">Are you sure you want to clear all notifications?<br>This action cannot be undone.</p>
                </div>

                <div class="text-center px-3 pb-3">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-semibold text-muted me-2" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold shadow-sm">Delete All</button>
                </div>
            </div>
        </form>
    </div>
</div>
