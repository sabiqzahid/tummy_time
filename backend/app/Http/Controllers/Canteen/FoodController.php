<?php

namespace App\Http\Controllers\Canteen;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\Food;
use App\Http\Requests\Canteen\RegisterFoodRequest;
use App\Http\Requests\Canteen\UpdateFoodRequest;
use App\OpenApi\Annotations as OA;

class FoodController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    private function imageHandler(Request $request, array &$validated, ?Food $food = null): void
    {
        if ($request->hasFile('image_url')) {
            if ($food && $food->image_url) {
                $this->deleteOldImage($food->image_url);
            }
            $path = $request->file('image_url')->store('food_images', 'public');
            $validated['image_url'] = Storage::url($path);
        } else if (array_key_exists('image_url', $validated) && is_null($validated['image_url'])) {
            // If remove Image button is pressed we will send image_url null
            // otherwise the key won't be sent
            if ($food && $food->image_url) {
                $this->deleteOldImage($food->image_url);
            }
            $validated['image_url'] = null;
        } else {
            unset($validated['image_url']);
        }
    }

    private function deleteOldImage(?string $imageUrl): void
    {
        if ($imageUrl) {
            $path = str_replace(Storage::url(''), '', $imageUrl);
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }
    }

    /**
     * @OA\Get(
     * path="/api/foods",
     * operationId="getFoodsList",
     * tags={"Food"},
     * summary="Get a paginated list of all food items",
     * description="Retrieves a paginated list of all food items with optional filtering by category, availability, searching by name, and sorting by price. Accessible by any authenticated user.",
     * security={{"sanctum": {}}},
     * @OA\Parameter(
     * name="page",
     * in="query",
     * description="Page number for pagination",
     * required=false,
     * @OA\Schema(type="integer", default=1)
     * ),
     * @OA\Parameter(
     * name="per_page",
     * in="query",
     * description="Number of items per page",
     * required=false,
     * @OA\Schema(type="integer", default=10)
     * ),
     * @OA\Parameter(
     * name="category",
     * in="query",
     * description="Filter food by category name",
     * required=false,
     * @OA\Schema(type="string")
     * ),
     * @OA\Parameter(
     * name="search",
     * in="query",
     * description="Search food by name",
     * required=false,
     * @OA\Schema(type="string")
     * ),
     * @OA\Parameter(
     * name="is_available",
     * in="query",
     * description="Filter by availability (true/false)",
     * required=false,
     * @OA\Schema(type="boolean")
     * ),
     * @OA\Parameter(
     * name="sort_by_price",
     * in="query",
     * description="Sort by price (asc or desc)",
     * required=false,
     * @OA\Schema(
     * type="string",
     * enum={"asc", "desc"}
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="Successful operation",
     * @OA\JsonContent(ref="#/components/schemas/FoodPagination")
     * ),
     * @OA\Response(
     * response=401,
     * description="Unauthenticated",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=500,
     * description="Internal Server Error",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Food::query();

            if ($request->has('category')) {
                $categoryName = $request->input('category');
                $query->whereHas('category', function ($q) use ($categoryName) {
                    $q->where('name', $categoryName);
                });
            }

            if ($request->has('search')) {
                $searchTerm = $request->input('search');
                $query->where('name', 'like', '%' . $searchTerm . '%');
            }

            if ($request->has('is_available')) {
                $isAvailable = filter_var($request->input('is_available'), FILTER_VALIDATE_BOOLEAN);
                $query->where('is_available', $isAvailable);
            }

            if ($request->has('sort_by_price')) {
                $sortDirection = $request->input('sort_by_price', 'asc');
                $query->orderBy('price', $sortDirection);
            }

            $foods = $query->paginate(10);

            return response()->json($foods, 200);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'error' => 'An error occurred while fetching food items.'
            ], 500);
        }
    }

    /**
     * @OA\Get(
     * path="/api/foods/most-sold",
     * summary="Get a paginated list of food items sorted by most sold (SuperAdmin only)",
     * tags={"Food"},
     * security={{"sanctum": {}}},
     * @OA\Parameter(
     * name="page",
     * in="query",
     * description="Page number for pagination",
     * required=false,
     * @OA\Schema(type="integer", default=1)
     * ),
     * @OA\Response(
     * response=200,
     * description="Successful operation",
     * @OA\JsonContent(ref="#/components/schemas/FoodWithCountPagination")
     * ),
     * @OA\Response(
     * response=403,
     * description="Forbidden",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=500,
     * description="Internal server error",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * )
     * )
     */
    public function indexMostSold(Request $request): JsonResponse
    {
        if (!Auth::user()->isSuperAdmin()) {
           return response()->json([
               'error' => 'You are not authorized to view these food items.'
           ]);
        }

        try {
            $foods = Food::withCount('orderItems')->orderByDesc('order_items_count')->paginate(10);
            return response()->json($foods, 200);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'error' => 'An error occurred while fetching food items.'
            ], 500);
        }
    }


    /**
     * @OA\Get(
     * path="/api/foods/{id}",
     * operationId="getFoodById",
     * tags={"Food"},
     * summary="Get food details by ID",
     * description="Retrieves the details of a specific food item by its ID. Accessible by any authenticated user.",
     * security={{"sanctum": {}}},
     * @OA\Parameter(
     * name="id",
     * in="path",
     * description="ID of the food item to retrieve",
     * required=true,
     * @OA\Schema(type="integer")
     * ),
     * @OA\Response(
     * response=200,
     * description="Successful operation",
     * @OA\JsonContent(ref="#/components/schemas/Food")
     * ),
     * @OA\Response(
     * response=401,
     * description="Unauthenticated",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=404,
     * description="Not Found: Food not found.",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=500,
     * description="Internal Server Error",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * )
     * )
     */
    public function show(string $food): JsonResponse
    {
        try {
            $foundFood = Food::find($food);

            if (!$foundFood) {
                return response()->json([
                    'error' => 'Food not found',
                ], 404);
            }

            return response()->json($foundFood, 200);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Post(
     * path="/api/foods",
     * operationId="createFood",
     * tags={"Food"},
     * summary="Create a new food item",
     * description="Creates a new food item. Requires super admin privileges.",
     * security={{"sanctum": {}}},
     * @OA\RequestBody(
     * required=true,
     * description="Food item creation data.",
     * @OA\MediaType(
     * mediaType="application/json",
     * @OA\Schema(
     * required={"category_id", "name", "price", "stock", "is_available"},
     * @OA\Property(property="category_id", type="integer", example=1),
     * @OA\Property(property="name", type="string", example="Chicken Biryani"),
     * @OA\Property(property="description", type="string", nullable=true, example="A classic Indian rice dish with spicy chicken."),
     * @OA\Property(property="price", type="number", format="float", example=15.99),
     * @OA\Property(property="stock", type="integer", example=100),
     * @OA\Property(property="is_available", type="boolean", example=true),
     * @OA\Property(property="image_url", type="string", nullable=true, description="URL of the food item's image.")
     * )
     * ),
     * @OA\MediaType(
     * mediaType="multipart/form-data",
     * @OA\Schema(
     * required={"category_id", "name", "price", "stock", "is_available"},
     * @OA\Property(property="category_id", type="integer", example=1),
     * @OA\Property(property="name", type="string", example="Chicken Biryani"),
     * @OA\Property(property="description", type="string", nullable=true, example="A classic Indian rice dish with spicy chicken."),
     * @OA\Property(property="price", type="number", format="float", example=15.99),
     * @OA\Property(property="stock", type="integer", example=100),
     * @OA\Property(property="is_available", type="boolean", example=true),
     * @OA\Property(property="image_url", type="string", format="binary", nullable=true, description="Image file for the food item.")
     * )
     * )
     * ),
     * @OA\Response(
     * response=201,
     * description="Food created successfully",
     * @OA\JsonContent(
     * @OA\Property(property="success", type="string", example="Food created successfully.")
     * )
     * ),
     * @OA\Response(
     * response=401,
     * description="Unauthenticated",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=403,
     * description="Forbidden: You are not authorized to create a food item.",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=422,
     * description="Validation Error: Invalid input.",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="The given data was invalid."),
     * @OA\Property(property="errors", type="object")
     * )
     * ),
     * @OA\Response(
     * response=500,
     * description="Internal Server Error",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * )
     * )
     */
    public function create(RegisterFoodRequest $request): JsonResponse
    {
        if (!Auth::user()->isSuperAdmin()) {
            return response()->json([
                'error' => 'You are not authorized to create a food',
            ], 403);
        }

        $validated = $request->validated();

        $this->imageHandler($request, $validated);

        try {
            Food::create($validated);

            return response()->json([
                'success' => 'Food created successfully',
            ], 201);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Patch(
     * path="/api/foods/{id}",
     * operationId="updateFood",
     * tags={"Food"},
     * summary="Update an existing food item",
     * description="Updates the details of an existing food item. Requires super admin privileges. Note: A POST request is used here with 'multipart/form-data' to handle both fields and file uploads.",
     * security={{"sanctum": {}}},
     * @OA\Parameter(
     * name="id",
     * in="path",
     * description="ID of the food item to update",
     * required=true,
     * @OA\Schema(type="integer")
     * ),
     * @OA\RequestBody(
     * required=true,
     * description="Food item data to update.",
     * @OA\MediaType(
     * mediaType="application/json",
     * @OA\Schema(
     * @OA\Property(property="category_id", type="integer", nullable=true, example=2),
     * @OA\Property(property="name", type="string", nullable=true, example="Spicy Chicken Biryani"),
     * @OA\Property(property="description", type="string", nullable=true, example="Updated description."),
     * @OA\Property(property="price", type="number", format="float", nullable=true, example=16.50),
     * @OA\Property(property="stock", type="integer", nullable=true, example=75),
     * @OA\Property(property="is_available", type="boolean", nullable=true, example=false),
     * @OA\Property(property="image_url", type="string", nullable=true, description="URL of the new image, or 'null' to remove.")
     * )
     * ),
     * @OA\MediaType(
     * mediaType="multipart/form-data",
     * @OA\Schema(
     * @OA\Property(property="category_id", type="integer", nullable=true, example=2),
     * @OA\Property(property="name", type="string", nullable=true, example="Spicy Chicken Biryani"),
     * @OA\Property(property="description", type="string", nullable=true, example="Updated description."),
     * @OA\Property(property="price", type="number", format="float", nullable=true, example=16.50),
     * @OA\Property(property="stock", type="integer", nullable=true, example=75),
     * @OA\Property(property="is_available", type="boolean", nullable=true, example=false),
     * @OA\Property(property="image_url", type="string", format="binary", nullable=true, description="New image file for the food item. Send 'null' to delete the existing image."),
     * @OA\Property(property="_method", type="string", default="PATCH", description="Method spoofing for form data.")
     * )
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="Food updated successfully",
     * @OA\JsonContent(
     * @OA\Property(property="success", type="string", example="Food updated successfully.")
     * )
     * ),
     * @OA\Response(
     * response=401,
     * description="Unauthenticated",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=403,
     * description="Forbidden: You are not authorized to update a food item.",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=404,
     * description="Not Found: Food not found.",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=422,
     * description="Validation Error: Invalid input.",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="The given data was invalid."),
     * @OA\Property(property="errors", type="object")
     * )
     * ),
     * @OA\Response(
     * response=500,
     * description="Internal Server Error",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * )
     * )
     */
    public function update(UpdateFoodRequest $request, string $food): JsonResponse
    {
        if (!Auth::user()->isSuperAdmin()) {
            return response()->json([
                'error' => 'You are not authorized to update this food',
            ], 403);
        }

        $validated = $request->validated();

        try {
            $foundFood = Food::find($food);

            if (!$foundFood) {
                return response()->json([
                    'error' => 'Food not found',
                ], 404);
            }

            $this->imageHandler($request, $validated, $foundFood);

            $foundFood->update($validated);

            return response()->json([
                'success' => 'Food updated successfully',
            ], 200);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Delete(
     * path="/api/foods/{id}",
     * operationId="deleteFood",
     * tags={"Food"},
     * summary="Delete a food item",
     * description="Deletes a food item by its ID. Requires super admin privileges.",
     * security={{"sanctum": {}}},
     * @OA\Parameter(
     * name="id",
     * in="path",
     * description="ID of the food item to delete",
     * required=true,
     * @OA\Schema(type="integer")
     * ),
     * @OA\Response(
     * response=200,
     * description="Food deleted successfully",
     * @OA\JsonContent(
     * @OA\Property(property="success", type="string", example="Food deleted successfully.")
     * )
     * ),
     * @OA\Response(
     * response=401,
     * description="Unauthenticated",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=403,
     * description="Forbidden: You are not authorized to delete a food item.",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=404,
     * description="Not Found: Food not found.",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=500,
     * description="Internal Server Error",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * )
     * )
     */
    public function destroy(string $food): JsonResponse
    {
        if (!Auth::user()->isSuperAdmin()) {
            return response()->json([
                'error' => 'You are not authorized to delete this food',
            ], 403);
        }

        try {
            $foundFood = Food::find($food);

            if (!$foundFood) {
                return response()->json([
                    'error' => 'Food not found',
                ], 404);
            }

            $this->deleteOldImage($foundFood->image_url);

            $foundFood->delete();

            return response()->json(null, 204);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}