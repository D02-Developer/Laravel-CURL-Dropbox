<?php

namespace App\Http\Controllers;

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
     * UPLOAD IMAGE USING POST REQUEST IN DROPBOX
     */
    public function imageRequest()
    {
        $headers = array(
            'Authorization: Bearer sl.BEVuuKbNHU4m4wnlhAO2pvPmRlk8zkE4exFZcVUcOrAQrMDa862Cr7Bp5SBXT5XoCoNEzUFKJcOgwEEqeC1kPheg-TG4rKTaz-alXTI4Vz8MjJlIDi3o6i4u_kdIwHGAvys8FG5M4PZw',
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
        dd($response);

        curl_close($ch);
    }
}
