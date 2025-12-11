
<!-- Activity Logs Section -->
<?php if (!empty($recentActivities)): ?>
<section class="activity-logs-section pb-5">
    <div class="container">
        <h2 class="section-title mb-4">Hoạt động gần đây</h2>
        <div class="card">
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <?php foreach ($recentActivities as $activity): ?>
                        <div class="list-group-item px-4 py-3 d-flex justify-content-between align-items-center">
                            <div class="activity-info flex-grow-1">
                                <h6 class="mb-1">
                                    <?php 
                                    $actionText = [
                                        'login' => '🔓 Đăng nhập',
                                        'logout' => '🔒 Đăng xuất',
                                        'survey_created' => '📋 Tạo khảo sát',
                                        'survey_updated' => '✏️ Cập nhật khảo sát',
                                        'survey_submitted' => '✅ Nộp khảo sát',
                                        'reward_redeemed' => '🎁 Đổi thưởng',
                                        'daily_reward_claimed' => '⭐ Nhận thưởng hàng ngày',
                                        'redemption_status_changed' => '🔄 Thay đổi trạng thái đổi quà',
                                        'profile_updated' => '👤 Cập nhật hồ sơ',
                                    ];
                                    echo $actionText[$activity['action']] ?? htmlspecialchars($activity['action']);
                                    ?>
                                </h6>
                                <p class="mb-0 text-muted small">
                                    <?php echo htmlspecialchars($activity['description'] ?? ''); ?>
                                </p>
                            </div>
                            <div class="activity-time text-end">
                                <small class="text-muted">
                                    <?php 
                                    $time = new DateTime($activity['created_at']);
                                    $now = new DateTime();
                                    $interval = $now->diff($time);
                                    
                                    if ($interval->d > 0) {
                                        echo $interval->d . ' ngày trước';
                                    } elseif ($interval->h > 0) {
                                        echo $interval->h . ' giờ trước';
                                    } elseif ($interval->i > 0) {
                                        echo $interval->i . ' phút trước';
                                    } else {
                                        echo 'Vừa xong';
                                    }
                                    ?>
                                </small>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>
