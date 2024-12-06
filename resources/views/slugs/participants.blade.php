<div>
    <button type="button" class="submit-btn modal-btn sub-text border-0" data-bs-toggle="modal" data-bs-target="#participantsModal-{{ $event->id }}" title="Show number of participants"> Participants&nbsp;(<span> {{$participantCounts[$event->id] ?? 0}} </span>) </button>
    <div class="modal fade" id="participantsModal-{{ $event->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">

            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Participants</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @forelse ($event->users as $participant)
                    <div class="user-card d-flex justify-content-between align-items-center py-3 px-3">
                        <div class="name">
                            {{ $participant->userInfo->name }}
                        </div>
                    </div>
                    @empty
                    <div class="user-card text-center py-3 px-3">
                        No participants yet.
                    </div>
                    @endforelse

                </div>
            </div>
        </div>
    </div>
</div>