<div class="container-fluid p-2">
    <!-- Top Search Bar Concept -->
    <div class="row justify-content-center mt-2 mb-3">
        <div class="col-md-10">
            <form action="{{ route('journals.index') }}" method="GET" class="bg-white rounded-3 shadow-sm p-2 d-flex align-items-center gap-2 border">
                <input type="text" name="search" class="form-control border-0" placeholder="Search entries by title or content..." value="{{ request('search') }}">
                
                <input type="date" name="search_date" class="form-control border-0 " style="max-width: 150px;" value="{{ request('search_date') }}">
                
                <button type="submit" class="btn btn-light border"><i class="fa-solid fa-magnifying-glass text-muted"></i></button>
                @if(request('search') || request('search_date'))
                    <a href="{{ route('journals.index') }}" class="btn btn-light border">Clear</a>
                @endif
            </form>
        </div>
    </div>

    <div class="row justify-content-center">
        <!-- Left: Entries List -->
        <div class="col-md-5">
            <div class="mb-3">
                <h5 class="fw-bold">
                    @if(request('search') || request('search_date'))
                        Search Results ({{ $journals->count() }})
                    @else
                        Recent Entries ({{ $journals->count() }})
                    @endif
                </h5>
            </div>

            <div class="journal-list pe-2" style="max-height: 70vh; overflow-y: auto;">
                @forelse($journals as $journal)
                    <div class="card border-0 shadow-sm rounded-4 mb-3 journal-card cursor-pointer {{ request('id') == $journal->id ? 'bg-primary bg-opacity-10 shadow-md' : '' }}" 
                         onclick="window.location.href='{{ route('journals.index', ['id' => $journal->id, 'search' => request('search'), 'search_date' => request('search_date')]) }}'">
                        <div class="card-body p-3">
                            <div class="row g-0">
                                <!-- Date Column -->
                                <div class="col-2 text-center border-end pe-2">
                                    <div class="small fw-bold text-muted">{{ \Carbon\Carbon::parse($journal->entry_date)->format('M') }}</div>
                                    <div class="fs-4 fw-bold lh-1">{{ \Carbon\Carbon::parse($journal->entry_date)->format('d') }}</div>
                                    <div class="small text-muted" style="font-size: 0.7rem;">{{ \Carbon\Carbon::parse($journal->entry_date)->format('Y') }}</div>
                                </div>
                                <!-- Content Column -->
                                <div class="col-10 ps-3">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <h6 class="fw-bold mb-0 text-truncate pe-2">
                                            {{ $journal->title }} 
                                            @if($journal->image)
                                                <i class="fa-regular fa-image text-primary ms-1"></i>
                                            @endif
                                        </h6>
                                        <div class="text-warning small text-nowrap">
                                            @for($i=1; $i<=5; $i++)
                                                <i class="{{ $i <= $journal->rating ? 'fa-solid' : 'fa-regular' }} fa-star"></i>
                                            @endfor
                                        </div>
                                    </div>
                                    <p class="text-muted small mb-0 text-truncate-2" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                        {{ Str::limit(strip_tags($journal->content), 100) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-muted text-center py-5">
                        <i class="fa-solid fa-book-open fs-1 mb-3 text-opacity-50"></i>
                        @if(request('search') || request('search_date'))
                            <p>No journal entries found matching your search.</p>
                        @else
                            <p>No journal entries found in the last week.<br>Write your reflection!</p>
                        @endif
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Right: Reader Pane -->
        <div class="col-md-5">
            <div class="bg-white rounded-4 shadow-sm p-4 h-100" style="min-height: 500px;">
                @if(isset($selectedJournal))
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div class="d-flex gx-3 align-items-center">
                            <div class="text-center me-3 border-end pe-3">
                                <div class="small fw-bold text-muted">{{ \Carbon\Carbon::parse($selectedJournal->entry_date)->format('M') }}</div>
                                <div class="fs-3 fw-bold lh-1">{{ \Carbon\Carbon::parse($selectedJournal->entry_date)->format('d') }}</div>
                                <div class="small text-muted">{{ \Carbon\Carbon::parse($selectedJournal->entry_date)->format('Y') }}</div>
                            </div>
                            <div>
                                <h3 class="fw-bold mb-1">{{ $selectedJournal->title }}</h3>
                                <div class="text-warning small">
                                    @for($i=1; $i<=5; $i++)
                                        <i class="{{ $i <= $selectedJournal->rating ? 'fa-solid' : 'fa-regular' }} fa-star"></i>
                                    @endfor
                                </div>
                            </div>
                        </div>

                        <!-- Actions Dropdown -->
                        <div class="dropdown">
                            <button class="btn btn-light btn-sm rounded-circle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa-solid fa-ellipsis-vertical"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end p-0">
                                <li>
                                    <a class="dropdown-item text-primary" href="{{ route('journals.index', ['view' => 'edit', 'id' => $selectedJournal->id]) }}">
                                        <i class="fa-solid fa-pen-to-square me-2"></i>Edit
                                    </a>
                                </li>
                                <li>
                                    <form action="{{ route('journals.destroy', $selectedJournal->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button class="dropdown-item text-danger" onclick="return confirm('Are you sure you want to delete this entry?');">
                                            <i class="fa-solid fa-trash-can me-2"></i>Delete
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>

                    @if($selectedJournal->image)
                        <div class="mb-4 rounded-3 overflow-hidden text-center bg-light">
                            <img src="{{ Storage::url($selectedJournal->image) }}" class="img-fluid" style="max-height: 150px; object-fit: contain;">
                        </div>
                    @endif

                    <div class="journal-content text-dark" style="white-space: pre-wrap; line-height: 1.8;">{{ $selectedJournal->content }}</div>
                @else
                    <div class="d-flex flex-column align-items-center justify-content-center h-100 text-muted opacity-50">
                        <i class="fa-solid fa-book-open-reader fs-1 mb-3"></i>
                        <p class="mb-0">Select an entry from the list to read.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
