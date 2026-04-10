@php
    $journal = $journals->firstWhere('id', request('id'));
@endphp

@if($journal)
<div class="container-fluid p-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="bg-white rounded-4 shadow-sm p-3 p-md-4 border">
                <h4 class="fw-bold my-2">{{ __('Edit Entry') }}</h4>
                
                <form action="{{ route('journals.update', $journal->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="row mb-2 mb-md-3">
                        <div class="col-md-3">
                            <label class="form-label fw-bold text-muted small mb-0">{{ __('Entry Date') }}</label>
                            <input type="date" class="form-control form-control-sm border-light shadow-sm @error('entry_date') is-invalid @enderror" name="entry_date" value="{{ old('entry_date', \Carbon\Carbon::parse($journal->entry_date)->format('Y-m-d')) }}" required>
                            @error('entry_date')
                                <div class="invalid-feedback fw-bold">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3 mt-2 mt-md-0">
                            <label class="form-label fw-bold text-muted small mb-0">{{ __('How was your day?') }}</label>
                            <div class="d-flex align-items-center gap-2 text-warning fs-3 rating-selector" id="star-rating">
                                <input type="hidden" name="rating" id="rating-input" value="{{ $journal->rating }}">
                                @for($i=1; $i<=5; $i++)
                                    <i class="{{ $i <= $journal->rating ? 'fa-solid' : 'fa-regular' }} fa-star cursor-pointer star" data-value="{{ $i }}"></i>
                                @endfor
                            </div>
                        </div>

                        <div class="col-md-12 mt-2">
                            <label class="form-label fw-bold text-muted small mb-0">{{ __('Title') }}</label>
                            <input type="text" class="form-control form-control-sm border-light shadow-sm" name="title" value="{{ old('title', $journal->title) }}" required>
                        </div>
                    </div>

                    <div class="mb-2 mb-md-3">
                        <label class="form-label fw-bold text-muted small mb-0">{{ __('Content') }}</label>
                        <textarea class="form-control border-light shadow-sm" name="content" rows="12" required style="resize: none;">{{ old('content', $journal->content) }}</textarea>
                    </div>

                    <div class="mb-5">
                        <label class="form-label fw-bold text-muted small mb-0">{{ __('Attach Image (Optional)') }}</label>
                        
                        <input class="form-control border-light shadow-sm @error('image') is-invalid @enderror" type="file" name="image" id="imageInputEdit" accept="image/jpeg, image/png, image/jpg, image/gif, image/webp">
                        <small class="text-muted d-block mt-1">{{ __('Accepted formats: JPG, PNG, GIF, WEBP (Max: 5MB). Uploading a new image will replace the current one.') }}</small>
                        @error('image')
                            <div class="invalid-feedback fw-bold">{{ $message }}</div>
                        @enderror

                        <input type="hidden" name="remove_image" id="removeImageInput" value="0">

                        <!-- Live Preview Container -->
                        <div class="mt-3 {{ $journal->image ? '' : 'd-none' }}" id="imagePreviewContainerEdit">
                            <p class="fw-bold text-muted small mb-2"><i class="fa-solid fa-image me-1 text-primary"></i><span id="previewLabelText">{{ $journal->image ? __('Current Image') : __('New Image Preview') }}</span></p>
                            <div class="position-relative d-inline-block">
                                <img id="imagePreviewEdit" src="{{ $journal->image ? (Str::startsWith($journal->image, 'data:image') ? $journal->image : Storage::url($journal->image)) : '#' }}" data-original-src="{{ $journal->image ? (Str::startsWith($journal->image, 'data:image') ? $journal->image : Storage::url($journal->image)) : '' }}" alt="Preview" class="img-fluid rounded-3 border shadow-sm" style="max-height: 250px; object-fit: contain;">
                                <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2 rounded-circle shadow" id="removeImageBtnEdit" style="width: 32px; height: 32px; padding: 0; line-height: 1; display:flex; align-items:center; justify-content:center;" title="Remove image">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('journals.index', ['id' => $journal->id]) }}" class="btn btn-light px-4">{{ __('Cancel') }}</a>
                        <button type="submit" class="btn btn-primary px-5 fw-bold shadow-sm">{{ __('Update') }}</button>
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
        const removeImageInput = document.getElementById('removeImageInput');
        const previewLabelText = document.getElementById('previewLabelText');
        
        const originalSrc = imagePreviewEdit ? imagePreviewEdit.getAttribute('data-original-src') : '';

        if (imageInputEdit) {
            imageInputEdit.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    if (file.size > 5242880) { // 5MB limit
                        alert("The selected image is too large. Maximum size is 5MB.");
                        this.value = '';
                        restoreOriginalPreview();
                        return;
                    }
                    
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imagePreviewEdit.src = e.target.result;
                        imagePreviewContainerEdit.classList.remove('d-none');
                        if(previewLabelText) previewLabelText.innerText = 'New Image Preview';
                        if (removeImageInput) removeImageInput.value = "0";
                    }
                    reader.readAsDataURL(file);
                } else {
                    restoreOriginalPreview();
                }
            });

            removeImageBtnEdit.addEventListener('click', function() {
                if (imageInputEdit.value !== '') {
                    // They selected a new image, so cancel that and restore original
                    imageInputEdit.value = '';
                    restoreOriginalPreview();
                } else {
                    // No new image selected, so they are removing the original image
                    imagePreviewContainerEdit.classList.add('d-none');
                    if (removeImageInput) removeImageInput.value = "1";
                }
            });

            function restoreOriginalPreview() {
                if (originalSrc && (!removeImageInput || removeImageInput.value == "0")) {
                    imagePreviewEdit.src = originalSrc;
                    imagePreviewContainerEdit.classList.remove('d-none');
                    if(previewLabelText) previewLabelText.innerText = 'Current Image';
                } else {
                    imagePreviewContainerEdit.classList.add('d-none');
                    imagePreviewEdit.src = '#';
                }
            }
        }
    });
</script>
@else
<div class="alert alert-danger">{{ __('Journal entry not found.') }}</div>
@endif
