<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Container;

use App\Core\Controller;
use App\Core\Request;
use App\Models\User;
use App\Helpers\ActivityLogHelper;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $name = trim((string) $request->input('name'));
        $email = strtolower(trim((string) $request->input('email')));
        $password = (string) $request->input('password');
        $role = trim((string) $request->input('role'));

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

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => $hashed,
            'role' => $role,
            'avatar' => '',
            'phone' => null,
            'gender' => 'other',
        ]);

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
        $email = strtolower(trim((string) $request->input('email')));
        $password = (string) $request->input('password');

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

        // Store user info in session
        $_SESSION['user_id'] = $user->getId();
        $_SESSION['user_email'] = $user->getEmail();
        $_SESSION['user_role'] = $user->getRole();
        $_SESSION['user_name'] = $user->getName();

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

    public function change_password(Request $request)
    {
        $email = strtolower(trim((string) $request->input('email')));
        $oldPassword = (string) $request->input('old_password');
        $newPassword = (string) $request->input('new_password');

        if (!$email || !$oldPassword || !$newPassword) {
            return $this->json([
                'error' => true,
                'message' => 'Email, old password, and new password are required.',
            ], 422);
        }

        $user = User::findByEmail($email);

        if (!$user || !$user->verifyPassword($oldPassword)) {
            return $this->json([
                'error' => true,
                'message' => 'Invalid credentials.',
            ], 401);
        }

        if (strlen($newPassword) < 6) {
            return $this->json([
                'error' => true,
                'message' => 'New password must be at least 6 characters.',
            ], 422);
        }

        $hashed = password_hash($newPassword, PASSWORD_BCRYPT);
        $user->updatePassword($hashed);

        return $this->json([
            'error' => false,
            'message' => 'Password changed successfully.',
        ]);
    }
    public function updateProfile(Request $request)
    {
        $id = (int) $request->input('id');
        if (!$id) {
            return $this->json(['error' => true, 'message' => 'Missing user id.'], 422);
        }

        $user = User::findById($id);
        if (!$user) {
            return $this->json(['error' => true, 'message' => 'User not found.'], 404);
        }

        $name = trim((string) $request->input('name')) ?: $user->getName();
        $email = strtolower(trim((string) $request->input('email')));
        $phone = $request->input('phone') ?: $user->getPhone();
        $gender = $request->input('gender') ?: $user->getGender();
        $avatar = $request->input('avatar') ?: $user->getAvatar();

        if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->json(['error' => true, 'message' => 'Invalid email format.'], 422);
        }

        // If email changed, ensure it's not used by another user
        if ($email && $email !== $user->getEmail()) {
            $existing = User::findByEmail($email);
            if ($existing && $existing->getId() !== $user->getId()) {
                return $this->json(['error' => true, 'message' => 'Email already in use.'], 422);
            }
        } else {
            $email = $user->getEmail();
        }

        /** @var \PDO $db */
        $db = Container::get('db');
        $now = (new \DateTimeImmutable())->format('Y-m-d H:i:s');

        $stmt = $db->prepare('UPDATE users SET name = :name, email = :email, phone = :phone, gender = :gender, avatar = :avatar, updated_at = :updated_at WHERE id = :id');
        $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':phone' => $phone,
            ':gender' => $gender,
            ':avatar' => $avatar,
            ':updated_at' => $now,
            ':id' => $id,
        ]);

        $updated = User::findById($id);

        return $this->json(['error' => false, 'message' => 'Profile updated.', 'data' => ['user' => $updated ? $updated->toArray() : null]]);
    }

    public function changePassword(Request $request)
    {
        $id = (int) $request->input('id');
        $current = (string) $request->input('current_password');
        $new = (string) $request->input('new_password');
        $confirm = (string) $request->input('confirm_password');

        if (!$id || !$current || !$new || !$confirm) {
            return $this->json(['error' => true, 'message' => 'Missing required fields.'], 422);
        }

        if ($new !== $confirm) {
            return $this->json(['error' => true, 'message' => 'New password and confirmation do not match.'], 422);
        }

        if (strlen($new) < 6) {
            return $this->json(['error' => true, 'message' => 'New password must be at least 6 characters.'], 422);
        }

        $user = User::findById($id);
        if (!$user) {
            return $this->json(['error' => true, 'message' => 'User not found.'], 404);
        }

        if (!$user->verifyPassword($current)) {
            return $this->json(['error' => true, 'message' => 'Current password is incorrect.'], 401);
        }

        $hashed = password_hash($new, PASSWORD_BCRYPT);
        $db = Container::get('db');
        $now = (new \DateTimeImmutable())->format('Y-m-d H:i:s');
        $stmt = $db->prepare('UPDATE users SET password = :password, updated_at = :updated_at WHERE id = :id');
        $stmt->execute([':password' => $hashed, ':updated_at' => $now, ':id' => $id]);

        return $this->json(['error' => false, 'message' => 'Password changed.']);
    }
}
