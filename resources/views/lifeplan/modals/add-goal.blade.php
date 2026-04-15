<!-- Add Goal Modal -->
<div class="modal fade" id="addGoalModal" tabindex="-1" aria-labelledby="addGoalModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mx-3 mx-sm-auto">
        <div class="modal-content p-3 border-0 shadow-lg rounded-4">

            <form action="{{ route('lifeplan.goal.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <h5 class="modal-title mb-4 fw-bold text-dark">{{ __('Add Goal') }}</h5>

                    <!-- Goal Title -->
                    <div class="mb-3">
                        <label class="fw-bold text-muted small text-uppercase mb-1 d-block">{{ __('Goal Title') }}</label>
                        <input type="text"
                               name="title"
                               class="form-control border bg-white rounded-3 px-3 py-2"
                               placeholder="{{ __('Goal Title') }}"
                               required>
                    </div>

                    <!-- Goal Details -->
                    <div class="mb-4">
                        <label class="fw-bold text-muted small text-uppercase mb-1 d-block">{{ __('Goal Details') }}</label>
                        <textarea name="description"
                                  class="form-control border bg-white rounded-3 px-3 py-2"
                                  placeholder="{{ __('Goal details...') }}"
                                  rows="4"></textarea>
                    </div>

                    <!-- Category -->
                    <div class="mb-3">
                        <label class="fw-bold text-muted small text-uppercase mb-1 d-block">{{ __('Category') }}</label>
                        <select name="category_id" class="form-select border bg-white rounded-3 px-3 py-2" required>
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
                            <label class="fw-bold text-muted small text-uppercase mb-1 d-block">{{ __('Target Age') }}</label>
                            <input type="number"
                                   name="target_age"
                                   id="addGoalTargetAge"
                                   class="form-control border bg-white rounded-3 px-3 py-2 target-age-input"
                                   min="{{ $userAge }}"
                                   value="{{ $userAge }}"
                                   required>
                            <div class="form-text text-muted">{{ __('Must be :age or older.', ['age' => $userAge]) }}</div>
                        </div>

                        <div class="col-md-6">
                            <label class="fw-bold text-muted small text-uppercase mb-1 d-block">{{ __('Target Date') }}</label>
                            <input type="date"
                                   name="target_date"
                                   id="addGoalTargetDate"
                                   class="form-control border bg-white rounded-3 px-3 py-2 target-date-input"
                                   required>
                        </div>
                    </div>

                </div>

                <div class="text-end px-3 pb-3">
                    @if(is_null($userAge))
                        <div class="alert alert-warning rounded-3 w-100 mb-3 small py-2 text-start">
                            <i class="fa-solid fa-circle-info me-2"></i>
                            {{ __('Please enter your birthday to create a goal.') }}
                        </div>
                        <button type="button" class="btn btn-light rounded-pill px-4 fw-semibold text-muted me-2" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="button" class="btn btn-secondary rounded-pill px-4 fw-semibold" disabled>{{ __('Save') }}</button>
                    @else
                        <button type="button" class="btn btn-light rounded-pill px-4 fw-semibold text-muted me-2" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">{{ __('Save') }}</button>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const userBirthdayStr = "{{ Auth::check() ? Auth::user()->birthday : '' }}";
    if (!userBirthdayStr) return;
    
    // Parse user's birthday without timezone shifting issues, handle H:i:s if present
    const bdParts = userBirthdayStr.split('-');
    const dayPart = bdParts[2] ? bdParts[2].split(' ')[0] : '1';
    const birthday = new Date(bdParts[0], bdParts[1]-1, dayPart);
    
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
        addDateInput.addEventListener('input', () => syncDateToAge(addDateInput, addAgeInput));
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
                    editDateInput.addEventListener('input', () => syncDateToAge(editDateInput, editAgeInput));
                    editDateInput.addEventListener('change', () => syncDateToAge(editDateInput, editAgeInput));
                    editAgeInput.dataset.synced = "true";
                }
            }
        }
    });
});
</script>
