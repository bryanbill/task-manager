<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use OpenApi\Annotations as OA;

class TaskController extends Controller
{
    // Cache duration in minutes
    private const CACHE_TTL = 60;

    /**
     * Get base query with user scope
     */
    protected function baseQuery()
    {
        return Task::where('user_id', Auth::id());
    }

    /**
     * @OA\Get(
     *     path="/api/tasks",
     *     tags={"Tasks"},
     *     summary="Get all tasks for the authenticated user",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of tasks",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Task")
     *         )
     *     )
     * )
     */
    public function index()
    {
        $cacheKey = 'user_' . Auth::id() . '_tasks_page_' . request('page', 1);

        $tasks = Cache::remember($cacheKey, self::CACHE_TTL, function () {
            return $this->baseQuery()
                ->select(['id', 'title', 'due_date', 'created_at'])
                ->orderBy('due_date', 'asc')
                ->paginate(10);
        });

        return response()->json($tasks);
    }

    /**
     * @OA\Post(
     *     path="/api/tasks",
     *     tags={"Tasks"},
     *     summary="Create a new task",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title"},
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="due_date", type="string", format="date")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Task created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Task")
     *     )
     * )
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'date|after_or_equal:today'
        ]);
        $task = $this->baseQuery()->create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'due_date' => $validated['due_date'],
            'user_id' => $user->id
        ]);

        $this->clearUserTasksCache();

        return response()->json([
            'message' => 'Task created successfully',
            'task' => $task->only(['id', 'title', 'due_date', 'created_at'])
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/tasks/{id}",
     *     tags={"Tasks"},
     *     summary="Get a specific task by ID",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Task details",
     *         @OA\JsonContent(ref="#/components/schemas/Task")
     *     )
     * )
     */
    public function show($id)
    {
        $cacheKey = 'task_' . $id;

        $task = Cache::remember($cacheKey, self::CACHE_TTL, function () use ($id) {
            return $this->baseQuery()->findOrFail($id);
        });

        return response()->json($task);
    }

    /**
     * @OA\Put(
     *     path="/api/tasks/{id}",
     *     tags={"Tasks"},
     *     summary="Update a specific task by ID",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="due_date", type="string", format="date")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Task updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Task")
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date|after_or_equal:today'
        ]);

        $task = $this->baseQuery()->findOrFail($id);
        $task->fill($validated);
        $task->save();

        Cache::forget('task_' . $id);
        $this->clearUserTasksCache();

        return response()->json($task->only(['id', 'title', 'due_date', 'description']));
    }

    /**
     * @OA\Delete(
     *     path="/api/tasks/{id}",
     *     tags={"Tasks"},
     *     summary="Delete a specific task by ID",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Task deleted successfully"
     *     )
     * )
     */
    public function destroy($id)
    {
        $task = $this->baseQuery()->findOrFail($id);
        $task->delete();

        Cache::forget('task_' . $id);
        $this->clearUserTasksCache();

        return response()->json(null, 204);
    }

    /**
     * Clear all tasks cache for the authenticated user
     */
    protected function clearUserTasksCache()
    {
        $prefix = 'user_' . Auth::id() . '_tasks_page_';
        Cache::forget($prefix . request('page', 1));

        $keys = Cache::getStore()->getPrefix() . $prefix . '*';
        Cache::forget($keys);
    }
}