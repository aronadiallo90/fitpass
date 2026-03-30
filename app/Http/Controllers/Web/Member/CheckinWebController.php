<?php

namespace App\Http\Controllers\Web\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CheckinWebController extends Controller
{
    public function __invoke(Request $request): View
    {
        $checkins = $request->user()
            ->checkins()
            ->with('gym')
            ->latest()
            ->paginate(20);

        return view('member.checkins', compact('checkins'));
    }
}
