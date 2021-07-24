<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Jsonwebtoken\JWT;

class Sessions extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'secretKey',
        'refreshKey',
        'refreshToken',
        'user_id',
        'verify_code',
        'verified_at',
        'is_activated'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'secretKey',
        'refreshKey',
    ];

    public function getUser() {
        return $this->belongsTo(User::class);
    }
    public static function checkLimit($user) {
        $session_count = self::where('user_id', $user->id)->count();
        if($user->session_max_count >= $session_count && $user->super_session_max_count >= $session_count) return true;
        return false;
    }
    public static function createSession($user){
        $id = $user->id;
        $identity = $user->identity;

        $secretKey = JWT::generateSecretKey();
        $refreshKey = JWT::generateSecretKey();

        // dd($id);
        $session = self::create([
            'secretKey' => $secretKey,
            'refreshKey' => $refreshKey,
            'user_id' => $id,
            'verified_at' => now(),
            'verify_code' => ''
        ]);

        $payload = [
            'user_id' => $id,
            'session_id' => $session->id
        ];
        $tokens = JWT::generateTokens($payload, $secretKey, $refreshKey);

        $session->refreshToken = $tokens['refresh_token'];
        $session->save();

        return array_merge([
            'is_activated' => false
        ], $tokens);
    }
}
