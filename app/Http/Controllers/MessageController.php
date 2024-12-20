<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Phone;
use Illuminate\Support\Facades\Log;

class MessageController extends Controller
{
    public function index()
    {
        // Retrieve all phone records to show in the recipients selection
        $phones = Phone::all();  // Assuming 'Phone' model has 'phone_id' and 'phone_number'
        
        // Pass the phone numbers to the view
        return view('students.message.message_index', compact('phones'));
    }

    public function sendMessage(Request $request)
    {
        // Get the list of selected recipient phone IDs
        $recipients = $request->input('recipients'); // Array of selected phone IDs
        $message = $request->input('message'); // Message content
        
        // Validate if there are recipients selected
        if (empty($recipients)) {
            return response()->json(['message' => 'No recipients selected.'], 400);
        }

        // WhatsApp Cloud API URL
        $apiUrl = 'https://graph.facebook.com/v21.0/477418495458042/messages'; // Replace with your actual API URL

        // Access token for authorization
        $accessToken = env('WHATSAPP_ACCESS_TOKEN'); // Replace with your actual access token

        // Retrieve the phone details based on selected phone IDs
        $phones = Phone::whereIn('phone_id', $recipients)->get();
        
        // Send message to each selected recipient
        foreach ($phones as $phone) {
            // Concatenate country code and phone number to create the full phone number
            $recipientNumber = ltrim($phone->country_code, '+');
            $phoneNumber = ltrim($phone->phone_number, '0');
            $fullPhoneNumber = $recipientNumber . $phoneNumber;
            // Prepare the message data for the API request
            $data = [
                'messaging_product' => 'whatsapp',
                'to' => $fullPhoneNumber,  // Send message to this full phone number
                'type' => 'text',
                'text' => [
                    'body' => $message,  // The message content
                ]
            ];

            // Send the message using WhatsApp API
            $response = $this->sendApiRequest($apiUrl, $data, $accessToken);

            // Check the response and handle success or failure
            if ($response['status'] != 200) {
                return response()->json(['message' => 'Failed to send message.', $fullPhoneNumber], 400);
            }
        }

        // Return success message if all messages sent
        return response()->json(['message' => 'Messages sent successfully!']);
    }

    // Helper function to send the POST request to the WhatsApp API
    private function sendApiRequest($url, $data, $accessToken)
    {
        $ch = curl_init($url);
        
        // Set HTTP headers
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json',
        ]);
        
        // Set the options for the POST request
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        
        // Execute the cURL request and get the response
        $response = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        // Log error for debugging if the status is not OK (200)
        Log::error("Failed API request", [
            'status' => $status,
            'response' => $response,
        ]);
        
        return ['status' => $status, 'response' => json_decode($response, true)];
    }
}
