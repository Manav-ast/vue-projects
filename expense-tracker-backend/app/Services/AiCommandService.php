<?php

namespace App\Services;

use Prism\Prism\Prism;
use Prism\Prism\Enums\Provider;
use App\Models\Groups;
use App\Models\Expenses;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AiCommandService
{
    public function processCommand(string $command)
    {
        try {
            $response = Prism::text()
                ->using(Provider::Gemini, 'gemini-2.0-flash')
                ->withPrompt($this->buildPrompt($command))
                ->asText();

            // Log the response for debugging
            Log::debug('Gemini response:', ['response' => $response]);

            // Parse the response
            $data = json_decode($response->text, true);

            if (!$data) {
                Log::error('Failed to parse JSON response:', ['response' => $response->text]);
                return [
                    'success' => false,
                    'message' => 'Could not understand the command. Please try again with a clearer command.'
                ];
            }

            return $this->handleAiResponse($data, $command);
        } catch (\Exception $e) {
            Log::error('Error processing command:', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return [
                'success' => false,
                'message' => 'Error processing command: ' . $e->getMessage()
            ];
        }
    }

    private function buildPrompt(string $command): string
    {
        return <<<PROMPT
            You are an AI assistant for an expense tracking application. Your task is to parse commands and return a JSON response in a specific format.

            IMPORTANT: You must ONLY return a valid JSON object, nothing else. The JSON must follow this exact structure:

            For creating a group:
            {
                "action": "create_group",
                "data": {
                    "name": "group name here"
                }
            }

            For adding an expense:
            {
                "action": "add_expense",
                "data": {
                    "group_name": "group name here",
                    "amount": number here (no quotes),
                    "description": "expense description here"
                }
            }

            Examples:
            Command: "create a group called Home"
            Response: {"action":"create_group","data":{"name":"Home"}}

            Command: "add expense of rent 50000 to Home group"
            Response: {"action":"add_expense","data":{"group_name":"Home","amount":50000,"description":"rent"}}

            Now parse this command: {$command}
        PROMPT;
    }

    private function handleAiResponse($response, string $originalCommand)
    {
        try {
            if (!isset($response['action']) || !isset($response['data'])) {
                Log::error('Invalid AI response format:', ['response' => $response]);
                return [
                    'success' => false,
                    'message' => 'Could not understand the command. Please try again with a clearer command.'
                ];
            }

            switch ($response['action']) {
                case 'create_group':
                    return $this->createGroup($response['data']);
                case 'add_expense':
                    return $this->addExpense($response['data']);
                default:
                    return [
                        'success' => false,
                        'message' => 'Unknown action. Please try again with a valid command.'
                    ];
            }
        } catch (\Exception $e) {
            Log::error('Error handling AI response:', ['error' => $e->getMessage(), 'response' => $response]);
            return [
                'success' => false,
                'message' => 'Error processing command: ' . $e->getMessage()
            ];
        }
    }

    private function createGroup(array $data)
    {
        if (!isset($data['name'])) {
            return [
                'success' => false,
                'message' => 'Group name is required'
            ];
        }

        try {
            $group = Groups::create([
                'name' => $data['name'],
                'user_id' => Auth::id()
            ]);

            return [
                'success' => true,
                'message' => "Group '{$data['name']}' created successfully",
                'data' => $group
            ];
        } catch (\Exception $e) {
            Log::error('Error creating group:', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'Error creating group: ' . $e->getMessage()
            ];
        }
    }

    private function addExpense(array $data)
    {
        if (!isset($data['group_name']) || !isset($data['amount'])) {
            return [
                'success' => false,
                'message' => 'Group name and amount are required'
            ];
        }

        try {
            $group = Groups::where('name', $data['group_name'])
                ->where('user_id', Auth::id())
                ->first();

            if (!$group) {
                // Create the group if it doesn't exist
                $group = Groups::create([
                    'name' => $data['group_name'],
                    'user_id' => Auth::id()
                ]);
            }

            $expense = Expenses::create([
                'amount' => $data['amount'],
                'description' => $data['description'] ?? null,
                'groups_id' => $group->id,
                'user_id' => Auth::id()
            ]);

            return [
                'success' => true,
                'message' => "Added expense of {$data['amount']} to group '{$data['group_name']}'",
                'data' => $expense
            ];
        } catch (\Exception $e) {
            Log::error('Error adding expense:', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'Error adding expense: ' . $e->getMessage()
            ];
        }
    }
}
