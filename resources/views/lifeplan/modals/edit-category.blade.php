<!-- Edit Category Modal -->
<div class="modal fade" id="editCategoryModal{{ $category->id }}" tabindex="-1" aria-labelledby="editCategoryModalLabel{{ $category->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content p-3 border-0 shadow-lg rounded-4">
            
            <form action="{{ route('lifeplan.category.update', $category->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <h5 class="modal-title mb-4 fw-bold text-dark">Edit Category</h5>
                    
                    <!-- Category name -->
                    <div class="mb-4">
                        <label class="fw-bold text-muted small text-uppercase mb-1 d-block">Category name</label>
                        <input type="text" name="name" class="form-control border bg-white rounded-3 px-3 py-2" placeholder="e.g. Work" value="{{ $category->name }}" required>
                    </div>

                    <!-- Icon -->
                    <div class="mb-4">
                        <label class="fw-bold text-muted small text-uppercase mb-1 d-block">Icon</label>
                        <div class="d-flex flex-wrap gap-2 icon-selection-grid">
                            @php
                                $presetIcons = [
                                    1 => 'fa-folder', 2 => 'fa-book', 3 => 'fa-briefcase', 4 => 'fa-house',
                                    5 => 'fa-dumbbell', 6 => 'fa-heart', 7 => 'fa-bullseye', 8 => 'fa-pen',
                                    9 => 'fa-mug-hot', 10 => 'fa-cart-shopping', 11 => 'fa-plane', 12 => 'fa-music'
                                ];
                            @endphp
                            @foreach($presetIcons as $id => $iconClass)
                                <label class="icon-radio-label">
                                    <input type="radio" name="icon_id" value="{{ $id }}" class="d-none icon-radio" required {{ $category->icon_id == $id ? 'checked' : '' }}>
                                    <div class="icon-box rounded-3 d-flex align-items-center justify-content-center cursor-pointer">
                                        <i class="fa-solid {{ $iconClass }} fs-5"></i>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Color -->
                    <div class="mb-4">
                        <label class="fw-bold text-muted small text-uppercase mb-1 d-block">Color</label>
                        <div class="d-flex flex-wrap gap-2 color-selection-grid">
                            @php
                                $presetColors = [
                                    1 => '#6366F1', // Blue
                                    2 => '#22C55E', // Green
                                    3 => '#FBBF24', // Yellow
                                    4 => '#EF4444', // Red
                                    5 => '#A855F7', // Purple
                                    6 => '#38BDF8', // Light Blue/Teal
                                    7 => '#F97316', // Orange
                                    8 => '#4B5563', // Dark Gray
                                ];
                            @endphp
                            @foreach($presetColors as $id => $hex)
                                <label class="color-radio-label">
                                    <input type="radio" name="color_id" value="{{ $id }}" class="d-none color-radio" required {{ $category->color_id == $id ? 'checked' : '' }}>
                                    <div class="color-box rounded-circle mx-1 cursor-pointer" style="background-color: {{ $hex }}; width: 32px; height: 32px; border: 2px solid transparent;"></div>
                                </label>
                            @endforeach
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
