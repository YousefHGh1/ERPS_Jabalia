<style>
    /* بطاقات الهيكل الإداري - RTL و Grid */
    .admin-structure-card {
        border: 1px solid #ebedf3;
        border-radius: 12px;
        transition: box-shadow 0.2s ease, transform 0.2s ease;
    }
    .admin-structure-card:hover {
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.08);
    }
    .admin-card-actions {
        display: flex;
        gap: 4px;
        flex-shrink: 0;
    }
    .admin-card-actions .btn-icon { padding: 0.35rem 0.5rem; }
    /* تبويبات الهيكل */
    .nav-pills-custom .nav-link {
        border-radius: 8px;
        color: #7e8299;
        font-weight: 600;
    }
    .nav-pills-custom .nav-link:hover { color: #3699FF; }
    .nav-pills-custom .nav-link.active {
        background-color: #3699FF;
        color: #fff;
    }
    /* RTL: الحفاظ على محاذاة البطاقات والأزرار */
    [dir="rtl"] .admin-structure-card .card-title { text-align: center; }
    [dir="rtl"] .admin-card-actions { margin-right: auto; margin-left: 0; }
    [dir="rtl"] .nav-pills-custom .nav-link i { margin-left: 0.35rem; margin-right: 0; }
</style>
