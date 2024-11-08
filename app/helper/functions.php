<?php
use Stripe\Stripe;
use App\Models\Donner;
use Illuminate\Http\Request;
use App\Models\DonationPayment;
function int_en_to_bn($number)
{

    $bn_digits = array('০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯');
    $en_digits = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');

    return str_replace($en_digits, $bn_digits, $number);
}
function int_bn_to_en($number)
{

    $bn_digits = array('০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯');
    $en_digits = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');

    return str_replace($bn_digits, $en_digits, $number);
}

function month_number_en_to_bn_text($number)
{
    $en = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12);
    $bn = array('জানুয়ারি', 'ফেব্রুয়ারি', 'মার্চ', 'এপ্রিল', 'মে', 'জুন', 'জুলাই', 'অগাস্ট', 'সেপ্টেম্বর', 'অক্টোবর', 'নভেম্বর', 'ডিসেম্বর');

    // Adjust the number to be within 1-12 range
    $number = max(1, min(12, $number));

    return str_replace($en, $bn, $number);
}

function month_name_en_to_bn_text($name)
{
    $en = array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
    $bn = array('জানুয়ারি', 'ফেব্রুয়ারি', 'মার্চ', 'এপ্রিল', 'মে', 'জুন', 'জুলাই', 'অগাস্ট', 'সেপ্টেম্বর', 'অক্টোবর', 'নভেম্বর', 'ডিসেম্বর');
    return str_replace($en, $bn, $name);
}

 function extractUrlFromIframe($iframe)
{
    $dom = new \DOMDocument();
    @$dom->loadHTML($iframe);

    $iframes = $dom->getElementsByTagName('iframe');
    if ($iframes->length > 0) {
        $src = $iframes->item(0)->getAttribute('src');
        return $src;
    }

    return $iframe;
}


function routeUsesMiddleware($route, $middlewareName)
{
   return $middlewares = $route->gatherMiddleware();

    foreach ($middlewares as $middleware) {
        if (preg_match("/^$middlewareName:/", $middleware)) {
            return true;
        }
    }

    return false;
}



 function calculateDuration($startDate, $endDate)
{
    $start = \Carbon\Carbon::parse($startDate);
    $end = \Carbon\Carbon::parse($endDate);

    $days = $start->diffInDays($end);
    $months = $start->diffInMonths($end);
    $years = $start->diffInYears($end);

    return [
        'start_date' => $startDate,
        'end_date' => $endDate,
        'days' => $days,
        'months' => $months,
        'years' => $years,
    ];
}



function stripe($array = [])
{
    // Set Stripe API key
    Stripe::setApiKey(env('STRIPE_SECRET'));


    // Create a new payment record in the database
    $payment = createDonationPayment($array);

    // Create Stripe checkout session
    $session = \Stripe\Checkout\Session::create([
        'payment_method_types' => [
            'card',
            'ach_debit',
            'alipay',
            'bancontact',
            'ideal',
            'p24',
            'sepa_debit',
            'sofort',
            'wechat_pay',
            'klarna',
            'afterpay_clearpay',
        ],
        'line_items' => [[
            'price_data' => [
                'currency' => 'usd',
                'product_data' => [
                    'name' => 'Payment for ' . ($array['first_name'].' '.$array['last_name'] ?? 'no name'),
                ],
                'unit_amount' => $array['amount'] * 100, // Amount in cents
            ],
            'quantity' => 1,
        ]],
        'mode' => 'payment',
        'payment_intent_data' => [
            'capture_method' => 'automatic', // Adjust capture method if necessary
        ],
        'client_reference_id' => $payment->trx_id, // Set client reference id to trxId or other unique identifier
        'success_url' => $array['success_url'] . '?session_id={CHECKOUT_SESSION_ID}',
        'cancel_url' => $array['cancel_url'] . '?session_id={CHECKOUT_SESSION_ID}',
    ]);

    Log::info("session:".$session);

    // Update payment record with Stripe URL and CHECKOUT_SESSION_ID
    $payment->update([
        'paymentUrl' => $session->url,
        'checkout_session_id' => $session->id, // Save session ID to payment model
    ]);

    // Redirect the user to Stripe checkout
    return $session->url;
}



function createDonner($array = [])
{
    $donner = Donner::firstOrCreate(
        ['email' => $array['email']], // Check for an existing donor by email
        [
            'first_name' => $array['first_name'] ?? '',
            'last_name' => $array['last_name'] ?? '',
            'phone' => $array['phone'] ?? '',
            'address' => $array['address'] ?? '',
            'address_line_2' => $array['address_line_2'] ?? '',
            'city' => $array['city'] ?? '',
            'country' => $array['country'] ?? '',
            'zip' => $array['zip'] ?? '',
            'payment_type' => $array['payment_type'] ?? '',
        ]
    );

    return $donner;
}



function createDonationPayment($array=[])
{

    $donner = createDonner($array);

    $donationPayment = DonationPayment::create([
        'donner_id' => $donner->id ?? '',
        'trx_id' => time() ?? '',
        'amount' => $array['amount'] ?? '',
        'currency' => $array['currency'] ?? 'USD',
        'status' => 'pending',
        'date' => now(),
        'month' => now()->month,
        'year' => now()->year,
        'method' => $array['method'] ?? 'card',
    ]);

    return $donationPayment;
}


function jsonResponse($success, $message, $data = null, $statusCode = 200, array $extraFields = [])
{
    // Build the base response structure
    $response = [
        'success' => $success,
        'message' => $message,
        'data' => $data
    ];

    // Merge any extra fields into the response
    if (!empty($extraFields)) {
        $response = array_merge($response, $extraFields);
    }

    // Return the JSON response with the given status code
    return response()->json($response, $statusCode);
}
