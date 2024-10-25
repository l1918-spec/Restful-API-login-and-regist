<?php
require './vendor/autoload.php'; // Importing dependencies

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtHandler
{
    protected $jwt_secret;
    protected $token;
    protected $issuedAt;
    protected $expire;
    protected $jwt;

    public function __construct()
    {
        // Set default timezone
        date_default_timezone_set('Asia/Kolkata');

        // Get the current timestamp
        $this->issuedAt = time();

        // Token validity (3600 seconds = 1 hour)
        $this->expire = $this->issuedAt + 3600;

        // Set the secret key for JWT
        $this->jwt_secret = 'this_is_my_secret';
    }

    // Method to encode data into a JWT token
    public function jwtEncodeData($iss, $data)
    {
        $this->token = [
            'iss' => $iss,
            'aud' => $iss,
            'iat' => $this->issuedAt,
            'exp' => $this->expire,
            'data' => $data
        ];

        // Encode the token
        $this->jwt = JWT::encode($this->token, $this->jwt_secret, 'HS256');
        return $this->jwt;
    }

    // Method to decode a JWT token
    public function jwtDecodeData($jwt_token)
    {
        try {
            // Use Firebase\JWT\Key to validate the token
            $decoded = JWT::decode($jwt_token, new Key($this->jwt_secret, 'HS256'));
            return [
                'data' => $decoded->data
            ];
        } catch (\Firebase\JWT\ExpiredException $e) {
            return [
                'message' => 'Token expired: ' . $e->getMessage()
            ];
        } catch (\Exception $e) {
            return [
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }
}
?>