<?php

namespace App\Http\Controllers;

use App\Services\ChatGPTService;
use App\Models\Expenses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class NaturalLanguageExpenseController extends Controller
{
    protected $chatGPTService;

    public function __construct(ChatGPTService $chatGPTService)
    {
        $this->middleware('auth:sanctum');
        $this->chatGPTService = $chatGPTService;
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'text' => 'required|string',
                'group_id' => 'required|exists:groups,id'
            ]);

            $parsedExpense = $this->chatGPTService->parseExpenseText($request->text);

            if (!$parsedExpense) {
                return response()->json([
                    'error' => 'Could not parse expense information from the text.'
                ], 422);
            }

            // Create the expense with parsed data
            $expense = Expenses::create([
                'name' => $parsedExpense['name'],
                'amount' => $parsedExpense['amount'],
                'date' => $parsedExpense['date'] ?? now(),
                'group_id' => $request->group_id,
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'message' => 'Expense created successfully',
                'expense' => $expense
            ]);
        } catch (\Exception $e) {
            Log::error('Error creating expense from natural language: ' . $e->getMessage());
            return response()->json([
                'error' => 'An error occurred while processing your request.'
            ], 500);
        }
    }
}
