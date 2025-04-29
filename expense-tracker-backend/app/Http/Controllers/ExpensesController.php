<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreExpenseRequest;
use App\Models\Expenses;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log as FacadesLog;
use Illuminate\Support\Facades\Auth;
use App\Exports\ExpensesExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

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

            return response()->json([
                'error' => 'An error occurred while deleting the expense.',
            ], 500);
        }
    }

    public function exportPdf()
    {
        try {
            $user = Auth::user();
            if (!$user) {
                throw new Exception('User not authenticated');
            }

            $expenses = Expenses::where('user_id', $user->id)
                ->with('group')
                ->get();

            if ($expenses->isEmpty()) {
                throw new Exception('No expenses found for the user');
            }

            FacadesLog::info('Generating PDF for user: ' . $user->id . ' with ' . $expenses->count() . ' expenses');

            $pdf = Pdf::loadView('exports.expenses-pdf', [
                'user' => $user,
                'expenses' => $expenses
            ]);

            $fileName = 'expenses-' . now()->format('Y-m-d') . '.pdf';
            $pdfPath = 'pdf/' . $fileName;

            // Ensure the pdf directory exists
            if (!file_exists(storage_path('app/public/pdf'))) {
                mkdir(storage_path('app/public/pdf'), 0755, true);
            }

            // Store the PDF in the public/pdf storage
            $pdf->save(storage_path('app/public/' . $pdfPath));

            FacadesLog::info('PDF generated successfully: ' . $pdfPath);

            //Return the file for download
            return response()->download(storage_path('app/public/' . $pdfPath), $fileName, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"'
            ]);
        } catch (Exception $e) {
            FacadesLog::error('Error exporting PDF: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return response()->json([
                'error' => 'An error occurred while generating the PDF: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function exportCsv()
    {
        try {
            $fileName = 'expenses-' . now()->format('Y-m-d') . '.csv';
            $csvPath = 'csv/' . $fileName;
            Excel::store(new ExpensesExport(), $csvPath, 'public');

            return response()->json([
                'success' => true,
                'file_url' => asset('storage/' . $csvPath)
            ]);
        } catch (Exception $e) {
            FacadesLog::error('Error exporting expenses: ' . $e->getMessage());
            return response()->json([
                'error' => 'An error occurred while exporting expenses.',
            ], 500);
        }
    }
}
