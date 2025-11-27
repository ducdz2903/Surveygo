<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Container;

use App\Core\Controller;
use App\Core\Request;
use App\Models\User;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
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
    public function sendResetOtp(Request $request)
    {
        $identifier = strtolower(trim((string)$request->input('email_or_phone')));
        $user = User::findByEmail($identifier);
        if (!$user) {
            return $this->json(['error'=>true,'message'=>'Không tìm thấy tài khoản.'],404);
        }

        $otp = random_int(100000,999999);
        $expire = (new \DateTimeImmutable('+5 minutes'))->format('Y-m-d H:i:s');

        $db = Container::get('db');
        $stmt = $db->prepare("UPDATE users SET reset_otp=:otp, reset_expire=:exp WHERE id=:id");
        $stmt->execute([':otp'=>$otp,':exp'=>$expire,':id'=>$user->getId()]);

        $this->sendOtpEmail($user->getEmail(), $otp);

        return $this->json(['error'=>false,'message'=>'OTP đã được gửi.']);
    }

    public function verifyOtp(Request $request)
    {
        $identifier = strtolower(trim((string)$request->input('email_or_phone')));
        $otp = (string)$request->input('otp');
        $newPassword = (string)$request->input('new_password');

        $db = Container::get('db');
        $stmt = $db->prepare("SELECT * FROM users WHERE email=:id AND reset_otp=:otp LIMIT 1");
        $stmt->execute([':id'=>$identifier,':otp'=>$otp]);
        $userData = $stmt->fetch(\PDO::FETCH_ASSOC);

        if(!$userData || strtotime($userData['reset_expire'])<time()) {
            return $this->json(['error'=>true,'message'=>'OTP không hợp lệ hoặc hết hạn.'],400);
        }

        $hashed = password_hash($newPassword,PASSWORD_BCRYPT);
        $stmt = $db->prepare("UPDATE users SET password=:pw, reset_otp=NULL, reset_expire=NULL WHERE id=:id");
        $stmt->execute([':pw'=>$hashed,':id'=>$userData['id']]);

        return $this->json(['error'=>false,'message'=>'Đổi mật khẩu thành công.']);
    }
    private function sendOtpEmail(string $email,int $otp)
    {
        $mail = new PHPMailer(true);
        try{
            $mail->isSMTP();
            $mail->Host='smtp.gmail.com';
            $mail->SMTPAuth=true;
            $mail->Username='youremail@gmail.com';
            $mail->Password='app-password';
            $mail->SMTPSecure=PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port=465;
            $mail->setFrom('youremail@gmail.com','SurveyGo');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject='Mã OTP lấy lại mật khẩu';
            $mail->Body="Mã OTP của bạn là: <b>$otp</b>. Có hiệu lực 5 phút.";
            $mail->send();
        }catch(Exception $e){
            error_log("Mailer error: ".$mail->ErrorInfo);
        }
    }

}
