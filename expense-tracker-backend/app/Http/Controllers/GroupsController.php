<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGroupRequest;
use App\Models\Groups;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Exception;

class GroupsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function index()
    {
        try {
            $groups = Groups::where('user_id', Auth::id())->get();
            return response()->json([
                'groups' => $groups,
            ]);
        } catch (Exception $e) {
            // Log the error for debugging purposes
            Log::error('Error fetching groups: ' . $e->getMessage());

            // Return a JSON response with a 500 status code
            return response()->json([
                'error' => 'An error occurred while fetching groups.',
            ], 500);
        }
    }

    public function store(StoreGroupRequest $request)
    {
        try {
            $validatedData = $request->validated();
            $validatedData['user_id'] = Auth::id();

            $group = Groups::create($validatedData);

            return response()->json([
                'group' => $group,
            ], 201);
        } catch (Exception $e) {
            Log::error('Error creating group: ' . $e->getMessage());

            return response()->json([
                'error' => 'An error occurred while creating the group.',
            ], 500);
        }
    }

    public function update(StoreGroupRequest $request, $id)
    {
        try {
            $group = Groups::where('user_id', Auth::id())->findOrFail($id);
            $validatedData = $request->validated();
            $group->update($validatedData);

            return response()->json([
                'message' => 'Group updated successfully.',
                'group' => $group,
            ], 200);
        } catch (Exception $e) {
            Log::error('Error updating group: ' . $e->getMessage());

            return response()->json([
                'error' => 'An error occurred while updating the group.',
            ], 500);
        }
    }

    public function destroy(Request $request)
    {
        try {
            $group = Groups::where('user_id', Auth::id())->findOrFail($request->id);
            $group->delete();

            return response()->json([
                'message' => 'Group deleted successfully.',
            ], 200);
        } catch (Exception $e) {
            Log::error('Error deleting group: ' . $e->getMessage());

            return response()->json([
                'error' => 'An error occurred while deleting the group.',
            ], 500);
        }
    }
}
