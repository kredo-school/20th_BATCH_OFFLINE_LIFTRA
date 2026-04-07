<!-- Edit Goal Modal -->
<div class="modal fade edit-goal-modal" id="editGoalModal{{ $goal->id }}" tabindex="-1" aria-labelledby="editGoalModalLabel{{ $goal->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mx-3 mx-sm-auto">
        <div class="modal-content p-3 border-0 shadow-lg rounded-4">

            <form action="{{ route('lifeplan.goal.update', $goal->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <h5 class="modal-title mb-4 fw-bold text-dark">Edit Goal</h5>

                    <!-- Goal Title -->
                    <div class="mb-3">
                        <label class="fw-bold text-muted small text-uppercase mb-1 d-block">Goal Title</label>
                        <input type="text"
                               name="title"
                               class="form-control border bg-white rounded-3 px-3 py-2"
                               placeholder="Goal Title"
                               value="{{ $goal->title }}"
                               required>
                    </div>

                    <!-- Goal Details -->
                    <div class="mb-4">
                        <label class="fw-bold text-muted small text-uppercase mb-1 d-block">Goal Details</label>
                        <textarea name="description"
                                  class="form-control border bg-white rounded-3 px-3 py-2"
                                  placeholder="Goal details..."
                                  rows="4">{{ $goal->description }}</textarea>
                    </div>

                    <!-- Category -->
                    <div class="mb-3">
                        <label class="fw-bold text-muted small text-uppercase mb-1 d-block">Category</label>
                        <select name="category_id" class="form-select border bg-white rounded-3 px-3 py-2" required>
                            @foreach($userCategories as $cat)
                                <option value="{{ $cat->id }}" {{ $cat->id == $goal->category_id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Target Age & Target Date -->
                    <div class="row mb-4">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <label class="fw-bold text-muted small text-uppercase mb-1 d-block">Target Age</label>
                            <input type="number"
                                   name="target_age"
                                   id="editGoalTargetAge{{ $goal->id }}"
                                   class="form-control border bg-white rounded-3 px-3 py-2 target-age-input"
                                   min="{{ $userAge }}"
                                   value="{{ $goal->target_age }}"
                                   required>
                            <div class="form-text text-muted">Must be {{ $userAge }} or older.</div>
                        </div>

                        <div class="col-md-6">
                            <label class="fw-bold text-muted small text-uppercase mb-1 d-block">Target Date</label>
                            <input type="date"
                                   name="target_date"
                                   id="editGoalTargetDate{{ $goal->id }}"
                                   class="form-control border bg-white rounded-3 px-3 py-2 target-date-input"
                                   value="{{ $goal->target_date ? $goal->target_date->format('Y-m-d') : '' }}"
                                   required>
                        </div>
                    </div>

                </div>

                <div class="text-end px-3 pb-3">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-semibold text-muted me-2" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
