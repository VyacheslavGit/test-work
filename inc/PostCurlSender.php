<?php

/**
 * Class PostCurlSender
 */
class PostCurlSender implements ISender
{
    /**
     * Send post curl request
     *
     * @param string $url
     * @param array $data
     * @return mixed
     */
    public function sendRequest(string $url, array $data = [])
    {
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($curl, CURLOPT_POST, true);
        if (!empty($data)) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }

        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        $response = curl_exec($curl);
        $response = json_decode($response, true);

        curl_close($curl);

        return $response;
    }
}

