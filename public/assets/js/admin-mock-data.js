// Function to generate more mock data
function generateSurveys(count) {
    const surveys = [];
    const titles = [
        'Khảo sát về thói quen đọc sách', 'Khảo sát về sức khỏe cộng đồng', 'Khảo sát về trang web thương mại điện tử',
        'Khảo sát về ứng dụng di động', 'Khảo sát về dịch vụ khách hàng', 'Khảo sát về nhu cầu giáo dục trực tuyến',
        'Khảo sát về môi trường làm việc', 'Khảo sát về sản phẩm mới', 'Khảo sát về trải nghiệm người dùng',
        'Khảo sát về chất lượng đào tạo', 'Khảo sát về văn hóa doanh nghiệp', 'Khảo sát về phúc lợi nhân viên',
        'Ý tưởng Year End Party', 'Phản hồi tính năng Dark Mode', 'Bữa trưa nay ăn gì?',
        'Mức độ hài lòng tổng quan', 'Đánh giá sự kiện', 'Góp ý cải tiến sản phẩm'
    ];
    const categories = ['Thói quen', 'Sức khỏe', 'Công nghệ', 'Giáo dục', 'Dịch vụ', 'Thương mại', 'QuickPoll'];
    const statuses = ['approved', 'pending', 'draft'];
    const creators = ['Nguyễn Văn A', 'Trần Thị B', 'Lê Văn C', 'Phạm Thị D', 'Hoàng Văn E', 'Nguyễn Thị F', 'Trần Văn G', 'Lê Thị H'];

    for (let i = 1; i <= count; i++) {
        const isQuickPoll = Math.random() > 0.7;
        const status = statuses[Math.floor(Math.random() * statuses.length)];
        surveys.push({
            id: i,
            code: isQuickPoll ? `QP${String(i).padStart(3, '0')}` : `KS${String(i).padStart(3, '0')}`,
            title: titles[Math.floor(Math.random() * titles.length)] + ` #${i}`,
            status: status,
            type: isQuickPoll ? 'quickpoll' : 'regular',
            questions: isQuickPoll ? 1 : Math.floor(Math.random() * 15) + 5,
            responses: status === 'draft' ? 0 : Math.floor(Math.random() * 300),
            creator: creators[Math.floor(Math.random() * creators.length)],
            createdAt: new Date(2024, Math.floor(Math.random() * 3), Math.floor(Math.random() * 28) + 1).toISOString().split('T')[0],
            category: isQuickPoll ? 'QuickPoll' : categories[Math.floor(Math.random() * (categories.length - 1))],
            points: isQuickPoll ? 5 : Math.floor(Math.random() * 15) + 5
        });
    }
    return surveys;
}

function generateUsers(count) {
    const users = [];
    const firstNames = ['Nguyễn', 'Trần', 'Lê', 'Phạm', 'Hoàng', 'Phan', 'Vũ', 'Võ', 'Đặng', 'Bùi'];
    const middleNames = ['Văn', 'Thị', 'Minh', 'Hữu', 'Đức', 'Thanh', 'Quang', 'Anh'];
    const lastNames = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T'];
    const roles = ['admin', 'moderator', 'user', 'user', 'user', 'user']; // More users than admins
    const statuses = ['active', 'active', 'active', 'inactive']; // More active than inactive

    for (let i = 1; i <= count; i++) {
        const firstName = firstNames[Math.floor(Math.random() * firstNames.length)];
        const middleName = middleNames[Math.floor(Math.random() * middleNames.length)];
        const lastName = lastNames[Math.floor(Math.random() * lastNames.length)];
        const fullName = `${firstName} ${middleName} ${lastName}`;
        const email = `${firstName.toLowerCase()}${middleName.toLowerCase()}${lastName.toLowerCase()}${i}@example.com`;
        const role = roles[Math.floor(Math.random() * roles.length)];
        const status = statuses[Math.floor(Math.random() * statuses.length)];

        users.push({
            id: i,
            name: fullName,
            email: email,
            role: role,
            avatar: `${firstName[0]}${lastName}`,
            status: status,
            surveys: Math.floor(Math.random() * 20),
            responses: Math.floor(Math.random() * 300),
            joinedAt: new Date(2023, Math.floor(Math.random() * 12), Math.floor(Math.random() * 28) + 1).toISOString().split('T')[0],
            lastActive: status === 'active' ?
                ['2 phút trước', '15 phút trước', '1 giờ trước', '3 giờ trước', '5 giờ trước'][Math.floor(Math.random() * 5)] :
                ['2 ngày trước', '1 tuần trước', '2 tuần trước'][Math.floor(Math.random() * 3)]
        });
    }
    return users;
}

