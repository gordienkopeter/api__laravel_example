<?php

namespace App\Services;

use App\Helpers\Contracts\TokenServiceContract;
use App\Helpers\Contracts\UserServiceContract;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

class UserService implements UserServiceContract
{
    protected $tokenService;

    public function __construct(TokenServiceContract $tokenService)
    {
        $this->tokenService = $tokenService;
    }

    public function userByLoginOrEmail(string $login)
    {
        return User::where('login', $login)
            ->orWhere('email', $login)
            ->first();
    }

    public function create(array $data): User
    {
        $salt = Str::random(33);

        return User::create([
            'login' => array_get($data, 'login'),
            'email' => array_get($data, 'email'),
            'first_name' => array_get($data, 'first_name'),
            'last_name' => array_get($data, 'last_name'),
            'password' => $this->preparePasswordBySalt($salt, array_get($data, 'password')),
            'salt' => Crypt::encryptString($salt)
        ]);
    }

    public function show(int $id)
    {
        return User::where('id', $id)
            ->first();
    }

    public function userByToken(string $token)
    {
        $model = $this->tokenService->find($token);

        return $model ? $model->user : null;
    }

    public function preparePassword(User $user, string $password): string
    {
        return sha1(Crypt::decryptString($user->salt) . sha1($password));
    }

    public function preparePasswordBySalt(string $salt, string $password): string
    {
        return sha1($salt . sha1($password));
    }
}