<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
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
            return ResponseHelper::success(['groups' => $groups]);
        } catch (Exception $e) {
            // Log the error for debugging purposes
            Log::error('Error fetching groups: ' . $e->getMessage());

            // Return a JSON response with a 500 status code
            return ResponseHelper::error('An error occurred while fetching groups.');
        }
    }

    public function store(StoreGroupRequest $request)
    {
        try {
            $validatedData = $request->validated();
            $validatedData['user_id'] = Auth::id();

            $group = Groups::create($validatedData);

            return ResponseHelper::success(['group' => $group], 'Group created successfully.', 201);
        } catch (Exception $e) {
            Log::error('Error creating group: ' . $e->getMessage());

            return ResponseHelper::error('An error occurred while creating the group.');
        }
    }

    public function update(StoreGroupRequest $request, $id)
    {
        try {
            $group = Groups::where('user_id', Auth::id())->findOrFail($id);
            $validatedData = $request->validated();
            $group->update($validatedData);

            return ResponseHelper::success(['group' => $group], 'Group updated successfully.');
        } catch (Exception $e) {
            Log::error('Error updating group: ' . $e->getMessage());

            return ResponseHelper::error('An error occurred while updating the group.');
        }
    }

    public function destroy(Request $request)
    {
        try {
            $group = Groups::where('user_id', Auth::id())->findOrFail($request->id);
            $group->delete();

            return ResponseHelper::success([], 'Group deleted successfully.');
        } catch (Exception $e) {
            Log::error('Error deleting group: ' . $e->getMessage());

            return ResponseHelper::error('An error occurred while deleting the group.');
        }
    }
}
