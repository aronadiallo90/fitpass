<?php

namespace App\Http\Controllers\Web\Member;

use App\Http\Controllers\Controller;
use App\Models\Gym;
use Illuminate\View\View;

class MapController extends Controller
{
    public function __invoke(): View
    {
        $gyms = Gym::active()
            ->get(['id', 'name', 'address', 'activities', 'phone', 'latitude', 'longitude']);

        return view('member.map', compact('gyms'));
    }
}
