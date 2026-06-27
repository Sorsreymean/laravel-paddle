<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Laravel\Paddle\Http\Controllers\WebhookController;
use Symfony\Component\HttpFoundation\Response;

class ManualPaddleWebhookController extends WebhookController
{
    public function __construct()
    {
    }

    public function __invoke(Request $request): Response
    {
        Log::info('Manual Paddle webhook received', [
            'event_type' => $request->input('event_type'),
            'event_id' => $request->input('event_id'),
        ]);

        return parent::__invoke($request);
    }
}
