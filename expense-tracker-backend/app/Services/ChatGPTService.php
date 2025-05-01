<?php

namespace App\Services;

use Prism\Prism\Prism;
use Prism\Prism\Enums\Provider;
use App\Models\Groups;
use App\Models\Expenses;
use Illuminate\Support\Facades\Log;

class ChatGPTService
{
    private $prism;

    public function __construct()
    {
        $this->prism = Prism::text()
            ->using(Provider::OpenAI, 'gpt-4o')
            ->withSystemPrompt('You are an expense tracking assistant. You help users manage their groups and expenses. You understand commands like "create group", "add expense", etc.');
    }

    public function processCommand(string $command)
    {
        try {
            $response = $this->prism
                ->withPrompt($command)
                ->asText();

            // Parse the response and perform actions
            if (str_contains(strtolower($command), 'create group')) {
                return $this->handleCreateGroup($command);
            } elseif (str_contains(strtolower($command), 'add expense')) {
                return $this->handleAddExpense($command);
            }

            return ['message' => 'Command processed successfully', 'response' => $response];
        } catch (\Exception $e) {
            Log::error('ChatGPT processing error: ' . $e->getMessage());
            return ['error' => 'Failed to process command', 'message' => $e->getMessage()];
        }
    }

    private function handleCreateGroup(string $command)
    {
        // Extract group name from command
        $groupName = $this->extractGroupName($command);

        if (!$groupName) {
            return ['error' => 'Could not determine group name from command'];
        }

        // Check if group exists
        $group = Groups::where('name', $groupName)->first();

        if (!$group) {
            $group = Groups::create([
                'name' => $groupName,
                'description' => 'Created via ChatGPT command'
            ]);
        }

        return [
            'message' => 'Group processed successfully',
            'group' => $group
        ];
    }

    private function handleAddExpense(string $command)
    {
        // Extract expense details from command
        $expenseDetails = $this->extractExpenseDetails($command);

        if (!$expenseDetails) {
            return ['error' => 'Could not determine expense details from command'];
        }

        // Find or create group
        $group = Groups::where('name', $expenseDetails['group'])->first();

        if (!$group) {
            $group = Groups::create([
                'name' => $expenseDetails['group'],
                'description' => 'Created via ChatGPT command'
            ]);
        }

        // Create expense
        $expense = Expenses::create([
            'group_id' => $group->id,
            'amount' => $expenseDetails['amount'],
            'description' => $expenseDetails['description'],
            'date' => now()
        ]);

        return [
            'message' => 'Expense added successfully',
            'expense' => $expense
        ];
    }

    private function extractGroupName(string $command)
    {
        // Simple extraction logic - can be enhanced
        if (preg_match('/create group\s+(.+)/i', $command, $matches)) {
            return trim($matches[1]);
        }
        return null;
    }

    private function extractExpenseDetails(string $command)
    {
        // Simple extraction logic - can be enhanced
        if (preg_match('/add expense of (.+?) of (\d+)\s+to\s+(.+)/i', $command, $matches)) {
            return [
                'description' => trim($matches[1]),
                'amount' => (float)$matches[2],
                'group' => trim($matches[3])
            ];
        }
        return null;
    }
}
