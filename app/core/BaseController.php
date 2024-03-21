<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';

class BaseController extends Filter
{
    public function view($view, $data = [])
    {
        if (count($data) > 0) {
            extract($data);
        }
        // require_once __DIR__ . '/../views/' . $view . '.php';
    }

    public function redirect($url)
    {
        header('Location: ' . $url);
    }


    public function model($model)
    {
        require_once __DIR__ . '/../models/' . $model . '.php';
        return new $model();
    }

    public function getToken()
    {
        $header = getallheaders();

        if (!isset($header['Authorization']) || empty($header['Authorization'])) {
            return false;
        } else {
            $authorization = $header['Authorization'];
            [$bearer, $token] = explode(" ", $authorization);
            if ($bearer !== "Bearer" || empty($token)) {
                return false;
            }

            $payload = $this->verify_jwt($token);

            $isExist = $this->model('User')->getUserByEmail($payload['email']);
            if (!is_array($isExist)) {
                return false;
            }

            return $isExist;
        }
    }

    public function base64url_encode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    public function base64url_decode($str)
    {
        // Replace non-url-safe characters with their base64 counterparts
        $str = strtr($str, '-_', '+/');

        // Decode the base64url encoded string
        return base64_decode($str, true);
    }

    public function create_jwt($payload)
    {
        // karna gak ngerti pake composer di docker maka pake ini
        $headers = ['alg' => 'HS256', 'typ' => 'JWT'];
        $headers_encoded = $this->base64url_encode(json_encode($headers));

        $payload = ['email' => $payload];
        $payload_encoded = $this->base64url_encode(json_encode($payload));

        $key = 'secret';
        $signature = hash_hmac('sha256', "$headers_encoded.$payload_encoded", $key, true);
        $signature_encoded = $this->base64url_encode($signature);

        $token = "$headers_encoded.$payload_encoded.$signature_encoded";
        return $token;
    }
    public function verify_jwt($jwt)
    {
        try {
            //code...
            $parts = explode('.', $jwt);
            if (count($parts) !== 3) {
                throw new Exception('Invalid JWT format');
            }

            // Decode header and payload (Base64url encoding)
            $header = json_decode($this->base64url_decode($parts[0]), true);
            $payload = json_decode($this->base64url_decode($parts[1]), true);

            // Validate header algorithm (only supports HS256 for simplicity)
            if (!isset($header['alg']) || $header['alg'] !== 'HS256') {
                throw new Exception('Unsupported signing algorithm');
            }

            // Validate expiration time (exp claim)
            if (isset($payload['exp']) && time() > $payload['exp']) {
                throw new Exception('JWT expired');
            }

            // Recreate signature (HMAC SHA-256)
            $signature = hash_hmac('sha256', implode('.', [$parts[0], $parts[1]]), 'secret', true);
            $encoded_signature = $this->base64url_encode($signature);

            // Verify signature
            if ($encoded_signature !== $parts[2]) {
                throw new Exception('Invalid signature');
            }

            // JWT is valid, return payload data
            return $payload;
        } catch (\Throwable $th) {
            header('Content-Type: application/json');
            header('HTTP/1.1 500 Internal Server Error');
            echo json_encode($th->getMessage());
            exit();
        }
    }

    public function sendEmail($data, $from)
    {

        $mail = new PHPMailer();
        $mail->IsSMTP();
        $mail->Mailer = "smtp";

        $mail->SMTPDebug  = 1;
        $mail->SMTPAuth   = TRUE;
        $mail->SMTPSecure = "tls";
        $mail->Port       = 587;
        $mail->Host       = "smtp.gmail.com";
        $mail->Username   = "fikilifadly-179@gmail.com";
        $mail->Password   = "bcEZjqwnkNY0HbNx";

        $mail->IsHTML(true);
        $mail->AddAddress($data['to'], "recipient-name");
        $mail->SetFrom($from, "from-name");
        $mail->Subject = "Test is Test Email sent via Gmail SMTP Server using PHP Mailer";
        $content = "<b>" . $data['message'] . "</b>";

        $mail->MsgHTML($content);
        if (!$mail->Send()) {
            echo "Error while sending Email.";
            var_dump($mail);
        } else {
            echo "Email sent successfully";
        }
    }
}
