<?php

namespace App\Models\Jsonwebtoken;

use Illuminate\Support\Facades\App;

use Illuminate\Support\Str;
use Firebase\JWT\JWT as PhpJwt;

class JWT
{
    private const ALGORITM = "HS512";

    public static function generateTokens(array $user, $secretKey, $refreshKey) {
        $issuedAt   = new \DateTimeImmutable();
        $expire     = $issuedAt->modify('+6 minutes')->getTimestamp();      // Add 60 seconds
        $serverName = env('APP_NAME');
        $serverUrl = env('APP_URL');

        $default_payload = array(
            "iss" => $serverName, // строка, содержащая имя или идентификатор эмитента. Может быть доменным именем и может использоваться для удаления токенов из других приложений.
            "aud" => $serverUrl,
            "iat" => $issuedAt->getTimestamp(), // метка времени выпуска токена.
            "nbf" => $issuedAt->getTimestamp(), // отметка времени, когда токен должен считаться действительным. Должно быть равно или больше iat.
            "exp" => $expire, // отметка времени, когда токен должен перестать быть действительным. Должно быть больше iatи nbf.
        );

        $payload = array_merge(
            $user,
            $default_payload
        );

        $access_token = self::generate($payload, $secretKey);

        $payload['expire'] = $issuedAt->modify('+600 minutes')->getTimestamp();
        $refresh_token = self::generate($payload, $refreshKey);
        return [
            'access_token' => $access_token,
            'refresh_token' => $refresh_token,
            // 'serverName' => $serverName
        ];
    }
    public static function decode($token) {

        list($header, $payload, $signature) = explode (".", $token);
        $json = base64_decode($payload);

        return json_decode($json);
    }
    public static function validate($token, $key) {
        return PhpJwt::decode($token, $key, [self::ALGORITM]);
    }
    public static function generate($payload, $key) {
        return PhpJwt::encode($payload, $key, self::ALGORITM);
    }
    public static function generateSecretKey() {
        return Str::random(40);;
    }
}
