<?php

namespace App\Validators;

use App\Services\UserService;

class AuthValidator
{
    /**
     * @var UserService
     */
    protected $usersService;

    public function __construct(UserService $usersService)
    {
        $this->usersService = $usersService;
    }

    public function authorizeLoginRule($attribute, $value): bool
    {
        return !!$this->usersService->userByLoginOrEmail($value);
    }

    public function authorizeLoginMessage(): string
    {
        return 'The selected login is invalid.';
    }

    public function authorizePasswordRule($attribute, $value, $parameters, $validator): bool
    {
        $user = $this->usersService->userByLoginOrEmail($validator->getData()['login']);

        if (!$user) {
            return true;
        }

        return $this->usersService->preparePassword($user, $value) === $user->password;
    }

    public function authorizePasswordMessage(): string
    {
        return 'Invalid credentials.';
    }
}