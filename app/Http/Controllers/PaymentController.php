<?php

namespace App\Http\Controllers;

use App\Notifications\SponsorDonationNotification;
use Illuminate\Http\Request;
use Srmklive\PayPal\Facades\PayPal;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use App\Models\Transaction;
use App\Models\CrowdfundEvent;
use Illuminate\Support\Facades\Log;


class PaymentController extends Controller
{
    private function convertToNumber($number)
    {
        return (float) str_replace(',', '', $number);
    }

    public function payment(Request $request)
    {
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));

        // Get the access token response, which is an array
        $response = $provider->getAccessToken();

        // Make sure the response is an array and contains the access token
        if (isset($response['access_token'])) {
            $token = $response['access_token'];  // Access token as a string
        } else {
            // Log the error response and return back with an error message
            Log::error('PayPal access token request failed', ['response' => $response]);
            return back()->with('error', 'Unable to retrieve PayPal access token.');
        }

        // Pass the access token to the provider
        $provider->setAccessToken($response); // Pass the whole response array
        $crowdfundEvent = CrowdfundEvent::findOrFail($request->crowdfund_id);
        $payeeEmail = $crowdfundEvent->program->agency->userInfo->paypal_account; // The dynamic PayPal email of the agency


        // Now proceed with creating the order
        $response = $provider->createOrder([
            "intent" => "CAPTURE",
            "purchase_units" => [
                [
                    "amount" => [
                        "currency_code" => "USD",
                        "value" => $this->convertToNumber($request->amount),
                    ],
                    "payee" => [
                        "email_address" => $payeeEmail,  // Dynamically set the payee (receiver)
                    ],
                ],
            ],
            "application_context" => [
                "cancel_url" => route('payment-cancel', [
                    'training_program_id' => $request->training_program_id,
                ]),
                "return_url" => route('payment-success', [
                    'crowdfund_id' => $request->crowdfund_id,
                    'user_id' => $request->user_id,
                    'email' => $request->email,
                    'name' => $request->name,
                    'training_program_id' => $request->training_program_id
                ]),
            ],
        ]);

        // Log the response for debugging purposes
        Log::info('PayPal Create Order Response', $response);

        if (isset($response['id'])) {
            return redirect()->away($response['links'][1]['href']); // Redirect to PayPal
        }

        return back()->with('error', 'Error processing payment.');
    }

    public function success(Request $request)
    {
        $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'required|email',
        ]);

        $programId = $request->query('training_program_id');
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $token = $provider->getAccessToken();

        $provider->setAccessToken($token);
        $response = $provider->capturePaymentOrder($request->query('token'));

        // Log the purchase_units response to inspect the structure
        // Log::info('Purchase Units Response', ['purchase_units' => $response['purchase_units']]);
        Log::info('PayPal Capture Payment Response', ['response' => $response]);

        $paymentDetails = $response['purchase_units'][0]['payments']['captures'][0] ?? null;
        $crowdfundEvent = CrowdfundEvent::findOrFail($request->query('crowdfund_id'));
        $payeeEmail = $crowdfundEvent->program->agency->userInfo->paypal_account;

        if ($paymentDetails && isset($paymentDetails['amount']['value'])) {
            $amount = $paymentDetails['amount']['value'];

            // Save transaction
            $transaction = Transaction::create([
                'name' => $request->name ?? 'Anonymous',
                'email' => $request->email,
                'crowdfund_id' => $request->query('crowdfund_id'),
                'sponsor_id' => $request->query('user_id'),
                'amount' => $this->convertToNumber($amount),
                'status' => 'Completed',
                'transaction_id' => $response['id'],
                'receiver' => $payeeEmail
            ]);

            // Update raised amount
            $crowdfundEvent = CrowdfundEvent::findOrFail($request->query('crowdfund_id'));
            $crowdfundEvent->raised_amount += $transaction->amount;
            $crowdfundEvent->save();

            $agencyUser = $crowdfundEvent->program->agency->userInfo;

            if ($agencyUser) {
                $agencyUser->user->notify(new SponsorDonationNotification($transaction));
            }



            return redirect()->route('trainingprog-details', $programId)->with('success', 'Payment successful!');
        }

        return redirect()->route('trainingprog-details', $programId)->with('error', 'Payment verification failed.');
    }


    public function cancel(Request $request)
    {
        $programId = $request->query('training_program_id');
        return redirect()->route('trainingprog-details', $programId)->with('error', 'Payment was canceled.');
    }
}
