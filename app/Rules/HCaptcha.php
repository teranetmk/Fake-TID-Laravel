<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use GuzzleHttp\Client;


class HCaptcha implements Rule
{
    /**
     * @var string
     */
    private $endpoint = "https://hcaptcha.com/siteverify";


    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        
    }


    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes( $attribute, $value )
    {
       
        if ( empty( $value ) ) {
            $this->error_msg = ':attribute field is required.';
            return false;
        }

        $client = new Client( [ 'base_uri' => $this->endpoint ] );

        $response = $client->request( 'POST', $this->endpoint, [
            'query' => [
                'response' => $value,
                'secret'   => env( 'HCAPTCHA_PRIVATE_KEY' ),
            ]
        ] );

        if ( $response->getStatusCode() != 200 ) {
            $this->error_msg = 'ReCaptcha field is required.';
            return false;
        }

        if ( !json_decode( $response->getBody(), false ) ) {
            $this->error_msg = 'Failed to validate captcha.';
            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->error_msg;
    }
}
