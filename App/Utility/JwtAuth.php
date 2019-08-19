<?php
/**
 * Created by PhpStorm.
 * User: wysmacmini02
 * Date: 2019/7/24
 * Time: 14:30
 */
namespace App\Utility;

class JwtAuth
{
    //三部分：header.payload.signature
    public static function encode(array $payload, string $key, string $alg = 'SHA256')
    {
        $key = md5($key);
        $jwt = self::urlsafeB64Encode(json_encode(['typ' => 'JWT', 'alg' => $alg])) . '.' . self::urlsafeB64Encode(json_encode($payload));
        return $jwt . '.' . self::signature($jwt, $key, $alg);
    }


    public static function decode(string $jwt, string $key)
    {
        $tokens = explode('.', $jwt);
        $key    = md5($key);

        if (count($tokens) != 3)
            return false;

        list($header64, $payload64, $sign) = $tokens;

        $header = json_decode(self::urlsafeB64Decode($header64), JSON_OBJECT_AS_ARRAY);
        if (empty($header['alg']))
            return false;

        if (self::signature($header64 . '.' . $payload64, $key, $header['alg']) !== $sign)
            return false;

        $payload = json_decode(self::urlsafeB64Decode($payload64), JSON_OBJECT_AS_ARRAY);

        $time = $_SERVER['REQUEST_TIME'];
        if (isset($payload['iat']) && $payload['iat'] > $time)
            return false;

        if (isset($payload['exp']) && $payload['exp'] < $time)
            return false;

        return $payload;
    }

    public static function signature(string $input, string $key, string $alg)
    {
        return hash_hmac($alg, $input, $key);
    }
    public static function urlsafeB64Decode(string $input)
    {
        $remainder = strlen($input) % 4;

        if ($remainder)
        {
            $padlen = 4 - $remainder;
            $input .= str_repeat('=', $padlen);
        }

        return base64_decode(strtr($input, '-_', '+/'));
    }
    public static function urlsafeB64Encode(string $input)
    {
        return str_replace('=', '', strtr(base64_encode($input), '+/', '-_'));
    }

}