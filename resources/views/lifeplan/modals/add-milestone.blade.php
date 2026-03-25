<!-- Add Milestone Modal -->
<div class="modal fade" id="addMilestoneModal" tabindex="-1" aria-labelledby="addMilestoneModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="addMilestoneModalLabel">Add Milestone</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('lifeplan.milestone.store') }}" method="POST">
                @csrf
                <input type="hidden" name="goal_id" value="{{ $goal->id }}">
                
                <div class="modal-body p-4 pt-3">
                    <!-- Milestone Title -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Milestone Title</label>
                        <input type="text" 
                               name="title" 
                               class="form-control rounded-3 py-2" 
                               placeholder="e.g., Technical Skills" 
                               required>
                    </div>

                    <!-- Due Date -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold small">Target Date</label>
                        <input type="date" 
                               name="due_date" 
                               class="form-control rounded-3 py-2" 
                               required>
                    </div>

                    <!-- Actions Section -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold small d-flex justify-content-between align-items-center">
                            Actions (Tasks)
                            <button type="button" class="btn btn-sm btn-link text-primary text-decoration-none p-0" id="add-action-btn">
                                <i class="fa-solid fa-plus me-1"></i> Add Action
                            </button>
                        </label>
                        <div id="actions-container" class="d-flex flex-column gap-2 mt-2">
                            <!-- Template -->
                            <div class="row gx-2 mb-1 template-action" style="display: none;">
                                <div class="col-7">
                                    <input type="text" name="action_titles[]" class="form-control rounded-3 py-2" placeholder="e.g., Master advanced React patterns">
                                </div>
                                <div class="col-4">
                                    <input type="date" name="action_dates[]" class="form-control rounded-3 py-2">
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

                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="submit" class="btn btn-primary w-100 rounded-3 py-2 fw-semibold" 
                            style="background-color: {{ $category->color->code ?? '#4F46E5' }}; border-color: {{ $category->color->code ?? '#4F46E5' }};">
                        Save Milestone
                    </button>
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
