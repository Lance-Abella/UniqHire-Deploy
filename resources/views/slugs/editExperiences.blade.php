<div class="mb-3 d-flex justify-content-between exp-header">
    <h4 class="">Work Experience&nbsp;&nbsp;</h4>
    <div class="d-flex">
        <div>
            <button type="button" id="exp-add-btn" data-bs-toggle="modal" data-bs-target="#exampleModal" class="border-0 match-info" style="display: none;"><i class='bx bx-plus'></i></button>
        </div>
        <div>
            <button class="border-0" type="button" id="exp-edit-btn" onclick="toggleEditExp()"><i class='bx bx-edit-alt sub-text'></i></button>
        </div>
    </div>
</div>
<div>
    <form action="{{route('add-experience')}}" method="POST">
        @csrf
        <div class="modal fade" id="exampleModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="staticBackdropLabel">Add Work Experience</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" value="{{ $user->id }}">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="floatingInput" placeholder="Title" name="title">
                            <label for="floatingInput">Title</label>
                            @error('title')
                            <span class="error-msg">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-floating mb-3">
                            <input type="date" class="form-control date" name="date" required placeholder=" Choose Date">
                            <label for="floatingInput">Date</label>
                            @error('date')
                            <span class="error-msg">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="modal-footer">
                            <button type="reset" class="deny-btn border-0">Clear</button>
                            <button type="submit" class="border-0 submit-btn">Save Changes</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <ul class="experiences">
        @forelse($experiences as $experience)
        <li class="mb-1">
            <div class="d-flex">
                <div class="exp-container">
                    <p class="exp-title">{{ $experience->title }}</p>
                    <p class="exp-date">{{ \Carbon\Carbon::parse($experience->date)->format('M d, Y') }}</p>
                </div>
                <form action="{{ route('delete-experience', $experience->id) }}" method="POST" class="d-flex justify-content-end">
                    @csrf
                    @method('DELETE')
                    <button class="border-0 match-info exp-delete-btn delete-btn" style="display: none;"><i class='bx bx-x'></i></button>
                </form>
            </div>
        </li>
        @empty
        <div class="about sub-text">No experiences. Add one.</div>
        @endforelse
    </ul>
</div>

<script>
    function toggleEditExp() {
        var editBtn = document.getElementById('exp-edit-btn');
        var addBtn = document.getElementById('exp-add-btn');
        var deleteBtns = document.querySelectorAll('.exp-delete-btn');

        if (addBtn.style.display === 'none' || addBtn.style.display === '') {
            addBtn.style.display = 'inline-block';
        } else {
            addBtn.style.display = 'none';
        }

        deleteBtns.forEach(btn => {
            if (btn.style.display === 'none' || btn.style.display === '') {
                btn.style.display = 'inline-block';
            } else {
                btn.style.display = 'none';
            }
        });
    }
</script>