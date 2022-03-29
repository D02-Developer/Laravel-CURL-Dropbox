<?php

namespace App\Http\Controllers;

use App\Models\DropboxToken;
use Illuminate\Http\Request;

class FileUploadController extends Controller
{
    /**
     * CREATE A NEW FOLDER IN DROPBOX
     */
    public static function curlCall()
    {
        $data = [
            "path" => "/laravelfileupload/data",
            "autorename" => false
        ];

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.dropboxapi.com/2/files/create_folder_v2",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30000,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
                "accept: */*",
                "Authorization: Bearer sl.BD8MEPcZoOyWu1_2JWgMeir82YAIAyPLPLjwzoTVzHaLgMbl6JaK6-ZGs-ZOjh9uZOxwAAk5FNFoI2cISxxL8690izaMaIfK7Ui0Otcow41BzbUCG_FyHbf363LcY269JWSBpKc",
                "content-type: application/json",
            ),
        ));

        $response = curl_exec($curl);
        dd($response);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            print_r(json_decode($response));
        }
    }

    /**
     * UPLOAD STATIC IMAGE IN DROPBOX
     */
    public function uploadImage()
    {
        $headers = array(
            'Authorization: Bearer sl.BER-wOFjQHkh0d3qcMt1N_WsFW69-O-N_NDFQDtR7AWNCyZwS07CBEzdujPn9EDqW5-B-2LQUxPjacQ5NvuQAJ9lyGQHF-uQ-V4lE_Ra098oRJ_EIxMepgKK03cShrVToHdvOBW0EYFY',
            'Content-Type: application/octet-stream',
            'Dropbox-API-Arg: ' .
                json_encode(
                    array(
                        "path" => '/' . basename('Logo.jpeg'),
                        "mode" => "add",
                        "autorename" => true,
                        "mute" => false
                    )
                )
        );

        $ch = curl_init('https://content.dropboxapi.com/2/files/upload');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, true);

        $path = 'Logo.jpeg';
        $fp = fopen($path, 'rb');
        $size = filesize($path);

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, fread($fp, $size));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        dd($response);
        curl_close($ch);
        fclose($fp);
    }

    /**
     * UPLOAD IMAGE DYNAMICALLY IN DROPBOX
     */
    public function imageRequest()
    {
        $model = DropboxToken::all();
        foreach ($model as $item) {
            $token = $item['access_token'];
        }
        // $token = DropboxToken::latest('access_token')->first();

        $headers = array(
            'Authorization: Bearer ' . $token,
            'Content-Type: application/octet-stream',
            'Dropbox-API-Arg: ' .
                json_encode(
                    array(
                        "path" => '/' . basename($_FILES['image']['name']),
                        "mode" => "add",
                        "autorename" => true,
                        "mute" => false
                    )
                )
        );

        $ch = curl_init('https://content.dropboxapi.com/2/files/upload');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, true);

        $path = fopen($_FILES['image']['tmp_name'], 'r');
        $data = fread($path, $_FILES['image']['size']);

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        // return view('dropbox');
        dd(json_decode($response));

        curl_close($ch);
    }

    /**
     * GENERATE TOKEN DROPBOX
     */
    public function getToken()
    {
        $code = $_GET['code'];

        $headers = array(
            "Authorization: Basic " . base64_encode(env('APP_KEY_DROPBOX') . ":" . env('APP_SECRET_DROPBOX')),
            "Content-Type: application/x-www-form-urlencoded"
        );

        $data = array(
            "code" => $code,
            "grant_type" => "authorization_code",
            "redirect_uri" => "http://127.0.0.1:8000/token"
        );

        $ch = curl_init('https://api.dropboxapi.com/oauth2/token');
        curl_setopt($ch, CURLOPT_POST, true);

        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = json_decode(curl_exec($ch));

        $response1 = [
            'access_token' => $response->access_token,
            'token_type' => $response->token_type,
            'expires_in' => $response->expires_in,
            'refresh_token' => $response->refresh_token,
            'scope' => $response->scope,
            'uid' => $response->uid,
            'account_id' => $response->account_id
        ];

        $uid = DropboxToken::all();

        foreach ($uid as $item) {
            if ($item['uid'] == $response1['uid']) {
                
                $id = $item['id']; 
                $refresh_token = $item['refresh_token'];

                $data2 = array(
                    "refresh_token" => $refresh_token,
                    "grant_type" => "refresh_token",
                );

                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data2));
                $response2 = json_decode(curl_exec($ch));

                $response3 = [
                    'access_token' => $response2->access_token,
                    'token_type' => $response2->token_type,
                    'expires_in' => $response2->expires_in
                ];

                DropboxToken::where('id', $id)->update($response3);
                echo 'Update access token';
                return view('welcome');
            } else {
                DropboxToken::create($response1);
                echo 'Create access token';
                return view('welcome');
            }
        }

        DropboxToken::create($response1);
        echo 'Create access token';
        return view('welcome');

        curl_close($ch);
    }
}
