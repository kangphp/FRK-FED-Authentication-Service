<?php

namespace App\Models;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Auth\Authenticatable as AuthenticableTrait;
use Illuminate\Support\Facades\Http;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User implements Authenticatable, JWTSubject
{
    use AuthenticableTrait;

    protected $username;

    public function __construct($username)
    {
        $this->username = $username;
    }

    /**
     * Lakukan autentikasi pengguna melalui layanan eksternal.
     *
     * @param string $password
     * @return bool
     */
    public function authenticate($password)
    {
        try {
            // Kirim permintaan ke layanan eksternal untuk otentikasi
            $response = Http::asForm()->post('https://cis-dev.del.ac.id/api/jwt-api/do-auth', [
                'username' => $this->username,
                'password' => $password
            ])->body();

//            print_r($response);
            $data = json_decode($response, true);

            if ($data['success'] == false) {
                return false;
            } else {
                $this->setTokenData($data['token']);

                $this->setData($data['user']);

                return true;
            }
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Mendapatkan identitas pengguna.
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return $this->username;
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getAuthIdentifier();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function setTokenData($token)
    {
        $this->tokenData = $token;
    }

    public function getTokenData()
    {
        return $this->tokenData;
    }

    public function setData($data)
    {
        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
    }
}
