<?php

namespace App\Http\Controllers;

use App\Models\Decision;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class DecisionController extends Controller
{

    /**
     * Display a listing of all decisions.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $admin = Auth::guard('admin')->user();
        $query = Decision::query();

        if ($admin->role === 'editor') {
            $query->whereHas('user', function ($q) use ($admin) {
                $q->where('creator_id', $admin->id);
            });
        }

        $decisions = $query->get()->map(function ($decision) {
            $duration = calculateDuration($decision->start_date, $decision->end_date);
            $decision->how_long = $duration;
            return $decision;
        });

        return response()->json($decisions);
    }

    /**
     * Show the form for creating a new decision.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Return a form for creating a new decision (optional if using API)
    }

    /**
     * Store a newly created decision in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'why' => 'required|string',
            'how_long' => 'required|array|min:2',
            'how_long.*' => 'date_format:Y-m-d\TH:i:s.u\Z',
            'how_much' => 'required|numeric',
            'currency' => 'required|string|max:3',
            'note' => 'nullable|string',
            'status' => 'required|string|in:pending,waiting_approval,approved,reject',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $how_long = $request->how_long;
        $start_date = !empty($how_long[0]) ? $how_long[0] : null;
        $end_date = !empty($how_long[1]) ? $how_long[1] : null;

        $decision = Decision::create([
            'user_id' => $request->user_id,
            'title' => $request->title,
            'why' => $request->why,
            'how_long' => json_encode($request->how_long),
            'how_much' => $request->how_much,
            'currency' => $request->currency,
            'note' => $request->note,
            'status' => $request->status,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'date' => now(),
        ]);

        $duration = calculateDuration($start_date, $end_date);
        $decision->how_long = $duration;

        return response()->json($decision, 201);
    }

    /**
     * Display the specified decision.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $decision = Decision::with('user')->findOrFail($id);
        $duration = calculateDuration($decision->start_date, $decision->end_date);
        $decision->how_long = $duration;

        return response()->json($decision);
    }

    /**
     * Show the form for editing the specified decision.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // Return a form for editing a decision (optional if using API)
    }

    /**
     * Update the specified decision in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $rules = [
            'title' => 'required|string|max:255',
            'why' => 'required|string',
            'how_long' => 'required|array|min:2',
            'how_long.*' => 'date_format:Y-m-d\TH:i:s.u\Z',
            'how_much' => 'required|numeric',
            'currency' => 'required|string|max:3',
            'note' => 'nullable|string',
            'status' => 'required|string|in:pending,waiting_approval,approved,reject',
            'approved_amount' => 'nullable|numeric',
            'feedback' => 'nullable|string',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $decision = Decision::findOrFail($id);

        $how_long = $request->how_long;
        $start_date = !empty($how_long[0]) ? $how_long[0] : null;
        $end_date = !empty($how_long[1]) ? $how_long[1] : null;

        $decision->update([
            'title' => $request->title,
            'why' => $request->why,
            'how_long' => json_encode($request->how_long),
            'how_much' => $request->how_much,
            'currency' => $request->currency,
            'note' => $request->note,
            'status' => $request->status,
            'approved_amount' => $request->approved_amount,
            'feedback' => $request->feedback,
            'start_date' => $start_date,
            'end_date' => $end_date,
        ]);

        $duration = calculateDuration($start_date, $end_date);
        $decision->how_long = $duration;

        return response()->json($decision);
    }

    /**
     * Remove the specified decision from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $decision = Decision::findOrFail($id);
        $decision->delete();

        return response()->json(['message' => 'Decision deleted successfully']);
    }

    /**
     * Display a listing of decisions with 'pending' status.
     *
     * @return \Illuminate\Http\Response
     */
    public function pending()
    {
        $admin = Auth::guard('admin')->user();
        $query = Decision::where('status', 'pending');

        if ($admin->role === 'editor') {
            $query->whereHas('user', function ($q) use ($admin) {
                $q->where('creator_id', $admin->id);
            });
        }

        $decisions = $query->get()->map(function ($decision) {
            $duration = calculateDuration($decision->start_date, $decision->end_date);
            $decision->how_long = $duration;
            return $decision;
        });

        return response()->json($decisions);
    }

    /**
     * Display a listing of decisions with 'waiting_approval' status.
     *
     * @return \Illuminate\Http\Response
     */
    public function waitingApproval()
    {
        $admin = Auth::guard('admin')->user();
        $query = Decision::where('status', 'waiting_approval');

        if ($admin->role === 'editor') {
            $query->whereHas('user', function ($q) use ($admin) {
                $q->where('creator_id', $admin->id);
            });
        }

        $decisions = $query->get()->map(function ($decision) {
            $duration = calculateDuration($decision->start_date, $decision->end_date);
            $decision->how_long = $duration;
            return $decision;
        });

        return response()->json($decisions);
    }

    /**
     * Display a listing of decisions with 'approved' status.
     *
     * @return \Illuminate\Http\Response
     */
    public function approved()
    {
        $admin = Auth::guard('admin')->user();
        $query = Decision::where('status', 'approved');

        if ($admin->role === 'editor') {
            $query->whereHas('user', function ($q) use ($admin) {
                $q->where('creator_id', $admin->id);
            });
        }

        $decisions = $query->get()->map(function ($decision) {
            $duration = calculateDuration($decision->start_date, $decision->end_date);
            $decision->how_long = $duration;
            return $decision;
        });

        return response()->json($decisions);
    }

    /**
     * Display a listing of decisions with 'reject' status.
     *
     * @return \Illuminate\Http\Response
     */
    public function reject()
    {
        $admin = Auth::guard('admin')->user();
        $query = Decision::where('status', 'reject');

        if ($admin->role === 'editor') {
            $query->whereHas('user', function ($q) use ($admin) {
                $q->where('creator_id', $admin->id);
            });
        }

        $decisions = $query->get()->map(function ($decision) {
            $duration = calculateDuration($decision->start_date, $decision->end_date);
            $decision->how_long = $duration;
            return $decision;
        });

        return response()->json($decisions);
    }

    /**
     * Display a listing of decisions by status.
     *
     * @param  string  $status
     * @return \Illuminate\Http\Response
     */
    public function byStatus($status)
    {
        $admin = Auth::guard('admin')->user();
        $query = Decision::where('status', $status);

        if ($admin->role === 'editor') {
            $query->whereHas('user', function ($q) use ($admin) {
                $q->where('creator_id', $admin->id);
            });
        }

        $decisions = $query->get()->map(function ($decision) {
            $duration = calculateDuration($decision->start_date, $decision->end_date);
            $decision->how_long = $duration;
            return $decision;
        });

        return response()->json($decisions);
    }
}
