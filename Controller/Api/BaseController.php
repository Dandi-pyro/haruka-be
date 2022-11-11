<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
class BaseController
{
    /**
     * __call magic method.
     */
    public function __call($name, $arguments)
    {
        $this->sendOutput('', array('HTTP/1.1 404 Not Found'));
    }
 
    /**
     * Get URI elements.
     * 
     * @return array
     */
    protected function getUriSegments()
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri = explode( '/', $uri );
 
        return $uri;
    }
 
    /**
     * Get querystring params.
     * 
     * @return array
     */
    protected function getQueryStringParams()
    {
        return parse_str($_SERVER['QUERY_STRING'], $query);
    }
 
    /**
     * Send API output.
     *
     * @param mixed  $data
     * @param string $httpHeader
     */
    protected function sendOutput($data, $httpHeaders=array())
    {
        header_remove('Set-Cookie');
 
        if (is_array($httpHeaders) && count($httpHeaders)) {
            foreach ($httpHeaders as $httpHeader) {
                header($httpHeader);
            }
        }
 
        echo $data;
        exit;
    }

    protected function base64url_encode($str) {
        return rtrim(strtr(base64_encode($str), '+/', '-_'), '=');
    }

    protected function makeJWT($id, $username, $level, $email, $instansi)
    {
        $headers = array('alg'=>'HS256','typ'=>'JWT');
        $headers_encoded = $this->base64url_encode(json_encode($headers));
	
        $payload = array(
            'sub' => '1234567890',
            'id' => $id, 
            'username' => $username,
            'level' => $level,
            'email' => $email,
            'instansi' => $instansi, 
            'exp' => (time() + 60 * 24 * 30 * 6),
            'iat' => time(),
        );
	    $payload_encoded = $this->base64url_encode(json_encode($payload));
	
	    $signature = hash_hmac('SHA256', "$headers_encoded.$payload_encoded", JWT_KEY_SECRET, true);
	    $signature_encoded = $this->base64url_encode($signature);
	
	    $jwt = "$headers_encoded.$payload_encoded.$signature_encoded";
	
	    return $jwt;
    } 

    protected function is_jwt_valid($jwt, $secret = JWT_KEY_SECRET) {
        // split the jwt
        $tokenParts = explode('.', $jwt);
        $header = base64_decode($tokenParts[0]);
        $payload = base64_decode($tokenParts[1]);
        $signature_provided = $tokenParts[2];
    
        $expiration = json_decode($payload)->exp;
        $is_token_expired = ($expiration - time()) < 0;
    
        // build a signature based on the header and payload using the secret
        $base64_url_header = $this->base64url_encode($header);
        $base64_url_payload = $this->base64url_encode($payload);
        $signature = hash_hmac('SHA256', $base64_url_header . "." . $base64_url_payload, $secret, true);
        $base64_url_signature = $this->base64url_encode($signature);
    
        // verify it matches the signature provided in the jwt
        $is_signature_valid = ($base64_url_signature === $signature_provided);
        
        if ($is_token_expired || !$is_signature_valid) {
            return FALSE;
        } else {
            return TRUE;
        }
    }
    protected function sendEmail($receipt, $subject, $body) {
        $mail = new PHPMailer(true);
        $mail->SMTPDebug = 0;
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'fauzihzm@gmail.com';
        $mail->Password   = 'bkowhnhakejprmvh';
        $mail->SMTPSecure = 'tls';           
        $mail->Port       = 587;                                
        //pengirim
        $mail->setFrom('fauzihzm@gmail.com', 'Haruka');
        $mail->addAddress($receipt);     
        //Content
        $judul = $subject;
        $pesan = $body;
        $mail->isHTML(true);                                  
        $mail->Subject = $judul;
        $mail->Body    = $pesan;
        $mail->AltBody = '';
        $mail->send();
    }
}