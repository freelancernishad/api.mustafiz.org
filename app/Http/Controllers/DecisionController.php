<?php

namespace App\Http\Controllers;

use App\Models\Decision;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DecisionController extends Controller
{
    /**
     * Display a listing of all decisions.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Fetch all decisions
        $decisions = Decision::all();
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
        // Validate the request data
        $request->validate([
            'title' => 'required|string|max:255',
            'why' => 'required|string',
            'how_long' => 'required|string|max:255',
            'how_much' => 'required|numeric',
            'note' => 'nullable|string',
            'status' => 'required|string|in:pending,waiting_approval,approved,reject',
        ]);

        // Create a new decision
        $decision = new Decision([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'why' => $request->why,
            'how_long' => $request->how_long,
            'how_much' => $request->how_much,
            'note' => $request->note,
            'status' => $request->status,
        ]);

        $decision->save();

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
        // Fetch a single decision by its ID
        $decision = Decision::findOrFail($id);
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
        // Validate the request data
        $request->validate([
            'title' => 'required|string|max:255',
            'why' => 'required|string',
            'how_long' => 'required|string|max:255',
            'how_much' => 'required|numeric',
            'note' => 'nullable|string',
            'status' => 'required|string|in:pending,waiting_approval,approved,reject',
        ]);

        // Fetch the decision and update its details
        $decision = Decision::findOrFail($id);
        $decision->update($request->all());

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
        // Fetch the decision and delete it
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
        // Fetch all pending decisions
        $decisions = Decision::where('status', 'pending')->get();
        return response()->json($decisions);
    }

    /**
     * Display a listing of decisions with 'waiting_approval' status.
     *
     * @return \Illuminate\Http\Response
     */
    public function waitingApproval()
    {
        // Fetch all decisions waiting for approval
        $decisions = Decision::where('status', 'waiting_approval')->get();
        return response()->json($decisions);
    }

    /**
     * Display a listing of decisions with 'approved' status.
     *
     * @return \Illuminate\Http\Response
     */
    public function approved()
    {
        // Fetch all approved decisions
        $decisions = Decision::where('status', 'approved')->get();
        return response()->json($decisions);
    }

    /**
     * Display a listing of decisions with 'reject' status.
     *
     * @return \Illuminate\Http\Response
     */
    public function reject()
    {
        // Fetch all rejected decisions
        $decisions = Decision::where('status', 'reject')->get();
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
        // Validate status
        if (!in_array($status, ['pending', 'waiting_approval', 'approved', 'reject'])) {
            return response()->json(['error' => 'Invalid status'], 400);
        }

        // Fetch decisions by status
        $decisions = Decision::where('status', $status)->get();
        return response()->json($decisions);
    }


    public function updateStatus(Request $request, $id)
    {
        // Validate the input
        $request->validate([
            'status' => 'required|in:pending,waiting_approval,approved,reject',
        ]);

        // Find the decision by ID
        $decision = Decision::findOrFail($id);

        // Update the status
        $decision->status = $request->input('status');
        $decision->save();

        // Return a success response
        return response()->json([
            'message' => 'Decision status updated successfully.',
            'decision' => $decision
        ]);
    }




}
