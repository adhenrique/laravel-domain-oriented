<?php

namespace LaravelDomainOriented\Policy;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Contracts\Auth\Authenticatable as AuthUser;

class Policy
{
    use HandlesAuthorization;

    public function before(?AuthUser $user, string $ability): bool
    {
        return true;
    }

    public function index(AuthUser $user): bool
    {
        return true;
    }

    public function show(AuthUser $user): bool
    {
        return true;
    }

    public function store(AuthUser $user): bool
    {
        return true;
    }

    public function update(AuthUser $user): bool
    {
        return true;
    }

    public function destroy(AuthUser $user): bool
    {
        return true;
    }
}

