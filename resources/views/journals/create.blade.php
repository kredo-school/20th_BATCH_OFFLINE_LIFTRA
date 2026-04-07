<div class="container-fluid p-4">
    <div class="row justify-content-center">
        <div class="col-md-10 ">
            <div class="bg-white rounded-4 shadow-sm p-3 p-md-4 border">
                <h4 class="fw-bold my-2">Write New Entry</h4>

                <form action="{{ route('journals.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row mb-2 mb-md-3">
                        <div class="col-md-3">
                            <label class="form-label fw-bold text-muted small mb-0">Entry Date</label>
                            <input type="date"
                                class="form-control form-control-sm border-light shadow-sm @error('entry_date') is-invalid @enderror"
                                name="entry_date" value="{{ old('entry_date', date('Y-m-d')) }}" required>
                            @error('entry_date')
                                <div class="invalid-feedback fw-bold">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3 mt-2 mt-md-0">
                            <label class="form-label fw-bold text-muted small mb-0">How was your day?</label>
                            <div class="d-flex my-auto align-items-center gap-1 text-warning fs-3 rating-selector" id="star-rating">
                                <input type="hidden" name="rating" id="rating-input" value="{{ old('rating', 3) }}">
                                @for($i=1; $i<=5; $i++)
                                    <i class="{{ $i <= old('rating', 3) ? 'fa-solid' : 'fa-regular' }} fa-star cursor-pointer star" data-value="{{ $i }}"></i>
                                @endfor
                            </div>
                        </div>
                        <div class="col-md-12 mt-2">
                            <label class="form-label fw-bold text-muted small mb-0">Title</label>
                            <input type="text" class="form-control form-control-sm border-light shadow-sm mt-0"
                                name="title" value="{{ old('title') }}" placeholder="A great breakthrough..." required>
                        </div>
                    </div>

                    

                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted small mb-0">Content</label>
                        <textarea class="form-control border-light shadow-sm" name="content" rows="12"
                            placeholder="Start writing your thoughts..." required style="resize: none;">{{ old('content') }}</textarea>
                    </div>

                    <div class="mb-5">
                        <label class="form-label fw-bold text-muted small mb-0">Attach Image (Optional)</label>
                        <input class="form-control border-light shadow-sm @error('image') is-invalid @enderror" type="file" name="image" id="imageInput"
                            accept="image/jpeg, image/png, image/jpg, image/gif, image/webp">
                        <small class="text-muted d-block mt-1">Accepted formats: JPG, PNG, GIF, WEBP (Max: 5MB)</small>
                        @error('image')
                            <div class="invalid-feedback fw-bold">{{ $message }}</div>
                        @enderror

                        <!-- Live Preview Container -->
                        <div class="mt-3 d-none" id="imagePreviewContainer">
                            <p class="fw-bold text-muted small mb-2"><i class="fa-solid fa-image me-1 text-primary"></i>Image Preview</p>
                            <div class="position-relative d-inline-block">
                                <img id="imagePreview" src="#" alt="Preview" class="img-fluid rounded-3 border shadow-sm" style="max-height: 250px; object-fit: contain;">
                                <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2 rounded-circle shadow" id="removeImageBtn" style="width: 32px; height: 32px; padding: 0; line-height: 1; display:flex; align-items:center; justify-content:center;" title="Remove image">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('journals.index') }}" class="btn btn-light px-4">Cancel</a>
                        <button type="submit" class="btn btn-primary px-5 fw-bold shadow-sm">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const stars = document.querySelectorAll('.star');
        const ratingInput = document.getElementById('rating-input');

        stars.forEach(star => {
            star.addEventListener('click', function() {
                const value = this.getAttribute('data-value');
                ratingInput.value = value;

                stars.forEach(s => {
                    if (s.getAttribute('data-value') <= value) {
                        s.classList.remove('fa-regular');
                        s.classList.add('fa-solid');
                    } else {
                        s.classList.remove('fa-solid');
                        s.classList.add('fa-regular');
                    }
                });
            });
        });

        // Image Preview Logic
        const imageInput = document.getElementById('imageInput');
        const imagePreviewContainer = document.getElementById('imagePreviewContainer');
        const imagePreview = document.getElementById('imagePreview');
        const removeImageBtn = document.getElementById('removeImageBtn');

        if (imageInput) {
            imageInput.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    // Check file size (5MB limit)
                    if (file.size > 5242880) {
                        alert("The selected image is too large. Maximum size is 5MB.");
                        this.value = '';
                        imagePreviewContainer.classList.add('d-none');
                        return;
                    }
                    
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imagePreview.src = e.target.result;
                        imagePreviewContainer.classList.remove('d-none');
                    }
                    reader.readAsDataURL(file);
                } else {
                    imagePreviewContainer.classList.add('d-none');
                }
            });

            removeImageBtn.addEventListener('click', function() {
                imageInput.value = '';
                imagePreviewContainer.classList.add('d-none');
                imagePreview.src = '#';
            });
        }
    });
</script>
