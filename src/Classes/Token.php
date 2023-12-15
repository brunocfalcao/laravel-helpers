<?php

namespace Brunocfalcao\LaravelHelpers\Classes;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Token extends Model
{
    use SoftDeletes;

    protected $fillable = ['token'];

    public static function createToken()
    {
        return self::create(['token' => Str::random(40)]);
    }

    public static function isValid($token)
    {
        return self::where('token', $token)->whereNull('deleted_at')->exists();
    }

    public static function burn($token)
    {
        $tokenInstance = self::where('token', $token)->firstOrFail();
        $tokenInstance->delete();
    }
}
