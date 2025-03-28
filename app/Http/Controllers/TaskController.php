<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use OpenApi\Annotations as OA;

class TaskController extends Controller
{
    protected function baseQuery()
    {
        return Task::where('user_id', Auth::id());
    }

    protected function getCacheKey($suffix = '')
    {
        $userId = Auth::id();
        $queryParams = request()->query();
        $paramsString = $queryParams ? md5(json_encode($queryParams)) : 'default';
        return "tasks:user:{$userId}:{$paramsString}:{$suffix}";
    }

    /**
     * @OA\Get(
     *     path="/api/tasks",
     *     tags={"Tasks"},
     *     summary="Get all tasks for the authenticated user",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search tasks by title or description",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="due_date_from",
     *         in="query",
     *         description="Filter tasks by due date from",
     *         required=false,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="due_date_to",
     *         in="query",
     *         description="Filter tasks by due date to",
     *         required=false,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="sort",
     *         in="query",
     *         description="Sort tasks by due date (asc or desc)",
     *         required=false,
     *         @OA\Schema(type="string", enum={"asc", "desc"})
     *     ),
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
        $sortDirection = request('sort', 'desc');
        $hasQueryParams = request()->hasAny(['search', 'due_date_from', 'due_date_to', 'sort']);

        if ($hasQueryParams) {
            $query = $this->baseQuery();
            $query->orderBy('due_date', $sortDirection);

            if (request()->has('search')) {
                $query->where(function ($query) {
                    $query->where('title', 'ILIKE', '%' . request('search') . '%')
                        ->orWhere('description', 'ILIKE', '%' . request('search') . '%');
                });
            }

            if (request()->has('due_date_from')) {
                $query->where('due_date', '>=', request('due_date_from'));
            }

            if (request()->has('due_date_to')) {
                $query->where('due_date', '<=', request('due_date_to'));
            }

            $tasks = $query
                ->select(['id', 'title', 'due_date', 'created_at'])
                ->paginate(10);

            return response()->json($tasks);
        }

        $cacheKey = $this->getCacheKey('index');
        $tasks = Cache::remember(
            $cacheKey,
            3600,
            function () use ($sortDirection) {
                return $this->baseQuery()
                    ->orderBy('due_date', $sortDirection)
                    ->select(['id', 'title', 'due_date', 'created_at'])
                    ->paginate(10);
            }
        );

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

        Cache::forget($this->getCacheKey('index'));

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
        $cacheKey = $this->getCacheKey("show:{$id}");

        $task = Cache::remember(
            $cacheKey,
            3600,
            function () use ($id) {
                return $this->baseQuery()->findOrFail($id);
            }
        );

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

        Cache::forget($this->getCacheKey('index'));
        Cache::forget($this->getCacheKey("show:{$id}"));

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

        Cache::forget($this->getCacheKey('index'));
        Cache::forget($this->getCacheKey("show:{$id}"));

        return response()->json(null, 204);
    }
}