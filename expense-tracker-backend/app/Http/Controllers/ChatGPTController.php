<?php

namespace App\Http\Controllers;

use App\Services\ChatGPTService;
use Illuminate\Http\Request;

class ChatGPTController extends Controller
{
    protected $chatGPTService;

    public function __construct(ChatGPTService $chatGPTService)
    {
        $this->chatGPTService = $chatGPTService;
    }

    public function processCommand(Request $request)
    {
        $request->validate([
            'command' => 'required|string'
        ]);

        $result = $this->chatGPTService->processCommand($request->command);

        return response()->json($result);
    }
}
