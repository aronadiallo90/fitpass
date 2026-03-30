<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PaymentResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PaymentController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $payments = $request->user()
            ->payments()
            ->latest()
            ->paginate(15);

        return PaymentResource::collection($payments);
    }
}
