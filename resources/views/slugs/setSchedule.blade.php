<form  action="{{ route('set-schedule') }}" method="POST">
    @csrf
    <div>
        <button type="button" class="submit-btn modal-btn border-0" data-bs-toggle="modal" data-bs-target="#donateModal">Set Schedule</button>
        <div class="modal fade" id="donateModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">

                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="staticBackdropLabel">Set Schedule</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-floating mb-3">
                           <input type="date" class="" name="schedule" required placeholder="Choose Date">
                            <label for="floatingInput">Schedule Interview</label>
                            @error('schedule')
                            <span class="error-msg">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="form-floating mb-3">
                                    <input type="time" class="form-control" id="startTime" name="start_time" value="{{old('start_time')}}" required placeholder="Input Start Time">
                                    <label for="floatingInput">Time Start</label>
                                            @error('start_time')
                                    <span class="error-msg">{{ $message }}</span>
                                            @enderror
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-floating mb-3">
                                    <input type="time" class="form-control" id="endTime" name="end_time" value="{{old('end_time')}}" required placeholder="Input Start Time">
                                    <label for="floatingInput">Time End</label>
                                                @error('end_time')
                                    <span class="error-msg">{{ $message }}</span>
                                                @enderror
                                </div>
                            </div>
                        </div>  
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="border-0 submit-btn">Enter</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>


<!-- <div id="donate-button-container">
    <div id="donate-button"></div>
    <script src="https://www.paypalobjects.com/donate/sdk/donate-sdk.js" charset="UTF-8"></script>
    <script>
        PayPal.Donation.Button({
            env: 'sandbox',
            hosted_button_id: 'GMSJGCL9VRSZL',
            image: {
                src: 'https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif',
                alt: 'Donate with PayPal button',
                title: 'PayPal - The safer, easier way to pay online!',
            }
        }).render('#donate-button');
    </script>
</div> -->

<!-- <script src="https://www.paypal.com/sdk/js?client-id=BAA4NQ6v6VEbHbGBsmVdeIjPHrzoaCFsYF3NEvF3bCo8iExmlRJ8HE2HN9rLV2DFA-FxnLYN-3EcgXhTaU&components=hosted-buttons&disable-funding=venmo&currency=PHP"></script>
<div id="paypal-container-ALJE4JSRH2YJW"></div>
<script>
    paypal.HostedButtons({
        hostedButtonId: "ALJE4JSRH2YJW",
    }).render("#paypal-container-ALJE4JSRH2YJW")
</script> -->

<!-- <form action="{{ route('set-schedule') }}" method="POST">
    @csrf
<button type="button" class="submit-btn modal-btn border-0" data-bs-toggle="modal" data-bs-target="#{{$employee->id}}">Set Schedule</button>
    <div class="modal fade" id="{{$employee->id}}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Set Schedule</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">    
                    <div class="form-floating mb-3">
                        <input type="date" class="form-control date" name="schedule" required placeholder="Choose Date">
                        <label for="floatingInput">Schedule Interview</label>
                    </div>                 
                    <div class="row">
                        <div class="col">
                                <div class="form-floating mb-3">
                                    <input type="time" class="form-control" id="startTime" name="start_time" value="{{old('start_time')}}" required placeholder="Input Start Time">
                                    <label for="floatingInput">Time Start</label>
                                            @error('start_time')
                                    <span class="error-msg">{{ $message }}</span>
                                            @enderror
                                </div>
                        </div>
                        <div class="col">
                            <div class="form-floating mb-3">
                                <input type="time" class="form-control" id="endTime" name="end_time" value="{{old('end_time')}}" required placeholder="Input Start Time">
                                <label for="floatingInput">Time End</label>
                                            @error('end_time')
                                <span class="error-msg">{{ $message }}</span>
                                            @enderror
                            </div>
                        </div>
                    </div>            
                    <div class="d-flex justify-content-evenly mt-3 prog-btn">
                        <button type="submit" class="submit-btn border-0">Add</button>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</form> -->
