<!-- Add Goal Modal -->
<div class="modal fade" id="addGoalModal" tabindex="-1" aria-labelledby="addGoalModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="addGoalModalLabel">Add Goal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('lifeplan.goal.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4 pt-3">

                    <!-- Goal Title -->
                    <div class="mb-3">
                        <input type="text"
                               name="title"
                               class="form-control rounded-3 py-2"
                               placeholder="Goal Title"
                               required>
                    </div>

                    <!-- Goal Details -->
                    <div class="mb-4">
                        <textarea name="description"
                                  class="form-control rounded-3 py-2"
                                  placeholder="Goal details..."
                                  rows="4"></textarea>
                    </div>

                    <!-- Category -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Category</label>
                        <select name="category_id" class="form-select rounded-3 py-2" required>
                            @foreach($userCategories as $cat)
                                <option value="{{ $cat->id }}" {{ $cat->id == $category->id ? 'selected' : '' }}>
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
                                   id="addGoalTargetAge"
                                   class="form-control rounded-3 py-2 target-age-input"
                                   min="{{ $userAge }}"
                                   value="{{ $userAge }}"
                                   required>
                            <div class="form-text text-muted">Must be {{ $userAge }} or older.</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Target Date</label>
                            <input type="date"
                                   name="target_date"
                                   id="addGoalTargetDate"
                                   class="form-control rounded-3 py-2 target-date-input"
                                   required>
                        </div>
                    </div>

                </div>

                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="submit" class="btn btn-primary w-100 rounded-3 py-2 fw-semibold"
                            style="background-color: #4F46E5; border-color: #4F46E5;">
                        Save Goal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const userBirthdayStr = "{{ Auth::check() ? Auth::user()->birthday : '' }}";
    if (!userBirthdayStr) return;
    
    // Parse user's birthday without timezone shifting issues
    const bdParts = userBirthdayStr.split('-');
    const birthday = new Date(bdParts[0], bdParts[1]-1, bdParts[2]);
    
    function syncAgeToDate(ageInput, dateInput) {
        let age = parseInt(ageInput.value);
        if (isNaN(age)) return;
        
        let targetYear = birthday.getFullYear() + age;
        let targetDate = new Date(targetYear, birthday.getMonth(), birthday.getDate());
        
        if (targetDate.getMonth() !== birthday.getMonth()) {
            targetDate = new Date(targetYear, birthday.getMonth() + 1, 0);
        }
        
        let y = targetDate.getFullYear();
        let m = String(targetDate.getMonth() + 1).padStart(2, '0');
        let d = String(targetDate.getDate()).padStart(2, '0');
        dateInput.value = `${y}-${m}-${d}`;
    }

    function syncDateToAge(dateInput, ageInput) {
        if (!dateInput.value) return;
        
        const tdParts = dateInput.value.split('-');
        let targetDate = new Date(tdParts[0], tdParts[1]-1, tdParts[2]);
        
        let age = targetDate.getFullYear() - birthday.getFullYear();
        let m = targetDate.getMonth() - birthday.getMonth();
        if (m < 0 || (m === 0 && targetDate.getDate() < birthday.getDate())) {
            age--;
        }
        
        ageInput.value = Math.max(age, 0);
    }

    // Set up listeners for Add Modal
    const addAgeInput = document.getElementById('addGoalTargetAge');
    const addDateInput = document.getElementById('addGoalTargetDate');
    
    if (addAgeInput && addDateInput) {
        addAgeInput.addEventListener('input', () => syncAgeToDate(addAgeInput, addDateInput));
        addDateInput.addEventListener('change', () => syncDateToAge(addDateInput, addAgeInput));
        
        syncAgeToDate(addAgeInput, addDateInput);
    }

    // Set up listeners for Edit Modal(s) by tracking dynamically injected modals using Event Delegation
    document.body.addEventListener('shown.bs.modal', function (event) {
        if (event.target.classList.contains('edit-goal-modal')) {
            const editAgeInput = event.target.querySelector('.target-age-input');
            const editDateInput = event.target.querySelector('.target-date-input');
            
            if (editAgeInput && editDateInput) {
                if (!editAgeInput.dataset.synced) {
                    editAgeInput.addEventListener('input', () => syncAgeToDate(editAgeInput, editDateInput));
                    editDateInput.addEventListener('change', () => syncDateToAge(editDateInput, editAgeInput));
                    editAgeInput.dataset.synced = "true";
                }
            }
        }
    });
});
</script>
