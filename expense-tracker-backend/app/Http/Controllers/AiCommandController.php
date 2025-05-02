<?php

namespace App\Http\Controllers;

use App\Services\AiCommandService;
use Illuminate\Http\Request;

class AiCommandController extends Controller
{
    protected $aiService;

    public function __construct(AiCommandService $aiService)
    {
        $this->aiService = $aiService;
    }

    public function processCommand(Request $request)
    {
        $command = $request->input('command');

        if (!$command) {
            return response()->json([
                'success' => false,
                'message' => 'No command provided'
            ], 400);
        }

        $result = $this->aiService->processCommand($command);
        return response()->json($result);
    }
}
