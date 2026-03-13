<div class="container-fluid p-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="bg-white rounded-4 shadow-sm p-4 p-md-5 border">
                <h4 class="fw-bold mb-4">Write New Entry</h4>

                <form action="{{ route('journals.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label class="form-label fw-bold text-muted small">Entry Date</label>
                            <input type="date"
                                class="form-control form-control-md border-light shadow-sm @error('entry_date') is-invalid @enderror"
                                name="entry_date" value="{{ old('entry_date', date('Y-m-d')) }}" required>
                            @error('entry_date')
                                <div class="invalid-feedback fw-bold">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold text-muted small">How was your day?</label>
                            <div class="d-flex my-auto align-items-center gap-2 text-warning fs-3 rating-selector" id="star-rating">
                                <input type="hidden" name="rating" id="rating-input" value="3">
                                <i class="fa-solid fa-star cursor-pointer star" data-value="1"></i>
                                <i class="fa-solid fa-star cursor-pointer star" data-value="2"></i>
                                <i class="fa-solid fa-star cursor-pointer star" data-value="3"></i>
                                <i class="fa-regular fa-star cursor-pointer star" data-value="4"></i>
                                <i class="fa-regular fa-star cursor-pointer star" data-value="5"></i>
                            </div>
                        </div>
                        <div class="col-md-12 mt-2">
                            <label class="form-label fw-bold text-muted small">Title</label>
                            <input type="text" class="form-control form-control-lg border-light shadow-sm"
                                name="title" placeholder="A great breakthrough..." required>
                        </div>
                    </div>

                    

                    <div class="mb-4">
                        <label class="form-label fw-bold text-muted small">Content</label>
                        <textarea class="form-control border-light shadow-sm" name="content" rows="12"
                            placeholder="Start writing your thoughts..." required style="resize: none;"></textarea>
                    </div>

                    <div class="mb-5">
                        <label class="form-label fw-bold text-muted small">Attach Image (Optional)</label>
                        <input class="form-control border-light shadow-sm" type="file" name="image"
                            accept="image/*">
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('journals.index') }}" class="btn btn-light px-4">Cancel</a>
                        <button type="submit" class="btn btn-primary px-5 fw-bold shadow-sm">Save Journal</button>
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
    });
</script>
