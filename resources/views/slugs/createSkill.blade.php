<button type="button" id="add-skill" data-bs-toggle="modal" data-bs-target="#create-skill" class="submit-btn border-0">Add Skill</button>
<form action="{{ route('skill-add') }}" method="POST">
    @csrf
    <div class="modal fade" id="create-skill" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Add Skill</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="floatingInput" name="title" value="{{old('title')}}" required placeholder="Skill Title">
                        <label for="floatingInput">Skill Title</label>
                        @error('title')
                        <span class="error-msg">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="modal-footer">
                        <button type="reset" class="deny-btn border-0">Clear</button>
                        <button type="submit" class="border-0 submit-btn">Add Skill</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>