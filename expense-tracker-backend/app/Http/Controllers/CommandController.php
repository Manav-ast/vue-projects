<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Groups;
use App\Models\Expenses;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CommandController extends Controller
{
    public function handle(Request $request)
    {
        $input = $request->input('command');
        $apiKey = env('GEMINI_API_KEY');

        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=$apiKey";

        $payload = [
            "contents" => [
                [
                    "role" => "user",
                    "parts" => [
                        [
                            "text" => $input
                        ]
                    ]
                ]
            ],
            "tools" => [
                [
                    "functionDeclarations" => [
                        [
                            "name" => "create_group",
                            "description" => "Creates a group with a given name.",
                            "parameters" => [
                                "type" => "object",
                                "properties" => [
                                    "name" => [
                                        "type" => "string",
                                        "description" => "Name of the group"
                                    ]
                                ],
                                "required" => ["name"]
                            ]
                        ],
                        [
                            "name" => "add_expense",
                            "description" => "Adds an expense to a group.",
                            "parameters" => [
                                "type" => "object",
                                "properties" => [
                                    "title" => ["type" => "string"],
                                    "amount" => ["type" => "number"],
                                    "date" => ["type" => "string"],
                                    "group" => ["type" => "string"]
                                ],
                                "required" => ["title", "amount", "date", "group"]
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post($url, $payload);

        $functionCall = $response->json()['candidates'][0]['content']['parts'][0]['functionCall'] ?? null;

        if (!$functionCall) {
            return response()->json([
                'error' => 'No function call returned from Gemini',
                'raw_response' => $response->json()
            ]);
        }

        $functionName = $functionCall['name'];
        $args = $functionCall['args'] ?? [];

        $userId = Auth::id();

        if ($functionName === 'create_group') {
            if (!isset($args['name'])) {
                return response()->json(['error' => 'Group name is required']);
            }

            $group = Groups::firstOrCreate(
                ['name' => $args['name']],
                ['user_id' => $userId]
            );

            return response()->json(['success' => true, 'group' => $group]);
        }

        if ($functionName === 'add_expense') {
            $required = ['title', 'amount', 'date', 'group'];
            foreach ($required as $field) {
                if (!isset($args[$field])) {
                    return response()->json(['error' => "$field is required"]);
                }
            }

            $group = Groups::firstOrCreate(
                ['name' => $args['group']],
                ['user_id' => $userId]
            );

            $expense = Expenses::create([
                'group_id' => $group->id,
                'name' => $args['title'],
                'amount' => $args['amount'],
                'date' => $args['date'],
                'user_id' => $userId
            ]);

            return response()->json(['success' => true, 'expense' => $expense]);
        }

        return response()->json(['error' => 'Unsupported action']);
    }
}
