<!-- Habit Delete Modal -->
<div class="modal fade" id="deleteHabitModal{{ $habit->id }}" tabindex="-1">

  <div class="modal-dialog modal-dialog-centered">

    <form action="{{ route('habits.destroy', $habit->id) }}" method="POST">

      @csrf
      @method('DELETE')

      <div class="modal-content p-3">

        <div class="modal-body text-center">

          <h5 class="fw-bold mb-3">Delete Habit</h5>

          <p>Are you sure you want to delete this habit?</p>

        </div>

        <div class="text-end">

          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>

          <button type="submit" class="btn btn-danger">Delete</button>

        </div>

      </div>

    </form>

  </div>

</div>