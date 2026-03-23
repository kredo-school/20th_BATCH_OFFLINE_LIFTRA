<!-- Edit Category Modal -->
<div class="modal fade" id="editCategoryModal{{ $category->id }}" tabindex="-1" aria-labelledby="editCategoryModalLabel{{ $category->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="editCategoryModalLabel{{ $category->id }}">Edit Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form action="{{ route('lifeplan.category.update', $category->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4 pt-3">
                    
                    <!-- Category name -->
                    <div class="mb-4">
                        <label class="form-label text-muted small fw-semibold">Category name</label>
                        <input type="text" name="name" class="form-control rounded-3 py-2 border-1" placeholder="e.g. Work" value="{{ $category->name }}" required>
                    </div>

                    <!-- Icon -->
                    <div class="mb-4">
                        <label class="form-label text-muted small fw-semibold">Icon</label>
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
                        <label class="form-label text-muted small fw-semibold">Color</label>
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
                
                <div class="modal-footer border-0 p-4 pt-0 mt-2">
                    <button type="submit" class="btn btn-primary w-100 rounded-3 py-2 fw-semibold" style="background-color: #4F46E5; border-color: #4F46E5;">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
