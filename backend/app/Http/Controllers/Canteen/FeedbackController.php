<?php

namespace App\Http\Controllers\Canteen;

use App\Http\Controllers\Controller;
use App\Http\Requests\Canteen\RegisterFeedbackRequest;
use App\Http\Requests\Canteen\UpdateFeedbackRequest;
use App\Models\Feedback;
use App\Models\Food;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * Display a listing of the resource.
     * @OA\Get(
     * path="/api/feedbacks/{food_id}",
     * tags={"Feedback"},
     * summary="Get feedback for a specific food item",
     * description="Retrieves all feedback entries for a given food ID.",
     * security={{"sanctum": {}}},
     * operationId="getFeedbackByFoodId",
     * @OA\Parameter(
     * name="food_id",
     * in="path",
     * required=true,
     * description="ID of the food item to get feedback for",
     * @OA\Schema(type="integer")
     * ),
     * @OA\Response(
     * response=200,
     * description="A list of feedback entries",
     * @OA\JsonContent(
     * type="array",
     * @OA\Items(ref="#/components/schemas/Feedback")
     * )
     * ),
     * @OA\Response(
     * response=404,
     * description="Food not found",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=500,
     * description="Internal server error",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * )
     * )
     */
    public function index(string $food_id)
    {
       try {
            $food = Food::find($food_id);

            if (!$food) {
                return response()->json([
                    'errors' => 'Food not found'
                ], 404);
            }

            $feedbacks = $food->feedbacks()->get();

            return response()->json($feedbacks, 200);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'errors' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     * @OA\Post(
     * path="/api/feedbacks",
     * tags={"Feedback"},
     * summary="Create a new feedback entry",
     * description="Creates a new feedback entry for a food item.",
     * operationId="createFeedback",
     * security={{"sanctum": {}}},
     * @OA\RequestBody(
     * required=true,
     * description="Feedback details",
     * @OA\JsonContent(ref="#/components/schemas/RegisterFeedbackRequest")
     * ),
     * @OA\Response(
     * response=201,
     * description="Feedback created successfully",
     * @OA\JsonContent(ref="#/components/schemas/Feedback")
     * ),
     * @OA\Response(
     * response=422,
     * description="Validation errors",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=500,
     * description="Internal server error",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * )
     * )
     */
    public function create(RegisterFeedbackRequest $request)
    {
        try {
            $validated = $request->validated();

            $validated['user_id'] = Auth::user()->id;
            $validated['feedback_date'] = now();
            Feedback::create($validated);

            return response()->json([
                'success' => 'Feedback recieved.',
            ], 201);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'errors' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     * @OA\Patch(
     * path="/api/feedbacks/{feedback}",
     * tags={"Feedback"},
     * summary="Update an existing feedback entry",
     * description="Updates an existing feedback entry by its ID. Only the owner can update their feedback.",
     * operationId="updateFeedback",
     * security={{"sanctum": {}}},
     * @OA\Parameter(
     * name="feedback",
     * in="path",
     * required=true,
     * description="ID of the feedback to update",
     * @OA\Schema(type="integer")
     * ),
     * @OA\RequestBody(
     * required=true,
     * description="Updated feedback comment",
     * @OA\JsonContent(ref="#/components/schemas/UpdateFeedbackRequest")
     * ),
     * @OA\Response(
     * response=200,
     * description="Feedback updated successfully",
     * @OA\JsonContent(ref="#/components/schemas/Feedback")
     * ),
     * @OA\Response(
     * response=403,
     * description="Unauthorized action",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=404,
     * description="Feedback not found",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=500,
     * description="Internal server error",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * )
     * )
     */
    public function update(UpdateFeedbackRequest $request, string $feedback)
    {
        try {
            $feedback = Feedback::find($feedback);

            if (!$feedback) {
                return response()->json([
                    'errors' => 'Feedback not found'
                ], 404);
            }

            if ($feedback->user_id !== Auth::user()->id) {
                return response()->json([
                    'errors' => 'You are not authorized to update this feedback.'
                ], 403);
            }

            $validated = $request->validated();

            $feedback->update($validated);

            return response()->json([
                'success' => 'Feedback updated successfully.',
            ],200);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'errors' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     * @OA\Delete(
     * path="/api/feedbacks/{feedback}",
     * tags={"Feedback"},
     * summary="Delete an existing feedback entry",
     * description="Deletes an existing feedback entry by its ID. Only the owner or a SuperAdmin can delete the feedback.",
     * operationId="deleteFeedback",
     * security={{"sanctum": {}}},
     * @OA\Parameter(
     * name="feedback",
     * in="path",
     * required=true,
     * description="ID of the feedback to delete",
     * @OA\Schema(type="integer")
     * ),
     * @OA\Response(
     * response=204,
     * description="Feedback deleted successfully"
     * ),
     * @OA\Response(
     * response=403,
     * description="Unauthorized action",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=404,
     * description="Feedback not found",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=500,
     * description="Internal server error",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * )
     * )
     */
    public function destroy(Request $request, string $feedback)
    {
        try {
            $feedback = Feedback::find($feedback);

            if (!$feedback) {
                return response()->json([
                    'errors' => 'Feedback not found'
                ], 404);
            }

            if ($feedback->user_id !== Auth::user()->id && !Auth::user()->isSuperAdmin()) {
                return response()->json([
                    'errors' => 'You are not authorized to delete this feedback.'
                ], 403);
            }

            $feedback->delete();

            return response()->json(null, 204);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'errors' => $e->getMessage(),
            ], 500);
        }
    }
}
