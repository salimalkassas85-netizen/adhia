<?php

namespace App\Services;

use Illuminate\Support\Str;

class CodeGenerator
{
    public function unique(string $prefix, string $modelClass): string
    {
        do {
            $code = $prefix.'-'.now()->format('ymd').'-'.Str::upper(Str::random(6));
        } while ($modelClass::query()->where('code', $code)->exists());

        return $code;
    }
}
