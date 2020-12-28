<?php

namespace App\Http\Controllers\Panel;

use App\Entities\Donations\Models\Donation;
use App\Http\WebController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DonationController extends WebController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $donations = Donation::with('account')->orderBy('created_at', 'desc')->paginate(100);

        return view('front.pages.panel.donations.index')->with(compact('donations'));
    }

    /**
     * Show the form for creating the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        return view('front.pages.panel.donations.create');
    }

    /**
     * Add a specified resource in storage.
     *
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|gt:0',
            'account_id' => 'nullable|numeric|exists:accounts,account_id',
            'created_at' => 'required|date',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Donation::create([
            'amount' => $request->get('amount'),
            'account_id' => $request->get('account_id'),
            'created_at' => $request->get('created_at'),
            'updated_at' => $request->get('created_at'),
        ]);

        return redirect(route('front.panel.donations.index'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Donation $donation)
    {
        return view('front.pages.panel.donations.edit')->with(compact('donation'));
    }

    /**
     * Update the specified resource in storage.
     *
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Donation $donation)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|gt:0',
            'account_id' => 'nullable|numeric|exists:accounts,account_id',
            'created_at' => 'required|date',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $donation->update($request->all());
        $donation->save();

        return redirect(route('front.panel.donations.index'));
    }

    /**
     * Delete the specified resource in storage.
     *
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Donation $donation)
    {
        $donation->delete();

        return redirect(route('front.panel.donations.index'));
    }
}
