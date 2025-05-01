<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatGPTService
{
    protected $apiKey;
    protected $apiUrl = 'https://api.openai.com/v1/chat/completions';

    public function __construct()
    {
        $this->apiKey = config('services.openai.api_key');
    }

    public function parseExpenseText($text)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->apiUrl, [
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are a financial assistant that extracts expense information from text. Your task is to carefully parse the input and extract expense details.

                        Rules for extraction:
                        1. For amount:
                        - Extract ONLY the numerical value
                        - Remove any currency symbols ($, €, etc)
                        - Must be a positive number
                        - Use decimal point for cents/pence
                        - Examples: "20.50", "100.00", "5.99"

                        2. For name:
                        - Extract the main purpose or description
                        - Keep it concise but descriptive
                        - Remove any dates or amounts
                        - Examples: "Grocery shopping", "Taxi ride", "Office supplies"

                        3. For date:
                        - Must be in YYYY-MM-DD format
                        - If no date found, omit this field
                        - Convert relative dates (today, yesterday) to actual dates
                        - Examples: "2023-12-25", "2024-01-15"

                        IMPORTANT:
                        - Always return a valid JSON object
                        - Include at least name and amount
                        - If unsure about any field, omit it
                        - Do not include any additional text or explanations
                        - Ensure the JSON is properly formatted

                        Examples:

                        Input: "Spent $50.99 at Walmart for groceries yesterday"
                        Output: {"name": "Grocery shopping at Walmart", "amount": 50.99, "date": "2024-01-14"}

                        Input: "Coffee and snacks 12.50"
                        Output: {"name": "Coffee and snacks", "amount": 12.50}

                        If you cannot confidently extract both name and amount, return null.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $text
                    ]
                ],
                'temperature' => 0.3,
                'max_tokens' => 150
            ]);

            if ($response->successful()) {
                $result = $response->json();
                $parsedText = $result['choices'][0]['message']['content'];

                // Try to clean up the response if it contains any non-JSON text
                if (preg_match('/\{.*\}/s', $parsedText, $matches)) {
                    $parsedText = $matches[0];
                }

                $parsedData = json_decode($parsedText, true);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    Log::error('JSON parse error: ' . json_last_error_msg() . ' in response: ' . $parsedText);
                    return null;
                }

                // Validate parsed data
                if (!$this->isValidExpenseData($parsedData)) {
                    Log::error('Invalid expense data format: ' . $parsedText);
                    return null;
                }

                return $parsedData;
            }

            Log::error('ChatGPT API Error: ' . $response->body());
            return null;
        } catch (\Exception $e) {
            Log::error('Error parsing expense text: ' . $e->getMessage());
            return null;
        }
    }

    protected function isValidExpenseData($data)
    {
        if (!is_array($data)) {
            Log::error('Invalid data type: ' . gettype($data));
            return false;
        }

        // Check for required fields
        if (!isset($data['name']) || !isset($data['amount'])) {
            Log::error('Missing required fields in expense data');
            return false;
        }

        // Validate name
        if (!is_string($data['name']) || empty(trim($data['name']))) {
            Log::error('Invalid name format');
            return false;
        }

        // Validate amount
        if (!is_numeric($data['amount']) || $data['amount'] <= 0) {
            Log::error('Invalid amount format: ' . $data['amount']);
            return false;
        }

        // Validate date if present
        if (isset($data['date'])) {
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data['date'])) {
                Log::error('Invalid date format: ' . $data['date']);
                return false;
            }
        }

        return true;
    }
}
