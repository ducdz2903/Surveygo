<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Models\User;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $name = trim((string)$request->input('name'));
        $email = strtolower(trim((string)$request->input('email')));
        $password = (string)$request->input('password');
        $role = trim((string)$request->input('role'));

        if (!$name || !$email || !$password) {
            return $this->json([
                'error' => true,
                'message' => 'Missing required fields.',
            ], 422);
        }

        if (!$role) {
            $role = 'user';
        } elseif (!in_array($role, ['admin', 'moderator', 'user'], true)) {
            return $this->json([
                'error' => true,
                'message' => 'Invalid role specified.',
            ], 422);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->json([
                'error' => true,
                'message' => 'Invalid email format.',
            ], 422);
        }

        if (strlen($password) < 6) {
            return $this->json([
                'error' => true,
                'message' => 'Password must be at least 6 characters.',
            ], 422);
        }

        if (User::findByEmail($email)) {
            return $this->json([
                'error' => true,
                'message' => 'Email already registered.',
            ], 422);
        }

        $hashed = password_hash($password, PASSWORD_BCRYPT);

        $user = User::create($name, $email, $hashed, $role);

        return $this->json([
            'error' => false,
            'message' => 'Registration successful.',
            'data' => [
                'user' => $user->toArray(),
            ],
        ], 201);
    }

    public function login(Request $request)
    {
        $email = strtolower(trim((string)$request->input('email')));
        $password = (string)$request->input('password');

        if (!$email || !$password) {
            return $this->json([
                'error' => true,
                'message' => 'Email and password are required.',
            ], 422);
        }

        $user = User::findByEmail($email);

        if (!$user || !$user->verifyPassword($password)) {
            return $this->json([
                'error' => true,
                'message' => 'Invalid credentials.',
            ], 401);
        }

        return $this->json([
            'error' => false,
            'message' => 'Login successful.',
            'data' => [
                'user' => $user->toArray(),
            ],
        ]);
    }

    public function profile(Request $request)
    {
        return $this->json([
            'error' => true,
            'message' => 'Profile endpoint disabled (JWT removed).',
        ], 404);
    }
}
