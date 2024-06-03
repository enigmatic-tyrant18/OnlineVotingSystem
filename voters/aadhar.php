<?php
// Generate Auth Token
$url = 'https://api.sandbox.co.in/authenticate';
$headers = array(
    'accept: application/json',
    'x-api-key: key_live_bNQHvH66rmDIo8pxsw0WSJYa9BpjsO5w',
    'x-api-secret: secret_live_w5StH7Oyy5MTjxjHq4vOMAxixqL0KPJN',
    'x-api-version: 2.0'
);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$result = curl_exec($ch);
curl_close($ch);

$auth_token = 'eyJhbGciOiJIUzUxMiJ9.eyJhdWQiOiJBUEkiLCJyZWZyZXNoX3Rva2VuIjoiZXlKaGJHY2lPaUpJVXpVeE1pSjkuZXlKaGRXUWlPaUpCVUVraUxDSnpkV0lpT2lKaWFHRm5ZWFJoZVhWemFEY3hNRUJuYldGcGJDNWpiMjBpTENKaGNHbGZhMlY1SWpvaWEyVjVYMnhwZG1WZllrNVJTSFpJTmpaeWJVUkpiemh3ZUhOM01GZFRTbGxoT1VKd2FuTlBOWGNpTENKcGMzTWlPaUpoY0drdWMyRnVaR0p2ZUM1amJ5NXBiaUlzSW1WNGNDSTZNVGMwT0RreU56YzBNaXdpYVc1MFpXNTBJam9pVWtWR1VrVlRTRjlVVDB0RlRpSXNJbWxoZENJNk1UY3hOek01TVRjME1uMC5VcVRxc2NWRHo4Y0ZlaHdMS3piQW5Nd3J2VW11U081Vi1JdVR5cURSUjVLMndfLXhFSmNfanJvUlNkLUxQYXF5M1U0LUNicXlJQS1BWGc2dGpfY2ZTUSIsInN1YiI6ImJoYWdhdGF5dXNoNzEwQGdtYWlsLmNvbSIsImFwaV9rZXkiOiJrZXlfbGl2ZV9iTlFIdkg2NnJtRElvOHB4c3cwV1NKWWE5QnBqc081dyIsImlzcyI6ImFwaS5zYW5kYm94LmNvLmluIiwiZXhwIjoxNzE3NDc4MTQyLCJpbnRlbnQiOiJBQ0NFU1NfVE9LRU4iLCJpYXQiOjE3MTczOTE3NDJ9.1NYRmxfp2YPokHetMYl-cSOGZBo8wjTTUNpEch4-oMYaEa7fmpXSlZb6sDb_WqJfzjc7R4dEqapKEPiC43o-cg';

// Send OTP
if (isset($_GET['sendotp'])) {
    $aadharno = $_POST['aadhar_no'];
    $url = 'https://api.sandbox.co.in/kyc/aadhaar/okyc/otp';
    $data = array(
        '@entity' => 'in.co.sandbox.kyc.aadhaar.okyc.otp.request',
        'aadhaar_number' => $aadharno,
        'consent' => 'y',
        'reason' => 'For KYC'
    );
    $headers = array(
        "accept: application/json",
        "authorization:" . $auth_token,
        "content-type: application/json",
        "x-api-key: key_live_bNQHvH66rmDIo8pxsw0WSJYa9BpjsO5w",
        "x-api-version: 2.0"
    );

    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => json_encode($data),
        CURLOPT_HTTPHEADER => $headers
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    if ($err) {
        echo "cURL Error #:" . $err;
    } else {
        echo $response;
    }
} elseif (isset($_GET['verifyotp'])) {
    $refid = $_POST['reference_id'];
    $otp = $_POST['otp'];
    $url = 'https://api.sandbox.co.in/kyc/aadhaar/okyc/otp/verify';
    $data = array(
        "@entity" => "in.co.sandbox.kyc.aadhaar.okyc.request",
        "reference_id" => $refid,
        "otp" => $otp
    );

    $headers = array(
        "accept: application/json",
        "authorization: " . $auth_token,
        "content-type: application/json",
        "x-api-key: key_live_bNQHvH66rmDIo8pxsw0WSJYa9BpjsO5w",
        "x-api-version: 2.0"
    );

    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => json_encode($data),
        CURLOPT_HTTPHEADER => $headers
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    if ($err) {
        echo "cURL Error #:" . $err;
    } else {
        echo $response;
    }
}