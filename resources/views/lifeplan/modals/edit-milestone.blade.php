<!-- Edit Milestone Modal -->
<div class="modal fade" id="editMilestone{{ $milestone->id }}" tabindex="-1" aria-labelledby="editMilestoneModalLabel{{ $milestone->id }}" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="editMilestoneModalLabel{{ $milestone->id }}">Edit Milestone</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('lifeplan.milestone.update', $milestone->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="modal-body p-4 pt-3">
                    <!-- Milestone Title -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Milestone Title</label>
                        <input type="text" 
                               name="title" 
                               class="form-control rounded-3 py-2" 
                               value="{{ $milestone->title }}" 
                               required>
                    </div>

                    <!-- Due Date -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold small">Target Date</label>
                        <input type="date" 
                               name="due_date" 
                               class="form-control rounded-3 py-2" 
                               value="{{ $milestone->due_date ? $milestone->due_date->format('Y-m-d') : '' }}"
                               required>
                    </div>

                    <!-- Existing Actions Section -->
                    @if($mActions->count() > 0)
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Existing Actions</label>
                        <div class="d-flex flex-column gap-2 mb-2">
                            @foreach($mActions as $action)
                                <div class="row gx-2">
                                    <div class="col-7">
                                        <input type="text" name="actions[{{ $action->id }}][title]" class="form-control rounded-3 py-2" value="{{ $action->title }}" placeholder="Clear to delete">
                                    </div>
                                    <div class="col-5">
                                        <input type="date" name="actions[{{ $action->id }}][due_date]" class="form-control rounded-3 py-2" value="{{ $action->due_date ? $action->due_date->format('Y-m-d') : '' }}">
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="form-text text-muted small"><i class="fa-solid fa-circle-info me-1"></i> Clear an action's text completely to delete it safely.</div>
                    </div>
                    @endif

                    <!-- Add New Actions Section -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold small d-flex justify-content-between align-items-center">
                            New Actions
                            <button type="button" class="btn btn-sm btn-link text-primary text-decoration-none p-0 add-action-btn-edit" data-milestone="{{ $milestone->id }}">
                                <i class="fa-solid fa-plus me-1"></i> Add Action
                            </button>
                        </label>
                        <div id="actions-container-edit-{{ $milestone->id }}" class="d-flex flex-column gap-2">
                            <!-- Template for cloning -->
                            <div class="row gx-2 mb-1 template-action-edit" style="display: none;">
                                <div class="col-7">
                                    <input type="text" name="new_action_titles[]" class="form-control rounded-3 py-2" placeholder="e.g., Master advanced React patterns">
                                </div>
                                <div class="col-4">
                                    <input type="date" name="new_action_dates[]" class="form-control rounded-3 py-2">
                                </div>
                                <div class="col-1 d-flex align-items-center">
                                    <button type="button" class="btn btn-link text-danger remove-action-edit p-0"><i class="fa-solid fa-xmark"></i></button>
                                </div>
                            </div>
                            
                            <!-- Default visible row -->
                            <div class="row gx-2 mb-1 active-action-row">
                                <div class="col-7">
                                    <input type="text" name="new_action_titles[]" class="form-control rounded-3 py-2" placeholder="e.g., Learn React hooks">
                                </div>
                                <div class="col-4">
                                    <input type="date" name="new_action_dates[]" class="form-control rounded-3 py-2">
                                </div>
                                <div class="col-1 d-flex align-items-center">
                                    <button type="button" class="btn btn-link text-danger remove-action-edit p-0" style="visibility: hidden;"><i class="fa-solid fa-xmark"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="submit" class="btn btn-primary w-100 rounded-3 py-2 fw-semibold" 
                            style="background-color: {{ $category->color->code ?? '#4F46E5' }}; border-color: {{ $category->color->code ?? '#4F46E5' }};">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Local script tag ensures multiple edit modals don't conflict, using unique IDs
    document.addEventListener('DOMContentLoaded', function() {
        const milestoneId = "{{ $milestone->id }}";
        const container = document.getElementById(`actions-container-edit-${milestoneId}`);
        const addBtn = document.querySelector(`.add-action-btn-edit[data-milestone="${milestoneId}"]`);
        
        if (addBtn && container) {
            addBtn.addEventListener('click', function() {
                const template = container.querySelector('.template-action-edit');
                const newGroup = template.cloneNode(true);
                
                newGroup.classList.remove('template-action-edit');
                newGroup.classList.add('active-action-row');
                newGroup.style.display = 'flex'; // Unhide cloned row
                
                // Show remove button
                const removeBtn = newGroup.querySelector('.remove-action-edit');
                removeBtn.style.visibility = 'visible';
                
                // Append
                container.appendChild(newGroup);

                // Add remote listener to cloned btn
                removeBtn.addEventListener('click', function() {
                    newGroup.remove();
                });
            });
        }
    });
</script>
