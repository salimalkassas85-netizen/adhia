<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class StatusLogService
{
    public function log(Model $model, ?string $from, string $to, ?string $note = null): void
    {
        $model->statusLogs()->create([
            'user_id' => Auth::id(),
            'from_status' => $from,
            'to_status' => $to,
            'note' => $note,
        ]);
    }
}
