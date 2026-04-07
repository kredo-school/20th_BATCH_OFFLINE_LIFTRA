<!-- Add Milestone Modal -->
<div class="modal fade" id="addMilestoneModal" tabindex="-1" aria-labelledby="addMilestoneModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content p-3 border-0 shadow-lg rounded-4">

            <form action="{{ route('lifeplan.milestone.store') }}" method="POST">
                @csrf
                <input type="hidden" name="goal_id" value="{{ $goal->id }}">
                
                <div class="modal-body">
                    <h5 class="modal-title mb-4 fw-bold text-dark">Add Milestone</h5>
                    <!-- Milestone Title -->
                    <div class="mb-3">
                        <label class="fw-bold text-muted small text-uppercase mb-1 d-block">Milestone Title</label>
                        <input type="text" 
                               name="title" 
                               class="form-control border bg-white rounded-3 px-3 py-2" 
                               placeholder="e.g., Technical Skills" 
                               required>
                    </div>

                    <!-- Due Date -->
                    <div class="mb-4">
                        <label class="fw-bold text-muted small text-uppercase mb-1 d-block">Target Date</label>
                        <input type="date" 
                               name="due_date" 
                               class="form-control border bg-white rounded-3 px-3 py-2" 
                               required>
                    </div>

                    <!-- Actions Section -->
                    <div class="mb-3">
                        <label class="fw-bold text-muted small text-uppercase mb-1 d-flex justify-content-between align-items-center">
                            Actions (Tasks)
                            <button type="button" class="btn btn-sm btn-link text-primary text-decoration-none p-0" id="add-action-btn">
                                <i class="fa-solid fa-plus me-1"></i> Add Action
                            </button>
                        </label>
                        <div id="actions-container" class="d-flex flex-column gap-2 mt-2">
                            <!-- Template -->
                            <div class="row gx-2 mb-1 template-action" style="display: none;">
                                <div class="col-7">
                                    <input type="text" name="action_titles[]" class="form-control border bg-white rounded-3 px-3 py-2" placeholder="e.g., Master advanced React patterns">
                                </div>
                                <div class="col-4">
                                    <input type="date" name="action_dates[]" class="form-control border bg-white rounded-3 px-3 py-2">
                                </div>
                                <div class="col-1 d-flex align-items-center justify-content-center">
                                    <button type="button" class="btn btn-link text-danger remove-action p-0 m-0"><i class="fa-solid fa-xmark"></i></button>
                                </div>
                            </div>
                            
                            <!-- First visible line -->
                            <div class="row gx-2 mb-1 active-action-row">
                                <div class="col-7">
                                    <input type="text" name="action_titles[]" class="form-control rounded-3 py-2" placeholder="e.g., Master advanced React patterns">
                                </div>
                                <div class="col-4">
                                    <input type="date" name="action_dates[]" class="form-control rounded-3 py-2">
                                </div>
                                <div class="col-1 d-flex align-items-center justify-content-center">
                                    <button type="button" class="btn btn-link text-danger remove-action p-0 m-0" style="visibility: hidden;"><i class="fa-solid fa-xmark"></i></button>
                                </div>
                            </div>
                        </div>
                        <div class="form-text text-muted small mt-2">Break down this milestone into smaller, actionable tasks.</div>
                    </div>
                </div>

                <div class="text-end px-3 pb-3">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-semibold text-muted me-2" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">Save Milestone</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('actions-container');
    const addBtn = document.getElementById('add-action-btn');

    if (addBtn && container) {
        addBtn.addEventListener('click', function() {
            const templateGroup = container.querySelector('.template-action');
            const newGroup = templateGroup.cloneNode(true);
            
            newGroup.classList.remove('template-action');
            newGroup.classList.add('active-action-row');
            newGroup.style.display = 'flex'; // Turn un-hidden

            // Show remove button
            const removeBtn = newGroup.querySelector('.remove-action');
            removeBtn.style.visibility = 'visible';
            removeBtn.style.display = 'block';
            
            // Append to container
            container.appendChild(newGroup);

            // Add remove listener
            removeBtn.addEventListener('click', function() {
                newGroup.remove();
            });
        });
    }
});
</script>
