<form id="donate-form-{{ $program->id }}" action="{{ route('payment') }}" method="POST">
    @csrf
    <div>
        <button type="button" class="submit-btn border-0" data-bs-toggle="modal" data-bs-target="#donateModal">Donate with Paypal</button>
        <div class="modal fade" id="donateModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">

                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="staticBackdropLabel">Donation Form (PayPal)</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="user_id" value="{{ Auth::user()->id }}">
                        <input type="hidden" name="training_program_id" value="{{ $program->id }}">
                        <input type="hidden" name="crowdfund_id" value="{{ $program->crowdfund->id }}">
                        <div class="row d-flex justify-content-center mb-3">
                            <img src="{{asset('images/paypal-logo.png')}}" alt="" style="width:20rem">
                        </div>
                        <div class=" row">
                            <div class="col">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="name" name="name" required placeholder="Name">
                                    <label for="name">Name (Optional, you may leave it blank)</label>
                                    @error('name')
                                    <span class="error-msg">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-floating mb-3">
                                    <input type="email" class="form-control" id="email" name="email" placeholder="Email Address">
                                    <label for="email">Email Address</label>
                                    @error('email')
                                    <span class="error-msg">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="amount-needed" name="amount" required placeholder="Amount to Donate" oninput="formatNumber(this)">
                            <label for="floatingInput">Amount to Donate</label>
                            @error('amount')
                            <span class="error-msg">{{ $message }}</span>
                            @enderror
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="border-0 submit-btn" onclick="confirm(event, 'donate-form-{{ $program->id }}')">Enter</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
<script>
    function formatNumber(input) {
        let value = input.value.replace(/,/g, '');
        if (!isNaN(value) && value !== '') {
            input.value = Number(value).toLocaleString();
        }
    }

    function confirm(event, formId) {
        event.preventDefault();
        Swal.fire({
            title: "Confirmation",
            text: "Do you really want to donate in this crowdfund?",
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Confirm"
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(formId).submit();
            }
        });
    }
</script>

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