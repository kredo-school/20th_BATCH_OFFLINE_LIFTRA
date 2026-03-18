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

                    <!-- Target Age -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Target Age</label>
                        <input type="number"
                               name="target_age"
                               class="form-control rounded-3 py-2"
                               min="{{ $userAge }}"
                               value="{{ $userAge }}"
                               required>
                        <div class="form-text text-muted">Must be {{ $userAge }} or older (your current age).</div>
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
