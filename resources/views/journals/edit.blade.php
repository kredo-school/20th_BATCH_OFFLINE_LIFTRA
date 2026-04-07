@php
    $journal = $journals->firstWhere('id', request('id'));
@endphp

@if($journal)
<div class="container-fluid p-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="bg-white rounded-4 shadow-sm p-3 p-md-4 border">
                <h4 class="fw-bold my-2">Edit Entry</h4>
                
                <form action="{{ route('journals.update', $journal->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="row mb-2 mb-md-3">
                        <div class="col-md-3">
                            <label class="form-label fw-bold text-muted small mb-0">Entry Date</label>
                            <input type="date" class="form-control form-control-sm border-light shadow-sm @error('entry_date') is-invalid @enderror" name="entry_date" value="{{ old('entry_date', \Carbon\Carbon::parse($journal->entry_date)->format('Y-m-d')) }}" required>
                            @error('entry_date')
                                <div class="invalid-feedback fw-bold">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3 mt-2 mt-md-0">
                            <label class="form-label fw-bold text-muted small mb-0">How was your day?</label>
                            <div class="d-flex align-items-center gap-2 text-warning fs-3 rating-selector" id="star-rating">
                                <input type="hidden" name="rating" id="rating-input" value="{{ $journal->rating }}">
                                @for($i=1; $i<=5; $i++)
                                    <i class="{{ $i <= $journal->rating ? 'fa-solid' : 'fa-regular' }} fa-star cursor-pointer star" data-value="{{ $i }}"></i>
                                @endfor
                            </div>
                        </div>

                        <div class="col-md-12 mt-2">
                            <label class="form-label fw-bold text-muted small mb-0">Title</label>
                            <input type="text" class="form-control form-control-sm border-light shadow-sm" name="title" value="{{ old('title', $journal->title) }}" required>
                        </div>
                    </div>

                    <div class="mb-2 mb-md-3">
                        <label class="form-label fw-bold text-muted small mb-0">Content</label>
                        <textarea class="form-control border-light shadow-sm" name="content" rows="12" required style="resize: none;">{{ old('content', $journal->content) }}</textarea>
                    </div>

                    <div class="mb-5">
                        <label class="form-label fw-bold text-muted small mb-0">Attach Image (Optional)</label>
                        @if($journal->image)
                            <div class="mb-2" id="currentImageInfo">
                                <span class="badge bg-secondary mb-0">Current image attached</span>
                            </div>
                        @endif
                        <input class="form-control border-light shadow-sm @error('image') is-invalid @enderror" type="file" name="image" id="imageInputEdit" accept="image/jpeg, image/png, image/jpg, image/gif, image/webp">
                        <small class="text-muted d-block mt-1">Accepted formats: JPG, PNG, GIF, WEBP (Max: 5MB). Uploading a new image will replace the current one.</small>
                        @error('image')
                            <div class="invalid-feedback fw-bold">{{ $message }}</div>
                        @enderror

                        <!-- Live Preview Container -->
                        <div class="mt-3 d-none" id="imagePreviewContainerEdit">
                            <p class="fw-bold text-muted small mb-2"><i class="fa-solid fa-image me-1 text-primary"></i>New Image Preview</p>
                            <div class="position-relative d-inline-block">
                                <img id="imagePreviewEdit" src="#" alt="Preview" class="img-fluid rounded-3 border shadow-sm" style="max-height: 250px; object-fit: contain;">
                                <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2 rounded-circle shadow" id="removeImageBtnEdit" style="width: 32px; height: 32px; padding: 0; line-height: 1; display:flex; align-items:center; justify-content:center;" title="Remove new image">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('journals.index', ['id' => $journal->id]) }}" class="btn btn-light px-4">Cancel</a>
                        <button type="submit" class="btn btn-primary px-5 fw-bold shadow-sm">Update</button>
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
        const imageInputEdit = document.getElementById('imageInputEdit');
        const imagePreviewContainerEdit = document.getElementById('imagePreviewContainerEdit');
        const imagePreviewEdit = document.getElementById('imagePreviewEdit');
        const removeImageBtnEdit = document.getElementById('removeImageBtnEdit');
        const currentImageInfo = document.getElementById('currentImageInfo');

        if (imageInputEdit) {
            imageInputEdit.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    if (file.size > 5242880) { // 5MB limit
                        alert("The selected image is too large. Maximum size is 5MB.");
                        this.value = '';
                        imagePreviewContainerEdit.classList.add('d-none');
                        if (currentImageInfo) currentImageInfo.style.display = 'block';
                        return;
                    }
                    
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imagePreviewEdit.src = e.target.result;
                        imagePreviewContainerEdit.classList.remove('d-none');
                        if (currentImageInfo) currentImageInfo.style.display = 'none';
                    }
                    reader.readAsDataURL(file);
                } else {
                    imagePreviewContainerEdit.classList.add('d-none');
                    if (currentImageInfo) currentImageInfo.style.display = 'block';
                }
            });

            removeImageBtnEdit.addEventListener('click', function() {
                imageInputEdit.value = '';
                imagePreviewContainerEdit.classList.add('d-none');
                imagePreviewEdit.src = '#';
                if (currentImageInfo) currentImageInfo.style.display = 'block';
            });
        }
    });
</script>
@else
<div class="alert alert-danger">Journal entry not found.</div>
@endif
