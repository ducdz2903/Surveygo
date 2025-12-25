<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;

class BankController extends Controller
{
    /**
     * Xác minh tài khoản ngân hàng thông qua service bên ngoài
     * Gọi đến purchase_url/capture với payload
     */
    public function verifyAccount(Request $request): Response
    {
        try {
            // Lấy dữ liệu từ request
            $data = $request->input();

            // Validate required fields
            if (empty($data['number'])) {
                return Response::json([
                    'error' => true,
                    'message' => 'Số tài khoản không được để trống'
                ], 400);
            }

            // Lấy purchase_url từ config
            $config = \App\Core\Container::get('config');

            // Kiểm tra purchase_url có được cấu hình hay không
            if (!isset($config['purchase_url']) || empty($config['purchase_url'])) {
                return Response::json([
                    'error' => true,
                    'message' => 'Purchase URL chưa được cấu hình. Vui lòng thêm "purchase_url" => "http://localhost:8000" vào config/app.php'
                ], 500);
            }

            $purchaseUrl = $config['purchase_url'];

            // Chuẩn bị payload
            $payload = [
                'url_id' => $data['url_id'] ?? null,
                'label_text' => $data['label_text'] ?? 'vcb',
                'number' => $data['number'],
                'headless' => $data['headless'] ?? true,
                'timeout_ms' => $data['timeout_ms'] ?? 30000
            ];

            // Gọi API bên ngoài
            $ch = curl_init();

            curl_setopt_array($ch, [
                CURLOPT_URL => rtrim($purchaseUrl, '/') . '/capture',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode($payload),
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                    'Accept: application/json'
                ],
                CURLOPT_TIMEOUT => 35, // Thêm 5s buffer so với timeout_ms
                CURLOPT_CONNECTTIMEOUT => 10
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);

            curl_close($ch);

            // Kiểm tra lỗi curl
            if ($curlError) {
                return Response::json([
                    'error' => true,
                    'message' => 'Lỗi kết nối đến service: ' . $curlError
                ], 500);
            }

            // Parse response
            $result = json_decode($response, true);

            // Kiểm tra HTTP code
            if ($httpCode !== 200) {
                return Response::json([
                    'error' => true,
                    'message' => $result['message'] ?? 'Lỗi từ service xác minh tài khoản',
                    'http_code' => $httpCode
                ], $httpCode);
            }

            // Trả về kết quả
            return Response::json([
                'success' => true,
                'accName' => $result['accName'] ?? null,
                'remaining_views' => $result['remaining_views'] ?? 0,
                'remaining_all' => $result['remaining_all'] ?? 0,
                'raw_response' => $result // Debug purpose
            ]);

        } catch (\Exception $e) {
            return Response::json([
                'error' => true,
                'message' => 'Lỗi xử lý: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Thực hiện chuyển khoản tự động qua service bên ngoài
     * Gọi đến purchase_url/submit với payload
     */
    public function submitTransfer(Request $request): Response
    {
        try {
            // Lấy dữ liệu từ request
            $data = $request->input();

            // Validate required fields
            if (empty($data['number'])) {
                return Response::json([
                    'error' => true,
                    'message' => 'Số tài khoản không được để trống'
                ], 400);
            }

            // Lấy purchase_url từ config
            $config = \App\Core\Container::get('config');

            // Kiểm tra purchase_url có được cấu hình hay không
            if (!isset($config['purchase_url']) || empty($config['purchase_url'])) {
                return Response::json([
                    'error' => true,
                    'message' => 'Purchase URL chưa được cấu hình. Vui lòng thêm "purchase_url" => "http://localhost:8000" vào config/app.php'
                ], 500);
            }

            $purchaseUrl = $config['purchase_url'];

            // Chuẩn bị payload
            $payload = [
                'url_id' => $data['url_id'] ?? null,
                'label_text' => $data['label_text'] ?? '',
                'number' => $data['number'],
                'headless' => $data['headless'] ?? true,
                'timeout_ms' => $data['timeout_ms'] ?? 30000
            ];

            // Gọi API bên ngoài
            $ch = curl_init();

            curl_setopt_array($ch, [
                CURLOPT_URL => rtrim($purchaseUrl, '/') . '/submit',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode($payload),
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                    'Accept: application/json'
                ],
                CURLOPT_TIMEOUT => 35, // Thêm 5s buffer so với timeout_ms
                CURLOPT_CONNECTTIMEOUT => 10
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);

            curl_close($ch);

            // Kiểm tra lỗi curl
            if ($curlError) {
                return Response::json([
                    'error' => true,
                    'message' => 'Lỗi kết nối đến service: ' . $curlError
                ], 500);
            }

            // Parse response
            $result = json_decode($response, true);

            // Kiểm tra HTTP code
            if ($httpCode !== 200) {
                return Response::json([
                    'error' => true,
                    'message' => $result['message'] ?? 'Lỗi từ service chuyển khoản',
                    'http_code' => $httpCode
                ], $httpCode);
            }

            // Trả về kết quả
            return Response::json([
                'success' => $result['success'] ?? false,
                'details' => $result['details'] ?? null,
                'reserved_id' => $result['reserved_id'] ?? null,
                'log_id' => $result['log_id'] ?? null,
                'raw_response' => $result // Debug purpose
            ]);

        } catch (\Exception $e) {
            return Response::json([
                'error' => true,
                'message' => 'Lỗi xử lý: ' . $e->getMessage()
            ], 500);
        }
    }
}
