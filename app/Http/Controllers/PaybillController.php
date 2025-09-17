<?php

namespace App\Http\Controllers;

use App\Models\Paybill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class PaybillController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $paybills = Paybill::where('user_id', auth()->id())->get();
        return view('paybills.index', compact('paybills'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('paybills.form');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'paybill_number' => 'required|string|max:255',
            'consumer_key' => 'required|string',
            'consumer_secret' => 'required|string',
            'passkey' => 'required|string',
            'daily_limit' => 'required|integer|min:1|max:1000',
        ]);

        $validated['user_id'] = auth()->id();

        $paybillData = [
            'user_id' => auth()->id(),
            'name' => $validated['name'],
            'paybill_number' => $validated['paybill_number'],
            'consumer_key' => ($validated['consumer_key']),
            'consumer_secret' => ($validated['consumer_secret']),
            'passkey' => ($validated['passkey']),
            'daily_limit' => $validated['daily_limit'],
        ];


        // dd($validated);
        Paybill::create($paybillData);

        return redirect()->route('paybills.index')->with('success', 'Paybill created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Paybill $paybill)
    {
        $this->authorize('view', $paybill);
        return view('paybills.show', compact('paybill'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Paybill $paybill)
    {
        // dd($paybill);
        // $this->authorize('update', $paybill);
        return view('paybills.form', compact('paybill'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Paybill $paybill)
    {
        // $this->authorize('update', $paybill);



        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'paybill_number' => 'required|string|max:255',
            'consumer_key' => 'required|string',
            'consumer_secret' => 'required|string',
            'passkey' => 'required|string',
            'daily_limit' => 'required|integer|min:1|max:1000',
        ]);

        $paybill->update($validated);

        return redirect()->route('paybills.index')->with('success', 'Paybill updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Paybill $paybill)
    {
        $this->authorize('delete', $paybill);
        
        // Check if paybill is used in any campaigns
        if ($paybill->campaigns()->count() > 0) {
            return redirect()->back()->with('error', 'Cannot delete paybill that is used in campaigns.');
        }

        $paybill->delete();

        return redirect()->route('paybills.index')->with('success', 'Paybill deleted successfully.');
    }
}