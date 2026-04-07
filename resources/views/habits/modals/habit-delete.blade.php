<!-- Habit Delete Modal -->
<div class="modal fade" id="deleteHabitModal{{ $habit->id }}" tabindex="-1">

  <div class="modal-dialog modal-dialog-centered">

    <form action="{{ route('habits.destroy', $habit->id) }}" method="POST">

      @csrf
      @method('DELETE')

      <div class="modal-content p-3 border-0 shadow-lg rounded-4">

        <div class="modal-body text-center">

          <h5 class="modal-title mb-4 fw-bold text-dark">Delete Habit</h5>

          <p class="text-muted mb-0">Are you sure you want to delete this habit?</p>

        </div>

        <div class="text-end px-3 pb-3">

          <button type="button" class="btn btn-light rounded-pill px-4 fw-semibold text-muted me-2" data-bs-dismiss="modal">Cancel</button>

          <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold shadow-sm">Delete</button>

        </div>

      </div>

    </form>

  </div>

</div>