<div class="container-fluid px-3 px-md-5 p-2">
    <div class="row justify-content-center mt-1">
        <div class="col-12">
    <!-- Top Search Bar Concept -->
    <div class="row justify-content-center mb-3">
        <div class="col-12">
            <form action="{{ route('journals.index') }}" method="GET" class="bg-white rounded-3 shadow-sm p-2 d-flex align-items-center gap-2 border">
                <input type="text" name="search" class="form-control border-0" placeholder="{{ __('Search entries by title or content...') }}" value="{{ request('search') }}">
                
                <div class="d-flex align-items-center gap-2 border-start ps-3" >
                    <i class="fa-regular fa-calendar text-muted"></i>
                    <input type="text" id="date_range_picker" class="form-control border-0 p-1 bg-transparent" placeholder="{{ __('Select Period...') }}" style="font-size: 0.85rem;" readonly>
                    <input type="hidden" name="start_date" id="start_date" value="{{ request('start_date') }}">
                    <input type="hidden" name="end_date" id="end_date" value="{{ request('end_date') }}">
                </div>
                
                <button type="submit" class="btn btn-light border ms-1 px-2 px-md-3"><i class="fa-solid fa-magnifying-glass text-muted"></i></button>
                @if(request('search') || request('start_date') || request('end_date'))
                    <a href="{{ route('journals.index') }}" class="btn btn-light border text-secondary  px-2 px-md-3" title="Clear Search">
                        <span class="d-none d-md-inline">{{ __('Clear') }}</span>
                        <i class="fa-solid fa-xmark d-md-none"></i>
                    </a>
                @endif
            </form>
        </div>
    </div>

    <div class="row justify-content-center">
        <!-- Left: Entries List -->
        <div class="col-md-6">
            <div class="mb-3">
                <h5 class="fw-bold">
                    @if(request('search') || request('start_date') || request('end_date'))
                        {{ __('Search Results (') }}{{ $journals->total() }})
                    @else
                        {{ __('All Entries (') }}{{ $journals->total() }})
                    @endif
                </h5>
            </div>

            <div class="journal-list pe-2" style="max-height: 70vh; overflow-y: auto;">
                @forelse($journals as $journal)
                    <div class="card border-0 shadow-sm rounded-4 mb-2 journal-card  cursor-pointer {{ request('id') == $journal->id ? 'bg-primary bg-opacity-10 shadow-md' : '' }}" 
                        onclick="window.location.href='{{ route('journals.index', ['id' => $journal->id, 'search' => request('search'), 'start_date' => request('start_date'), 'end_date' => request('end_date')]) }}'">
                        <div class="card-body p-3 py-0 ">
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
                                            @if($journal->image)
                                                <i class="fa-regular fa-image text-primary ms-1"></i>
                                            @endif
                                            {{ $journal->title }}
                                        </h6>
                                        <div class="text-warning small text-nowrap">
                                            @for($i=1; $i<=5; $i++)
                                                <i class="{{ $i <= $journal->rating ? 'fa-solid' : 'fa-regular' }} fa-star"></i>
                                            @endfor
                                        </div>
                                    </div>
                                    <p class="text-muted small mb-0 {{ request('id') == $journal->id ? 'd-none d-md-block text-truncate-2' : 'text-truncate-2' }}" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                        {{ Str::limit(strip_tags($journal->content), 100) }}
                                    </p>
                                </div>
                            </div>
                            
                            <!-- Mobile Expanded View -->
                            @if(request('id') == $journal->id)
                            <div class="d-md-none mt-2 pt-2 border-top border-primary border-opacity-25 expanded-mobile-content">                                                  
                                @if($journal->image)
                                    <div class="mb-3 rounded-3 overflow-hidden text-center bg-light">
                                        <img src="{{ Str::startsWith($journal->image, 'data:image') ? $journal->image : Storage::url($journal->image) }}" class="img-fluid" style="max-height: 150px; object-fit: contain;">
                                    </div>
                                @endif
                                <div class="journal-content text-dark mb-3" style="white-space: pre-wrap; line-height: 1.6; word-break: break-word;">{{ $journal->content }}</div>
                                
                                <div class="d-flex justify-content-end gap-2" onclick="event.stopPropagation()">
                                    <a href="{{ route('journals.index', ['view' => 'edit', 'id' => $journal->id]) }}" class="btn btn-sm btn-light border border-secondary text-secondary">
                                        <i class="fa-solid fa-pen-to-square"></i> {{ __('Edit') }}
                                    </a>
                                    <button class="btn btn-sm btn-light border border-danger text-danger" data-bs-toggle="modal" data-bs-target="#deleteJournalModal{{ $journal->id }}">
                                        <i class="fa-solid fa-trash-can"></i> {{ __('Delete') }}
                                    </button>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-muted text-center py-5">
                        <i class="fa-solid fa-book-open fs-1 mb-3 text-opacity-50"></i>
                        @if(request('search') || request('start_date') || request('end_date'))
                            <p>{{ __('No journal entries found matching your search.') }}</p>
                        @else
                            <p>{!! __('No journal entries found in the last week.<br>Write your reflection!') !!}</p>
                        @endif
                    </div>
                @endforelse
                
                <div class="mt-3 mx-1">{{-- Pagination --}}
                    {{ $journals->links('pagination::simple-bootstrap-5') }}
                </div>
            </div>
        </div>

        <!-- Right: Reader Pane -->
        <div class="col-md-6 mt-4 d-none d-md-inline">
            <div class="bg-white rounded-4 shadow-sm p-4" style="min-height: 600px;">
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
                            <button class="btn btn-sm border-0" type="button"  data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa-solid fa-ellipsis-vertical"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end p-0">
                                <li>
                                    <a class="dropdown-item btn btn-light text-secondary" href="{{ route('journals.index', ['view' => 'edit', 'id' => $selectedJournal->id]) }}">
                                        <i class="fa-solid fa-pen-to-square me-2"></i>{{ __('Edit') }}
                                    </a>
                                </li>
                                <li>
                                    <button class="dropdown-item btn btn-light text-danger" data-bs-toggle="modal" data-bs-target="#deleteJournalModal{{ $selectedJournal->id }}">
                                        <i class="fa-solid fa-trash-can me-2"></i>{{ __('Delete') }}
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>

                    @if($selectedJournal->image)
                        <div class="mb-4 rounded-3 overflow-hidden text-center bg-light">
                            <img src="{{ Str::startsWith($selectedJournal->image, 'data:image') ? $selectedJournal->image : Storage::url($selectedJournal->image) }}" class="img-fluid" style="max-height: 150px; object-fit: contain;">
                        </div>
                    @endif

                    <div class="journal-content text-dark" style="white-space: pre-wrap; line-height: 1.8; max-height: 70vh; overflow-y: auto; overflow-x: hidden; word-break: break-word;">{{ $selectedJournal->content }}</div>
                @else
                    <div class="d-flex flex-column align-items-center justify-content-center h-100 text-muted opacity-50">
                        <i class="fa-solid fa-book-open-reader fs-1 mb-3"></i>
                        <p class="mb-0">{{ __('Select an entry from the list to read.') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
        </div>
    </div>
</div>

@if(isset($selectedJournal))
    @include('journals.modals.delete-journal', ['journal' => $selectedJournal])
@endif

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    flatpickr("#date_range_picker", {
        mode: "range",
        dateFormat: "Y-m-d",
        defaultDate: [
            "{{ request('start_date') }}", 
            "{{ request('end_date') }}"
        ],
        onClose: function(selectedDates, dateStr, instance) {
            if (selectedDates.length === 2) {
                document.getElementById('start_date').value = instance.formatDate(selectedDates[0], "Y-m-d");
                document.getElementById('end_date').value = instance.formatDate(selectedDates[1], "Y-m-d");
            } else if (selectedDates.length === 0) {
                document.getElementById('start_date').value = '';
                document.getElementById('end_date').value = '';
            }
        }
    });
});
</script>
@endpush