function generateEvents(count) {
    const events = [];
    const titles = [
        'Sự kiện Khởi động Năm Mới', 'Hội thảo Sức khỏe Cộng đồng', 'Workshop về Digital Marketing',
        'Ngày hội Công nghệ', 'Hội nghị Khoa học', 'Triển lãm Sản phẩm', 'Chương trình Team Building',
        'Lễ kỷ niệm thành lập', 'Buổi gặp mặt khách hàng', 'Hội thảo Đào tạo'
    ];
    const locations = ['Hội trường A', 'Phòng họp B', 'Online', 'Trung tâm Hội nghị', 'Khách sạn 5 sao', 'Văn phòng chính'];
    const statuses = ['completed', 'ongoing', 'upcoming'];
    const creators = ['Nguyễn Văn A', 'Trần Thị B', 'Lê Văn C', 'Phạm Thị D'];

    for (let i = 1; i <= count; i++) {
        const status = statuses[Math.floor(Math.random() * statuses.length)];
        const month = Math.floor(Math.random() * 6) + 1;
        const day = Math.floor(Math.random() * 28) + 1;

        events.push({
            id: i,
            code: `SK${String(i).padStart(3, '0')}`,
            title: titles[Math.floor(Math.random() * titles.length)] + ` ${2024}`,
            startDate: `2024-${String(month).padStart(2, '0')}-${String(day).padStart(2, '0')} 09:00:00`,
            endDate: `2024-${String(month).padStart(2, '0')}-${String(day).padStart(2, '0')} 17:00:00`,
            status: status,
            participants: status === 'upcoming' ? 0 : Math.floor(Math.random() * 200) + 50,
            surveys: Math.floor(Math.random() * 8) + 1,
            creator: creators[Math.floor(Math.random() * creators.length)],
            location: locations[Math.floor(Math.random() * locations.length)]
        });
    }
    return events;
}

