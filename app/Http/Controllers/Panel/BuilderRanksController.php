<?php

namespace App\Http\Controllers\Panel;

use App\Http\WebController;
use Domain\BuilderRankApplications\Entities\ApplicationStatus;
use Entities\Models\Eloquent\BuilderRankApplication;
use Entities\Models\Eloquent\Donation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BuilderRanksController extends WebController
{
    public function index(Request $request)
    {
        $applications = BuilderRankApplication::orderbyRaw("FIELD(status, ".ApplicationStatus::IN_PROGRESS->value.") DESC")
            ->orderBy('created_at', 'desc')
            ->paginate(100);

        return view('admin.builder-rank.index')->with(compact('applications'));
    }

    public function show(Donation $donation)
    {
        $donation->load('perks', 'perks.account');

        return view('admin.donation.show')->with(compact('donation'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Donation $donation)
    {
        return view('admin.donation.edit')->with(compact('donation'));
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

        return redirect(route('front.panel.donations.show', $donation->donation_id));
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
