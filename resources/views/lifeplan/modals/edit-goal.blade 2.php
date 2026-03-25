<!-- Edit Goal Modal -->
<div class="modal fade edit-goal-modal" id="editGoalModal{{ $goal->id }}" tabindex="-1" aria-labelledby="editGoalModalLabel{{ $goal->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="editGoalModalLabel{{ $goal->id }}">Edit Goal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('lifeplan.goal.update', $goal->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4 pt-3">

                    <!-- Goal Title -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Goal Title</label>
                        <input type="text"
                               name="title"
                               class="form-control rounded-3 py-2"
                               placeholder="Goal Title"
                               value="{{ $goal->title }}"
                               required>
                    </div>

                    <!-- Goal Details -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Goal details</label>
                        <textarea name="description"
                                  class="form-control rounded-3 py-2"
                                  placeholder="Goal details..."
                                  rows="4">{{ $goal->description }}</textarea>
                    </div>

                    <!-- Category -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Category</label>
                        <select name="category_id" class="form-select rounded-3 py-2" required>
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
                            <label class="form-label fw-semibold">Target Age</label>
                            <input type="number"
                                   name="target_age"
                                   id="editGoalTargetAge{{ $goal->id }}"
                                   class="form-control rounded-3 py-2 target-age-input"
                                   min="{{ $userAge }}"
                                   value="{{ $goal->target_age }}"
                                   required>
                            <div class="form-text text-muted">Must be {{ $userAge }} or older.</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Target Date</label>
                            <input type="date"
                                   name="target_date"
                                   id="editGoalTargetDate{{ $goal->id }}"
                                   class="form-control rounded-3 py-2 target-date-input"
                                   value="{{ $goal->target_date ? $goal->target_date->format('Y-m-d') : '' }}"
                                   required>
                        </div>
                    </div>

                </div>

                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="submit" class="btn btn-primary w-100 rounded-3 py-2 fw-semibold"
                            style="background-color: #4F46E5; border-color: #4F46E5;">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
