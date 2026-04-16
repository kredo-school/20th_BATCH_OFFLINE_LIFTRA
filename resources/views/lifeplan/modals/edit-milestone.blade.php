<!-- Edit Milestone Modal -->
<div class="modal fade" id="editMilestone{{ $milestone->id }}" tabindex="-1" aria-labelledby="editMilestoneModalLabel{{ $milestone->id }}" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered mx-3 mx-sm-auto">
        <div class="modal-content p-3 border-0 shadow-lg rounded-4">

            <form action="{{ route('lifeplan.milestone.update', $milestone->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="modal-body">
                    <h5 class="modal-title mb-4 fw-bold text-dark">{{ __('Edit Milestone') }}</h5>
                    <!-- Milestone Title -->
                    <div class="mb-3">
                        <label class="fw-bold text-muted small text-uppercase mb-1 d-block">{{ __('Milestone Title') }}</label>
                        <input type="text" 
                               name="title" 
                               class="form-control border bg-white rounded-3 px-3 py-2" 
                               value="{{ $milestone->title }}" 
                               required>
                    </div>

                    <!-- Due Date -->
                    <div class="mb-4">
                        <label class="fw-bold text-muted small text-uppercase mb-1 d-block">{{ __('Target Date') }}</label>
                        <input type="date" 
                               name="due_date" 
                               class="form-control border bg-white rounded-3 px-3 py-2" 
                               value="{{ $milestone->due_date ? $milestone->due_date->format('Y-m-d') : '' }}"
                               required>
                    </div>

                    <!-- Existing Actions Section -->
                    @if($mActions->count() > 0)
                    <div class="mb-3">
                        <label class="fw-bold text-muted small text-uppercase mb-1 d-block">{{ __('Existing Actions') }}</label>
                        <div class="d-flex flex-column gap-2 mb-2">
                            @foreach($mActions as $action)
                                <div class="row gx-2">
                                    <div class="col-7">
                                        <input type="text" name="actions[{{ $action->id }}][title]" class="form-control border bg-white rounded-3 px-3 py-2" value="{{ $action->title }}" placeholder="{{ __('Clear to delete') }}">
                                    </div>
                                    <div class="col-5">
                                        <input type="date" name="actions[{{ $action->id }}][due_date]" class="form-control border bg-white rounded-3 px-3 py-2" value="{{ $action->due_date ? $action->due_date->format('Y-m-d') : '' }}">
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="form-text text-muted small"><i class="fa-solid fa-circle-info me-1"></i> {{ __('Clear an action\'s text completely to delete it safely.') }}</div>
                    </div>
                    @endif

                    <!-- Add New Actions Section -->
                    <div class="mb-3">
                        <label class="fw-bold text-muted small text-uppercase mb-1 d-flex justify-content-between align-items-center">
                            {{ __('New Actions') }}
                            <button type="button" class="btn btn-sm btn-link text-primary text-decoration-none p-0 add-action-btn-edit" data-milestone="{{ $milestone->id }}">
                                <i class="fa-solid fa-plus me-1"></i> {{ __('Add Action') }}
                            </button>
                        </label>
                        <div id="actions-container-edit-{{ $milestone->id }}" class="d-flex flex-column gap-2">
                            <!-- Template for cloning -->
                            <div class="row gx-2 mb-1 template-action-edit" style="display: none;">
                                <div class="col-7">
                                    <input type="text" name="new_action_titles[]" class="form-control border bg-white rounded-3 px-3 py-2" placeholder="{{ __('e.g., Master advanced React patterns') }}">
                                </div>
                                <div class="col-4">
                                    <input type="date" name="new_action_dates[]" class="form-control border bg-white rounded-3 px-3 py-2">
                                </div>
                                <div class="col-1 d-flex align-items-center">
                                    <button type="button" class="btn btn-link text-danger remove-action-edit p-0"><i class="fa-solid fa-xmark"></i></button>
                                </div>
                            </div>
                            
                            <!-- Default visible row -->
                            <div class="row gx-2 mb-1 active-action-row">
                                <div class="col-7">
                                    <input type="text" name="new_action_titles[]" class="form-control border bg-white rounded-3 px-3 py-2" placeholder="{{ __('e.g., Learn React hooks') }}">
                                </div>
                                <div class="col-4">
                                    <input type="date" name="new_action_dates[]" class="form-control border bg-white rounded-3 px-3 py-2">
                                </div>
                                <div class="col-1 d-flex align-items-center">
                                    <button type="button" class="btn btn-link text-danger remove-action-edit p-0" style="visibility: hidden;"><i class="fa-solid fa-xmark"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-end px-3 pb-3">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-semibold text-muted me-2 shadow-sm border" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">{{ __('Update') }}</button>
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