const AdminMockData = {
    // Statistics
    stats: {
        totalUsers: 1234,
        totalSurveys: 567,
        totalResponses: 8901,
        totalEvents: 23,
        activeUsers: 892,
        pendingSurveys: 45,
        completedSurveys: 522,
        quickPolls: 156
    },

    // Trends (for charts)
    trends: {
        userGrowth: [120, 145, 178, 210, 245, 289, 334, 398, 456, 512, 589, 678],
        surveysByMonth: [45, 52, 48, 61, 58, 72, 68, 75, 82, 88, 95, 102],
        responsesByMonth: [520, 680, 750, 820, 910, 1050, 1180, 1250, 1320, 1450, 1580, 1720],
        surveyTypes: {
            regular: 411,
            quickPoll: 156
        }
    },

    // Recent Activities
    activities: [
        {
            id: 1,
            user: 'Nguyễn Văn A',
            action: 'đã tạo khảo sát mới',
            target: 'Khảo sát về sản phẩm mới',
            time: '5 phút trước',
            icon: 'fa-poll',
            color: 'primary'
        },
        {
            id: 2,
            user: 'Trần Thị B',
            action: 'đã hoàn thành',
            target: 'Khảo sát về dịch vụ khách hàng',
            time: '15 phút trước',
            icon: 'fa-check-circle',
            color: 'success'
        },
        {
            id: 3,
            user: 'Lê Văn C',
            action: 'đã tham gia sự kiện',
            target: 'Hội thảo Sức khỏe Cộng đồng',
            time: '1 giờ trước',
            icon: 'fa-calendar',
            color: 'info'
        },
        {
            id: 4,
            user: 'Phạm Thị D',
            action: 'đã phê duyệt',
            target: 'Khảo sát về thói quen đọc sách',
            time: '2 giờ trước',
            icon: 'fa-thumbs-up',
            color: 'warning'
        },
        {
            id: 5,
            user: 'Hoàng Văn E',
            action: 'đã báo cáo',
            target: 'Vấn đề với câu hỏi #234',
            time: '3 giờ trước',
            icon: 'fa-flag',
            color: 'danger'
        }
    ],

    // Generate large datasets
    surveys: generateSurveys(50),
    // Increase mock users so admin UI can demo pagination
    users: generateUsers(120),
    events: generateEvents(15),

    // Reports Data
    reports: {
        topSurveys: [
            { title: 'Khảo sát về dịch vụ khách hàng', responses: 234, rating: 4.5 },
            { title: 'Khảo sát về thói quen đọc sách', responses: 245, rating: 4.2 },
            { title: 'Khảo sát về nhu cầu giáo dục', responses: 189, rating: 4.7 },
            { title: 'Khảo sát về ứng dụng di động', responses: 178, rating: 4.1 },
            { title: 'Mức độ hài lòng tổng quan', responses: 312, rating: 4.8 }
        ],
        topUsers: [
            { name: 'Nguyễn Văn A', responses: 234, surveys: 15 },
            { name: 'Hoàng Văn E', responses: 201, surveys: 12 },
            { name: 'Trần Văn G', responses: 178, surveys: 7 },
            { name: 'Trần Thị B', responses: 156, surveys: 8 },
            { name: 'Phạm Thị D', responses: 123, surveys: 5 }
        ],
        categoryDistribution: {
            'Thói quen': 45,
            'Sức khỏe': 38,
            'Công nghệ': 52,
            'Giáo dục': 41,
            'Dịch vụ': 35,
            'QuickPoll': 156
        }
    }
};

// Helper Functions
const AdminHelpers = {
    formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('vi-VN');
    },

    formatDateTime(dateString) {
        const date = new Date(dateString);
        return date.toLocaleString('vi-VN');
    },

    getStatusBadge(status) {
        const badges = {
            'approved': 'badge-success',
            'pending': 'badge-warning',
            'draft': 'badge-secondary',
            'rejected': 'badge-danger',
            'active': 'badge-success',
            'inactive': 'badge-secondary',
            'completed': 'badge-info',
            'ongoing': 'badge-success',
            'upcoming': 'badge-primary'
        };
        return badges[status] || 'badge-secondary';
    },

    getStatusText(status) {
        const texts = {
            'approved': 'Đã duyệt',
            'pending': 'Chờ duyệt',
            'draft': 'Nháp',
            'rejected': 'Từ chối',
            'active': 'Hoạt động',
            'inactive': 'Không hoạt động',
            'completed': 'Hoàn thành',
            'ongoing': 'Đang diễn ra',
            'upcoming': 'Sắp diễn ra'
        };
        return texts[status] || status;
    },

    getRoleBadge(role) {
        const badges = {
            'admin': 'badge-danger',
            'moderator': 'badge-warning',
            'user': 'badge-primary'
        };
        return badges[role] || 'badge-secondary';
    },

    getRoleText(role) {
        const texts = {
            'admin': 'Quản trị viên',
            'moderator': 'Kiểm duyệt viên',
            'user': 'Người dùng'
        };
        return texts[role] || role;
    },

    getAvatarColor(name) {
        const colors = [
            '#3498db', '#2ecc71', '#f39c12', '#e74c3c', '#9b59b6',
            '#1abc9c', '#34495e', '#16a085', '#27ae60', '#2980b9'
        ];
        const index = name.charCodeAt(0) % colors.length;
        return colors[index];
    }
};
