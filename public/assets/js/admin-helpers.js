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

    static getRoleBadge(role) {
        const badges = {
            'admin': 'badge-danger',
            'moderator': 'badge-warning',
            'user': 'badge-primary'
        };
        return badges[role] || 'badge-secondary';
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
