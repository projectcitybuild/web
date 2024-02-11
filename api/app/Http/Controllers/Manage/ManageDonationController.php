<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use App\Http\Requests\Manage\ManageDonationRequest;
use App\Models\Eloquent\Donation;
use Illuminate\Http\JsonResponse;

class ManageDonationController extends Controller
{
    public function index(): JsonResponse
    {
        $donations = Donation::cursorPaginate(config('api.page_size'));

        return response()->json($donations);
    }

    public function store(ManageDonationRequest $request): JsonResponse
    {
        $donation = Donation::create($request->validated());

        return response()->json($donation);
    }

    public function show(string $id): JsonResponse
    {
        $donation = Donation::findOrFail($id);

        return response()->json($donation);
    }

    public function update(ManageDonationRequest $request, string $id): JsonResponse
    {
        $donation = Donation::findOrFail($id);
        $donation->update($request->validated());
        $donation->save();

        return response()->json($donation);
    }

    public function destroy(string $id): JsonResponse
    {
        $donation = Donation::findOrFail($id);
        $donation->delete();

        return response()->json();
    }
}
