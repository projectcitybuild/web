<?php

namespace App\Http\Controllers\Panel;

use Entities\Models\Eloquent\BanAppeal;
use Illuminate\Http\Request;

class BanAppealController
{
    public function index()
    {
        $banAppeals = BanAppeal::paginate(50);

        return view('admin.ban-appeal.index')->with([
            'banAppeals' => $banAppeals
        ]);
    }

    public function show(BanAppeal $banAppeal)
    {
        return view('admin.ban-appeal.show')->with([
            'banAppeal' => $banAppeal
        ]);
    }

    public function edit(BanAppeal $banAppeal)
    {
        //
    }

    public function update(Request $request, BanAppeal $banAppeal)
    {
        //
    }

    public function destroy(BanAppeal $banAppeal)
    {
        //
    }
}
