<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreExpenseRequest;
use App\Models\Expenses;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log as FacadesLog;
use Illuminate\Support\Facades\Auth;

class ExpensesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    function index()
    {
        try {
            $expenses = Expenses::where('user_id', Auth::id())->get();
            return response()->json([
                'expenses' => $expenses,
            ]);
        } catch (Exception $e) {
            // Log the error for debugging purposes
            FacadesLog::error('Error fetching expenses: ' . $e->getMessage());

            // Return a JSON response with a 500 status code
            return response()->json([
                'error' => 'An error occurred while fetching expenses.',
            ], 500);
        }
    }

    function store(StoreExpenseRequest $request)
    {
        try {
            $validatedData = $request->validated();
            $validatedData['user_id'] = Auth::id();

            $expense = Expenses::create($validatedData);

            return response()->json([
                'expense' => $expense,
            ]);
        } catch (Exception $e) {
            // Log the error for debugging purposes
            FacadesLog::error('Error creating expense: ' . $e->getMessage());

            // Return a JSON response with a 500 status code
            return response()->json([
                'error' => 'An error occurred while creating the expense.',
            ], 500);
        }
    }

    function update(StoreExpenseRequest $request, $id)
    {
        try {
            // Find the expense by ID and user_id to ensure ownership
            $expense = Expenses::where('user_id', Auth::id())->findOrFail($id);

            // Use the validated data from the request
            $validatedData = $request->validated();
            $validatedData['user_id'] = Auth::id(); // Ensure user_id is set

            // Update the expense with the validated data
            $expense->update($validatedData);

            return response()->json([
                'message' => 'Expense updated successfully.',
                'expense' => $expense,
            ], 200);
        } catch (Exception $e) {
            // Log the error for debugging purposes
            FacadesLog::error('Error updating expense: ' . $e->getMessage());

            // Return a JSON response with a 500 status code
            return response()->json([
                'error' => 'An error occurred while updating the expense.',
            ], 500);
        }
    }

    function destroy(Request $request)
    {
        try {
            $expense = Expenses::findOrFail($request->id);
            $expense->delete();

            return response()->json([
                'message' => 'Expense deleted successfully.',
            ], 200);
        } catch (Exception $e) {
            // Log the error for debugging purposes
            FacadesLog::error('Error deleting expense: ' . $e->getMessage());

            // Return a JSON response with a 500 status code
            return response()->json([
                'error' => 'An error occurred while deleting the expense.',
            ], 500);
        }
    }
}
