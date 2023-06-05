<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use GuzzleHttp\Client;
use GuzzleHttp\Promise;


class Test extends Controller
{
    public function index( Request $request )
    {
        return view( 'test' );
    }

    public function check( Request $request )
    {
        $token    = $request->input( 'h-captcha-response' );
        $s_key    = '0x0000000000000000000000000000000000000000';
        $endpoint = "https://hcaptcha.com/siteverify";

        $client = new Client( [ 'base_uri' => $endpoint ] );

        $response = $client->request( 'POST', $endpoint, [
            'query' => [
                'response' => $token,
                'secret'   => $s_key,
            ]
        ] );

        dd(
            $response,
            $response->getBody(),
            json_decode( $response->getBody(), true )
        );


//         # PSEUDO CODE
//
//         SECRET_KEY = "your_secret_key"    # replace with your secret key
// VERIFY_URL = "https://hcaptcha.com/siteverify"
//
// # Retrieve token from post data with key 'h-captcha-response'.
// token = request.POST_DATA['h-captcha-response']
//
// # Build payload with secret key and token.
// data = { 'secret': SECRET_KEY, 'response': token }
//
// # Make POST request with data payload to hCaptcha API endpoint.
// response = http.post(url=VERIFY_URL, data=data)
//
// # Parse JSON from response. Check for success or error codes.
// response_json = JSON.parse(response.content)
// success = response_json['success']
//
//


//         //get the IP address of the origin of the submission
//         $ip = $_SERVER[ 'REMOTE_ADDR' ];
//
// //construct the url to send your private Secret Key, token and (optionally) IP address of the form submitter to Google to get a spam rating for the submission (I've saved '$reCAPTCHA_secret_key' in config.php)
//         $url = 'https://www.google.com/recaptcha/api/siteverify?secret=' . urlencode( $reCAPTCHA_secret_key ) . '&response=' . urlencode( $g_recaptcha_response ) . '&remoteip=' . urlencode( $ip );
//
// //save the response, e.g. print_r($response) prints { "success": true, "challenge_ts": "2019-07-24T11:19:07Z", "hostname": "your-website-domain.co.uk", "score": 0.9, "action": "contactForm" }
//         $response = file_get_contents( $url );
//
// //decode the response, e.g. print_r($responseKeys) prints Array ( [success] => 1 [challenge_ts] => 2019-07-24T11:19:07Z [hostname] => your-website-domain.co.uk [score] => 0.9 [action] => contactForm )
//         $responseKeys = json_decode( $response, true );
//
// //check if the test was done OK, if the action name is correct and if the score is above your chosen threshold (again, I've saved '$g_recaptcha_allowable_score' in config.php)
//         if ( $responseKeys[ "success" ] && $responseKeys[ "action" ] == 'contactForm' ) {
//             if ( $responseKeys[ "score" ] >= $g_recaptcha_allowable_score ) {
//                 //send email with contact form submission data to site owner/ submit to database/ etc
//                 //redirect to confirmation page or whatever you need to do
//             } elseif ( $responseKeys[ "score" ] < $g_recaptcha_allowable_score ) {
//                 //failed spam test. Offer the visitor the option to try again or use an alternative method of contact.
//             }
//         } elseif ( $responseKeys[ "error-codes" ] ) { //optional
//             //handle errors. See notes below for possible error codes
//             //personally I'm probably going to handle errors in much the same way by sending myself a the error code for debugging and offering the visitor the option to try again or use an alternative method of contact
//         } else {
//             //unkown screw up. Again, offer the visitor the option to try again or use an alternative method of contact.
//         }


        dd( $request );
    }


}
