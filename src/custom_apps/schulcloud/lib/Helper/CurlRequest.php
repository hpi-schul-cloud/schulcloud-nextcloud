<?php
namespace OCA\Schulcloud\Helper;

class CurlRequest {

    public const API_URL = 'http://localhost:80/';

    public function __construct() {
        
	}

    public function send(string $method, string $url, $data = false):string | Exception {
        $curl = curl_init();

        $adminUser = getenv('NEXTCLOUD_ADMIN_USER');
        $adminPassword = getenv('NEXTCLOUD_ADMIN_PASSWORD');

        switch ($method){
            case 'POST':
                curl_setopt($curl, CURLOPT_POST, 1);
                if ($data)
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                break;
            case 'PUT':
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
                if ($data)
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                break;
            default:
                if ($data)
                $url = sprintf('%s?%s', $url, http_build_query($data));
        }

        // Optional Authentication:
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_USERPWD, $adminUser . ':' . $adminPassword);

        // Options:
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'OCS-APIRequest: true',
            'Content-Disposition: form-data'
        ));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        // Execute:
        $result = curl_exec($curl);
        curl_close($curl);

        if(curl_errno($curl))
            throw new \Exception('Curl error: '. curl_error($curl));

        return $result;
    }
}