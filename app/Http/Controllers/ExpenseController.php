<?php

namespace App\Http\Controllers;

use App\Category;
use App\Expense;
use App\Http\Resources\ExpenseResource;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return ExpenseResourceCollection
     */
    public function index(Request $request)
    {
        // Get last 3 years expenses
        $lastThreeYears = date('Y-01-01', strtotime('-3 year'));
        return ExpenseResource::collection(Expense::where('user_id', $request->auth->id)
                ->whereDate('date', '>=', $lastThreeYears)
                ->orderBy('date', 'desc')->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return ExpenseResource
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'amount'   => 'required|numeric',
            'category' => 'required|numeric|exists:categories,id',
            'date'     => 'required|date|date_format:Y-m-d',
        ]);

        // Check if Category belong to user
        $category = Category::where('id', $request->category)->first();
        if ($request->auth->id != $category->user_id) {
            return response()->json(['error' => 'Category belong to another user.'], 403);
        }

        $expense = Expense::create([
            'user_id'  => $request->auth->id,
            'amount'   => $request->amount,
            'category' => $request->category,
            'comment'  => $request->comment ? $request->comment : '',
            'date'     => $request->date,
        ]);

        // Increase the count in category
        $category->count = $category->count + 1;
        $category->save();

        return new ExpenseResource($expense);
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param $id
     * @return ExpenseResource
     */
    public function show(Request $request, $id)
    {
        $expense = Expense::findOrFail($id);
        // Check if currently authenticated user is the owner of the Expense
        if ($request->auth->id != $expense->user_id) {
            return response()->json(['error' => 'You can only view your own Expense.'], 403);
        }

        return new ExpenseResource($expense);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  $id
     * @return ExpenseResource
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'amount'   => 'numeric',
            'category' => 'numeric|exists:categories,id',
            'date'     => 'date|date_format:Y-m-d',
        ]);

        // Check if currently authenticated user is the owner of the Expense
        $expense = Expense::findOrFail($id);
        if ($request->auth->id != $expense->user_id) {
            return response()->json(['error' => 'You can only edit your own Expense.'], 403);
        }

        if ($request->has("amount")) {
            $expense->amount = $request->amount;
        }

        if ($request->has("comment")) {
            $expense->comment = $request->comment;
        }

        if ($request->has("date")) {
            $expense->date = $request->date;
        }

        if ($request->has("category")) {
            // Check if Category belong to user
            $category = Category::where("id", $request->category)->first();
            if ($request->auth->id != $category->user_id) {
                return response()->json(['error' => 'Category belong to another user.'], 403);
            }
            $expense->category = $request->category;
        }

        $expense->save();

        return new ExpenseResource($expense);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        // Check if currently authenticated user is the owner of the Expense
        $expense = Expense::findOrFail($id);
        if ($request->auth->id != $expense->user_id) {
            return response()->json(['error' => 'You can only delete your own Expense.'], 403);
        }

        $expense->delete();

        return response()->json(null, 204);
    }

    public function __construct()
    {
    }
}
