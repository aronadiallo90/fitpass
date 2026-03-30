<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Gym;
use Illuminate\View\View;

class LandingController extends Controller
{
    public function __invoke(): View
    {
        $gyms = Gym::where('is_active', true)
            ->select(['id', 'name', 'address', 'latitude', 'longitude', 'activities'])
            ->get();

        return view('landing', [
            'gyms'      => $gyms,
            'gymsCount' => $gyms->count(),
        ]);
    }
}
