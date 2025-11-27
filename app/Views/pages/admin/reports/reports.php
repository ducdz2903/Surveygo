<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Báo cáo & Thống kê</h4>
            <p class="text-muted mb-0">Tổng hợp số liệu hoạt động của hệ thống</p>
        </div>
        <div class="btn-group">
            <button class="btn btn-outline-primary" onclick="alert('Đang xuất PDF...')">
                <i class="fas fa-file-pdf me-2"></i>Xuất PDF
            </button>
            <button class="btn btn-outline-success" onclick="alert('Đang xuất Excel...')">
                <i class="fas fa-file-excel me-2"></i>Xuất Excel
            </button>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card h-100 fade-in">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="fas fa-trophy me-2 text-warning"></i>Top khảo sát hiệu quả</h5>
                </div>
                <div class="card-body">
                    <div id="top-surveys-list" class="list-group list-group-flush">
                        <div class="text-center py-4"><div class="spinner-border text-primary"></div></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card h-100 fade-in" style="animation-delay: 0.1s">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="fas fa-users me-2 text-primary"></i>Người dùng tích cực</h5>
                </div>
                <div class="card-body">
                    <div id="top-users-list" class="list-group list-group-flush">
                         <div class="text-center py-4"><div class="spinner-border text-primary"></div></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-6">
            <div class="card h-100 fade-in" style="animation-delay: 0.2s">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="fas fa-chart-pie me-2 text-success"></i>Phân bố theo danh mục</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="height: 300px; position: relative;">
                        <canvas id="categoryChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card h-100 fade-in" style="animation-delay: 0.3s">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="fas fa-chart-line me-2 text-info"></i>Xu hướng tăng trưởng</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="height: 300px; position: relative;">
                        <canvas id="growthChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const reportData = {
            topSurveys: [
                { title: "Khảo sát thói quen đọc sách 2024", responses: 1250, rating: 4.8 },
                { title: "Đánh giá chất lượng dịch vụ Canteen", responses: 980, rating: 4.2 },
                { title: "Ý kiến về đồng phục mới", responses: 850, rating: 3.9 },
                { title: "Khảo sát nhu cầu học kỹ năng mềm", responses: 720, rating: 4.5 },
                { title: "Bình chọn sự kiện cuối năm", responses: 690, rating: 4.7 }
            ],
            topUsers: [
                { name: "Nguyễn Văn An", surveys: 15, responses: 120 },
                { name: "Trần Thị Bình", surveys: 12, responses: 98 },
                { name: "Lê Hoàng Cường", surveys: 10, responses: 85 },
                { name: "Phạm Minh Dung", surveys: 8, responses: 72 },
                { name: "Hoàng Văn Em", surveys: 8, responses: 65 }
            ],
            categories: {
                labels: ['Giáo dục', 'Công nghệ', 'Đời sống', 'Sức khỏe', 'Giải trí', 'Khác'],
                data: [35, 25, 20, 15, 10, 5]
            },
            growth: {
                labels: ['T1', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'T8', 'T9', 'T10', 'T11', 'T12'],
                users: [50, 65, 80, 120, 150, 190, 220, 250, 300, 320, 350, 400],
                responses: [200, 250, 300, 450, 500, 600, 750, 800, 950, 1100, 1200, 1500]
            }
        };
        function getAvatarColor(name) {
            const colors = ['#3498db', '#e74c3c', '#2ecc71', '#f1c40f', '#9b59b6', '#1abc9c'];
            let hash = 0;
            for (let i = 0; i < name.length; i++) hash = name.charCodeAt(i) + ((hash << 5) - hash);
            return colors[Math.abs(hash) % colors.length];
        }

        // hàm tạo giao diện top khảo sát
        const surveysList = document.getElementById('top-surveys-list');
        if(surveysList) {
            surveysList.innerHTML = reportData.topSurveys.map((s, i) => `
                <div class="list-group-item border-0 px-0 py-3 d-flex align-items-center">
                    <div class="fw-bold fs-4 text-muted opacity-50 me-3" style="width: 25px;">${i + 1}</div>
                    <div class="flex-grow-1">
                        <div class="fw-bold text-dark mb-1 text-truncate" style="max-width: 300px;">${s.title}</div>
                        <div class="small text-muted">
                            <i class="fas fa-poll me-1"></i>${s.responses.toLocaleString()} phản hồi
                            <span class="mx-2">•</span>
                            <span class="text-warning"><i class="fas fa-star me-1"></i>${s.rating}</span>
                        </div>
                    </div>
                    <div>
                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">Top ${i+1}</span>
                    </div>
                </div>
            `).join('');
        }

        // hàm tạo giao diện top người dùng
        const usersList = document.getElementById('top-users-list');
        if(usersList) {
            usersList.innerHTML = reportData.topUsers.map((u, i) => `
                <div class="list-group-item border-0 px-0 py-3 d-flex align-items-center">
                    <div class="fw-bold fs-4 text-muted opacity-50 me-3" style="width: 25px;">${i + 1}</div>
                    <div class="me-3">
                        <div class="rounded-circle text-white d-flex align-items-center justify-content-center fw-bold shadow-sm" 
                             style="width: 40px; height: 40px; background: ${getAvatarColor(u.name)}">
                            ${u.name.split(' ').pop()[0]}
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-bold text-dark">${u.name}</div>
                        <div class="small text-muted">Tham gia ${u.responses} lần</div>
                    </div>
                    <div class="text-end">
                        <div class="fw-bold text-primary">${u.surveys}</div>
                        <div class="small text-muted" style="font-size: 0.7rem;">Khảo sát</div>
                    </div>
                </div>
            `).join('');
        }

        // hàm tạo biểu đồ danh mục
        const catCtx = document.getElementById('categoryChart');
        if (catCtx) {
            new Chart(catCtx, {
                type: 'doughnut', 
                data: {
                    labels: reportData.categories.labels,
                    datasets: [{
                        data: reportData.categories.data,
                        backgroundColor: [
                            '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796'
                        ],
                        hoverOffset: 4,
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'right' }
                    }
                }
            });
        }

        // hàm tạo biểu đồ tăng trưởng
        const growthCtx = document.getElementById('growthChart');
        if (growthCtx) {
            new Chart(growthCtx, {
                type: 'line',
                data: {
                    labels: reportData.growth.labels,
                    datasets: [
                        {
                            label: 'Người dùng mới',
                            data: reportData.growth.users,
                            borderColor: '#4e73df',
                            backgroundColor: 'rgba(78, 115, 223, 0.05)',
                            tension: 0.3,
                            fill: true
                        },
                        {
                            label: 'Lượt phản hồi',
                            data: reportData.growth.responses,
                            borderColor: '#1cc88a',
                            backgroundColor: 'rgba(28, 200, 138, 0.05)',
                            tension: 0.3,
                            fill: true
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    plugins: {
                        legend: { position: 'top' }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { borderDash: [2, 4], color: "#eaecf4" }
                        },
                        x: {
                            grid: { display: false }
                        }
                    }
                }
            });
        }
    });
</script>