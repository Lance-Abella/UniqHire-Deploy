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

<script src="https://www.paypal.com/sdk/js?client-id=BAA4NQ6v6VEbHbGBsmVdeIjPHrzoaCFsYF3NEvF3bCo8iExmlRJ8HE2HN9rLV2DFA-FxnLYN-3EcgXhTaU&components=hosted-buttons&disable-funding=venmo&currency=PHP"></script>
<div id="paypal-container-ALJE4JSRH2YJW"></div>
<script>
    paypal.HostedButtons({
        hostedButtonId: "ALJE4JSRH2YJW",
    }).render("#paypal-container-ALJE4JSRH2YJW")
</script>