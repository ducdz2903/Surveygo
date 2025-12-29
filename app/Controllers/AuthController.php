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
        $inviteCode = trim((string) $request->input('invite_code')); // Manual input
        $inviteToken = trim((string) $request->input('invite_token')); // From URL token

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

        // kiểm tra lời mời có hợp lệ không? -> tạo token giấu code mời đi.
        $inviterInvite = null;
        if ($inviteToken) {
            // tìm theo token trước
            $inviterInvite = \App\Models\UserInvite::findByToken($inviteToken);
            if (!$inviterInvite) {
                return $this->json([
                    'error' => true,
                    'message' => 'Link mời không hợp lệ.',
                ], 422);
            }
        } elseif ($inviteCode) {
            // trả về code mời vừa nhập (manual input)
            $inviterInvite = \App\Models\UserInvite::findByInviteCode($inviteCode);
            if (!$inviterInvite) {
                return $this->json([
                    'error' => true,
                    'message' => 'Mã mời không hợp lệ.',
                ], 422);
            }
        }

        $hashed = password_hash($password, PASSWORD_BCRYPT);

        // Tạo user với invited_by reference
        $db = Container::get('db');
        $now = (new \DateTimeImmutable())->format('Y-m-d H:i:s');
        
        $stmt = $db->prepare(
            'INSERT INTO users (name, avatar, email, phone, password, gender, role, code, invited_by, created_at, updated_at) 
             VALUES (:name, :avatar, :email, :phone, :password, :gender, :role, :code, :invited_by, :created_at, :updated_at)'
        );
        
        $stmt->execute([
            ':name' => $name,
            ':avatar' => '',
            ':email' => $email,
            ':phone' => null,
            ':password' => $hashed,
            ':gender' => 'other',
            ':role' => $role,
            ':code' => '',
            ':invited_by' => $inviterInvite ? $inviterInvite->getUserId() : null,
            ':created_at' => $now,
            ':updated_at' => $now,
        ]);

        $newUserId = (int) $db->lastInsertId();
        
        // Cập nhật user code
        if ($newUserId > 0) {
            $code = 'US' . str_pad((string) $newUserId, 3, '0', STR_PAD_LEFT);
            $upd = $db->prepare('UPDATE users SET code = :code WHERE id = :id');
            $upd->execute([':code' => $code, ':id' => $newUserId]);
        }

        $user = User::findById($newUserId);

        // Bắt đầu xử lý thưởng giới thiệu nếu có mã mời
    if ($inviterInvite && $user) {
        try {
            $rewardAmount = 500; // 500 points for both parties
            
            // Create invite record for new user (generate their own invite code)
            \App\Models\UserInvite::create($newUserId);
            
            // Award points to new user using PointTransaction
            \App\Models\PointTransaction::addPoints(
                $newUserId, 
                $rewardAmount, 
                'referral_bonus', 
                null, 
                'Thưởng đăng ký qua mã mời'
            );
            
            // Award points to inviter using PointTransaction
            \App\Models\PointTransaction::addPoints(
                $inviterInvite->getUserId(), 
                $rewardAmount, 
                'referral_bonus', 
                null, 
                'Thưởng giới thiệu thành công'
            );
            
            // Update inviter's referral stats
            $inviterInvite->incrementInviteCount();
            $inviterInvite->addRewards($rewardAmount);
            
            // Log activity for inviter only
            $inviterUser = User::findById($inviterInvite->getUserId());
            if ($inviterUser) {
                // Log for inviter: "Bạn đã mời {new user name}, cộng 500 điểm"
                ActivityLogHelper::logReferralInviter(
                    $inviterInvite->getUserId(), 
                    $newUserId, 
                    $user->getName(), 
                    $rewardAmount
                );
                
                // Log for invitee (new user): "Bạn đã đăng ký thông qua mã mời của {inviter name}"
                ActivityLogHelper::logReferralInvitee(
                    $newUserId,
                    $inviterInvite->getUserId(),
                    $inviterUser->getName(),
                    $rewardAmount
                );
            }
            
        } catch (\Exception $e) {
            // Log error but don't fail registration
            error_log('Referral reward error: ' . $e->getMessage());
            error_log('Stack trace: ' . $e->getTraceAsString());
            error_log('Error code: ' . $e->getCode());
        }
    } else {
        // Create invite record for new user even if no invite code was used
        try {
            \App\Models\UserInvite::create($newUserId);
        } catch (\Exception $e) {
            error_log('Failed to create invite record: ' . $e->getMessage());
        }
    }

        return $this->json([
            'error' => false,
            'message' => 'Registration successful.',
            'data' => [
                'user' => $user->toArray(),
                'referral_bonus' => $inviterInvite ? $rewardAmount : 0,
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
