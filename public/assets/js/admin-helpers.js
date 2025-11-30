class AdminHelpers {
    static formatDate(dateString) {
        if (!dateString) return '';
        const date = new Date(dateString);
        return date.toLocaleDateString('vi-VN');
    }

    static formatDateTime(dateString) {
        if (!dateString) return '';
        const date = new Date(dateString);
        return date.toLocaleString('vi-VN');
    }

    static getStatusBadge(status) {
        // Return full Bootstrap badge classes to ensure consistent styling
        const badges = {
            'approved': 'badge-success rounded-pill',
            'pending': 'badge-warning rounded-pill',
            'draft': 'badge-secondary rounded-pill',
            'rejected': 'badge-danger rounded-pill',
            'active': 'badge-success rounded-pill',
            'inactive': 'badge-secondary rounded-pill',
            'completed': 'badge-info rounded-pill',
            'ongoing': 'badge-success rounded-pill',
            'upcoming': 'badge-primary rounded-pill'
        };
        return badges[status] || 'badge-secondary rounded-pill';
    }

    static getStatusText(status) {
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
    }

    static getRoleText(role) {
        const texts = {
            'admin': 'Quản trị viên',
            'moderator': 'Kiểm duyệt viên',
            'user': 'Người dùng'
        };
        return texts[role] || role;
    }

    static getAvatarColor(name) {
        if (!name || name.length === 0) return '#34495e';
        const colors = [
            '#3498db', '#2ecc71', '#f39c12', '#e74c3c', '#9b59b6',
            '#1abc9c', '#34495e', '#16a085', '#27ae60', '#2980b9'
        ];
        const index = name.charCodeAt(0) % colors.length;
        return colors[index];
    }
}
// export class
if (typeof window !== 'undefined') {
    window.AdminHelpers = AdminHelpers;
}

if (typeof module !== 'undefined' && typeof module.exports !== 'undefined') {
    module.exports = AdminHelpers;
}
