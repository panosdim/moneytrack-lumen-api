<?php

namespace App\Http\Controllers;

use App\Category;
use App\Http\Resources\CategoryResource;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return CategoryResourceCollection
     */
    public function index(Request $request)
    {
        return CategoryResource::collection(Category::where("user_id", $request->auth->id)->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return CategoryResource
     */
    public function store(Request $request)
    {
        if ($request->has("category")) {
            $exist = Category::where("category", $request->category)->first();
            if (!empty($exist)) {
                return response()->json(['error' => 'Category with same name already exist'], 422);
            }
            $category = Category::create([
                'user_id' => $request->auth->id,
                'category' => $request->category,
                'count' => 0,
            ]);

            return new CategoryResource($category);
        } else {
            return response()->json(['error' => 'Category name must be present'], 422);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param $id
     * @return CategoryResource
     */
    public function show(Request $request, $id)
    {
        $category = Category::findOrFail($id);
        // check if currently authenticated user is the owner of the leave
        if ($request->auth->id != $category->user_id) {
            return response()->json(['error' => 'You can only view your own categories.'], 403);
        }

        return new CategoryResource($category);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  $id
     * @return CategoryResource
     */
    public function update(Request $request, $id)
    {
        // check if currently authenticated user is the owner of the leave
        if ($request->user()->id != $leaf->user_id) {
            return response()->json(['error' => 'You can only edit your own leaves.'], 403);
        }

        if (isset($request->from)) {
            $from = DateTime::createFromFormat('Y-m-d', $request->from);
            if ($from) {
                $from->setTime(0, 0, 0);
                $leaf->from = $request->from;
            } else {
                return response()->json(['error' => 'From and Until dates must be in Y-m-d format'], 422);
            }
        } else {
            $from = DateTime::createFromFormat('Y-m-d', $leaf->from);
        }

        if (isset($request->until)) {
            $until = DateTime::createFromFormat('Y-m-d', $request->until);
            if ($until) {
                $until->setTime(0, 0, 0);
                $leaf->until = $request->until;
            } else {
                return response()->json(['error' => 'From and Until dates must be in Y-m-d format'], 422);
            }
        } else {
            $until = DateTime::createFromFormat('Y-m-d', $leaf->until);
        }

        if ($from > $until) {
            return response()->json(['error' => 'From can not be greater than Until date'], 422);
        }

        if (isset($request->from) || isset($request->until)) {
            $leaf->days = WorkingDays::calculateWorkingDays($from, $until);
        }

        $leaf->save();

        return new LeaveResource($leaf);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param Leave $leaf
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Request $request, Leave $leaf)
    {
        // check if currently authenticated user is the owner of the leave
        if ($request->user()->id != $leaf->user_id) {
            return response()->json(['error' => 'You can only delete your own leaves.'], 403);
        }

        $leaf->delete();

        return response()->json(null, 204);
    }

    public function __construct()
    {
    }
}
