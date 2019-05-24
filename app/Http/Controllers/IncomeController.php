<?php

namespace App\Http\Controllers;

use App\Http\Resources\IncomeResource;
use App\Income;
use Illuminate\Http\Request;

class IncomeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return IncomeResourceCollection
     */
    public function index(Request $request)
    {
        // Get last 3 years income
        $lastThreeYears = date('Y-01-01', strtotime('-3 year'));
        return IncomeResource::collection(Income::where("user_id", $request->auth->id)
                ->whereDate('date', '>=', $lastThreeYears)
                ->orderBy('date', 'desc')->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return IncomeResource
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'amount' => 'required|numeric',
            'date'   => 'required|date|date_format:Y-m-d',
        ]);

        $income = Income::create([
            'user_id' => $request->auth->id,
            'amount'  => $request->amount,
            'comment' => $request->comment ? $request->comment : '',
            'date'    => $request->date,
        ]);

        return new IncomeResource($income);
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param $id
     * @return IncomeResource
     */
    public function show(Request $request, $id)
    {
        $income = Income::findOrFail($id);
        // Check if currently authenticated user is the owner of the Income
        if ($request->auth->id != $income->user_id) {
            return response()->json(['error' => 'You can only view your own Income.'], 403);
        }

        return new IncomeResource($income);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  $id
     * @return IncomeResource
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'amount' => 'numeric',
            'date'   => 'date|date_format:Y-m-d',
        ]);

        // Check if currently authenticated user is the owner of the Income
        $income = Income::findOrFail($id);
        if ($request->auth->id != $income->user_id) {
            return response()->json(['error' => 'You can only edit your own Income.'], 403);
        }

        if ($request->has("amount")) {
            $income->amount = $request->amount;
        }

        if ($request->has("comment")) {
            $income->comment = $request->comment;
        }

        if ($request->has("date")) {
            $income->date = $request->date;
        }

        $income->save();

        return new IncomeResource($income);
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
        // Check if currently authenticated user is the owner of the Income
        $income = Income::findOrFail($id);
        if ($request->auth->id != $income->user_id) {
            return response()->json(['error' => 'You can only delete your own Income.'], 403);
        }

        $income->delete();

        return response()->json(null, 204);
    }

    public function __construct()
    {
    }
}
