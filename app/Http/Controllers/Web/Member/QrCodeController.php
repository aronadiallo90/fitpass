<?php

namespace App\Http\Controllers\Web\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrCodeController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user()->load('activeSubscription.plan');

        if (!$user->qr_token) {
            $user->qr_token = (string) Str::uuid();
            $user->save();
        }

        $activeSubscription = $user->activeSubscription;

        $qrCode = QrCode::size(280)
            ->backgroundColor(255, 255, 255)
            ->color(10, 10, 15)
            ->generate($user->qr_token);

        return view('member.qrcode', compact('activeSubscription', 'qrCode'));
    }
}
