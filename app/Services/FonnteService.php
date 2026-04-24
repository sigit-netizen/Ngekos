<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class FonnteService
{
    protected $token;
    protected $apiUrl = 'https://api.fonnte.com/send';

    public function __construct()
    {
        $this->token = env('FONNTE_TOKEN');
    }

    /**
     * Send a WhatsApp message.
     *
     * @param string $target Number or targets separated by comma
     * @param string $message Message content
     * @param array $additionalParams Other Fonnte parameters
     * @return array
     */
    public function sendMessage($target, $message, $additionalParams = [])
    {
        if (empty($this->token)) {
            return [
                'success' => false,
                'message' => 'Fonnte Token is not configured in .env'
            ];
        }

        $curl = curl_init();

        $payload = array_merge([
            'target' => $target,
            'message' => $message,
            'countryCode' => '62', // Default to Indonesia
        ], $additionalParams);

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->apiUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_HTTPHEADER => array(
                'Authorization: ' . $this->token
            ),
        ));

        $response = curl_exec($curl);
        $error = curl_error($curl);
        curl_close($curl);

        if ($error) {
            Log::error('FonnteService cURL Error: ' . $error, ['target' => $target]);
            return [
                'success' => false,
                'message' => 'cURL Error: ' . $error
            ];
        }

        $decoded = json_decode($response, true);

        // Log respons dari Fonnte untuk debugging
        if (!$decoded || (isset($decoded['status']) && $decoded['status'] == false)) {
            Log::warning('FonnteService gagal kirim pesan', [
                'target' => $target,
                'response_raw' => $response,
                'decoded' => $decoded
            ]);
        } else {
            Log::info('FonnteService berhasil kirim pesan ke: ' . $target);
        }

        return $decoded;
    }
}
