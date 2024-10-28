<?php

namespace App\Http\Controllers;

use Stripe\Stripe;
use App\Models\Package;
use App\Models\Payment;
use Illuminate\Http\Request;
use App\Models\HiringRequest;
use App\Models\DonationPayment;
use Illuminate\Support\Facades\Log;
use Stripe\Checkout\Session as StripeSession;

class StripePaymentController extends Controller
{
    /**
     * Create a Stripe Payment and save it to the database.
     */

    public function createPayment(Request $request)
    {
        $paymentData = [
            "amount" => $request->amount,
            "success_url" => $request->success_url,
            "cancel_url" => $request->cancel_url,
            "first_name" => $request->first_name,
            "last_name" => $request->last_name,
            "email" => $request->email,
            "phone" => $request->phone,
            "address" => $request->address,
            "address_line_2" => $request->address_line_2,
            "city" => $request->city,
            "country" => $request->country,
            "zip" => $request->zip,
            "payment_type" => $request->payment_type,
        ];

        return stripe($paymentData);
    }


    /**
     * Handle payment success (after redirect from Stripe).
     */
    public function paymentSuccess(Request $request)
    {
        // Retrieve the session ID from the URL
        $session_id = $request->input('session_id');

        // Find the payment by checkout_session_id
        $payment = Payment::where('checkout_session_id', $session_id)->first();

        // Check if the payment exists
        if (!$payment) {
            // Return error response if payment is not found
            return jsonResponse(false, 'Payment not found', null, 404);
        }

        // If the payment is already approved, no need to check with Stripe
        if ($payment->status === 'approved') {
            return jsonResponse(true, 'Payment is already approved', $payment, 200);
        }

        // If payment is not approved, check with Stripe
        Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            // Retrieve session details from Stripe
            $session = \Stripe\Checkout\Session::retrieve($session_id);
        } catch (\Exception $e) {
            // Handle any errors from Stripe
            return jsonResponse(false, 'Error retrieving payment session from Stripe', null, 500);
        }

        // Use the private function to update payment status
        $this->updatePaymentStatus($payment, $session);

        // Return success response after updating payment status
        return jsonResponse(true, 'Payment status updated successfully', $payment, 200);
    }



    /**
     * Handle Stripe webhook notifications.
     */
    public function handleDonateWebhook(Request $request)
    {
        // Set your Stripe secret key
        Stripe::setApiKey(env('STRIPE_SECRET'));

        // Get the webhook secret from the environment variables
        $endpoint_secret = env('STRIPE_WEBHOOK_SECRET');

        // Get the payload and signature header from the request
        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');

        try {
            // Verify the event with the Stripe webhook secret
            $event = \Stripe\Webhook::constructEvent($payload, $sig_header, $endpoint_secret);

            // Handle the 'checkout.session.completed' event
            if ($event->type == 'checkout.session.completed') {
                $session = $event->data->object;

                // Attempt to find the donation payment by trxId
                $payment = DonationPayment::where('trxId', $session->client_reference_id)->first();

                if ($payment) {
                    // Update the payment status based on the session's payment status
                    $this->updateDonationStatus($payment, $session);
                } else {
                    // Log or handle missing payment record, if necessary
                    // Log::warning('DonationPayment not found for trxId: ' . $session->client_reference_id);
                }
            }
        } catch (\UnexpectedValueException $e) {
            return jsonResponse(false, 'Invalid Payload', null, 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            return jsonResponse(false, 'Invalid Signature', null, 400);
        } catch (\Exception $e) {
            return jsonResponse(false, 'Webhook Error', null, 400);
        }

        return jsonResponse(true, 'Webhook received', null, 200);
    }

    /**
     * Private function to update the donation payment status only if the payment record exists.
     */
    private function updateDonationStatus($payment, $session)
    {
        if ($session->payment_status === 'paid') {
            // Update payment to completed
            $payment->update([
                'status' => 'completed',
                'ipnResponse' => json_encode($session),
            ]);
        } else {
            // Update payment to failed
            $payment->update([
                'status' => 'failed',
                'ipnResponse' => json_encode($session),
            ]);
        }
    }



}
