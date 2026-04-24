@extends('layouts.admin')

@section('title', 'Kelola Jadwal Praktek')

@push('styles')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    * { 
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }

    /* ===== DARK MODE SUPPORT ===== */
    :root {
        --p1: #39A616;
        --p2: #1D8208;
        --p3: #0C5B00;
        --grad: linear-gradient(135deg, #39A616, #1D8208, #0C5B00);
        --grad-reverse: linear-gradient(135deg, #0C5B00, #1D8208, #39A616);
        
        /* Light Mode Colors */
        --text-primary: #1f2937;
        --text-secondary: #6b7280;
        --text-muted: #9ca3af;
        --bg-primary: #ffffff;
        --bg-secondary: #f9fafb;
        --bg-tertiary: #f3f4f6;
        --border-color: rgba(57, 166, 22, 0.15);
        --shadow-color: rgba(57, 166, 22, 0.1);
        --hover-bg: rgba(57, 166, 22, 0.04);
        --modal-bg: #ffffff;
        --modal-overlay: rgba(0,0,0,0.7);
    }

    /* Dark Mode Colors */
    [data-theme="dark"],
    .dark-mode {
        --text-primary: #f9fafb;
        --text-secondary: #d1d5db;
        --text-muted: #9ca3af;
        --bg-primary: #1f2937;
        --bg-secondary: #111827;
        --bg-tertiary: #374151;
        --border-color: rgba(57, 166, 22, 0.3);
        --shadow-color: rgba(0, 0, 0, 0.3);
        --hover-bg: rgba(57, 166, 22, 0.15);
        --modal-bg: #1f2937;
        --modal-overlay: rgba(0,0,0,0.85);
    }

    /* Auto Dark Mode */
    @media (prefers-color-scheme: dark) {
        :root:not([data-theme="light"]) {
            --text-primary: #f9fafb;
            --text-secondary: #d1d5db;
            --text-muted: #9ca3af;
            --bg-primary: #1f2937;
            --bg-secondary: #111827;
            --bg-tertiary: #374151;
            --border-color: rgba(57, 166, 22, 0.3);
            --shadow-color: rgba(0, 0, 0, 0.3);
            --hover-bg: rgba(57, 166, 22, 0.15);
            --modal-bg: #1f2937;
            --modal-overlay: rgba(0,0,0,0.85);
        }
    }

    body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        background: var(--bg-secondary);
        color: var(--text-primary);
        transition: background 0.3s ease, color 0.3s ease;
    }

    .container-fluid-modern {
        max-width: 1400px;
        margin: 0 auto;
        padding: 40px 20px;
    }

    /* ===== HEADER BANNER ===== */
    .page-header-banner {
        margin-bottom: 40px;
    }

    .header-content {
        display: flex;
        align-items: center;
        gap: 20px; 
        background: var(--grad);
        padding: 35px 40px;
        border-radius: 24px;
        box-shadow: 0 15px 50px rgba(57, 166, 22, 0.25);
        position: relative;
        overflow: hidden;
    }

    .header-content::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 350px;
        height: 350px;
        background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 70%);
        border-radius: 50%;
    }

    .header-icon {
        width: 75px;
        height: 75px;
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 36px;
        color: #fff;
        flex-shrink: 0;
        position: relative;
        z-index: 1;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
    }
    
    .header-text {
        flex: 1;
        position: relative;
        z-index: 1;
    }

    .greeting-badge {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        background: rgba(255, 255, 255, 0.25);
        backdrop-filter: blur(12px);
        padding: 10px 20px;
        border-radius: 25px;
        color: white;
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 15px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .page-title {
        color: #fff;
        font-weight: 800;
        font-size: 2.2rem;
        margin: 0 0 10px 0;
        letter-spacing: -0.5px;
    }

    .page-subtitle {
        display: flex;
        align-items: center;
        gap: 10px;
        color: rgba(255, 255, 255, 0.95);
        font-size: 1.05rem;
        font-weight: 500;
        margin: 0;
    }

    .hero-illustration {
        position: relative;
        flex-shrink: 0;
        z-index: 1;
    }

    .btn-add {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        background: rgba(255, 255, 255, 0.18);
        backdrop-filter: blur(15px);
        color: white;
        padding: 16px 28px;
        border-radius: 25px;
        font-size: 1rem;
        font-weight: 700;
        cursor: pointer;
        border: none;
        transition: all 0.3s ease;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        position: relative;
        z-index: 1;
        white-space: nowrap;
    }

    .btn-add:hover {
        background: rgba(255, 255, 255, 0.3);
    }

    /* ===== PAGE ACTION BAR ===== */
    .page-action-bar {
        display: flex;
        justify-content: flex-end;
        margin-bottom: 20px;
    }

    .btn-danger-action {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        background: linear-gradient(135deg, #e74c3c, #c0392b);
        color: white;
        padding: 14px 24px;
        border-radius: 20px;
        font-size: 0.95rem;
        font-weight: 700;
        cursor: pointer;
        border: none;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(231, 76, 60, 0.3);
        white-space: nowrap;
    }

    .btn-danger-action:hover {
        background: linear-gradient(135deg, #c0392b, #a93226);
    }

    /* ===== ALERT ===== */
    .alert-success-modern {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 20px 28px;
        border-radius: 20px;
        margin-bottom: 30px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.12);
        background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
        border: 2px solid #28a745;
    }

    [data-theme="dark"] .alert-success-modern,
    .dark-mode .alert-success-modern {
        background: linear-gradient(135deg, rgba(46, 204, 113, 0.2), rgba(39, 174, 96, 0.3));
        border-color: #28a745;
    }

    .alert-icon {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        background: #28a745;
        color: white;
        font-size: 22px;
    }

    .alert-text {
        flex: 1;
        color: var(--text-primary);
        font-weight: 600;
        font-size: 1rem;
    }

    .alert-close-btn {
        background: none;
        border: none;
        color: var(--text-muted);
        cursor: pointer;
        font-size: 1.4rem;
        padding: 5px;
        transition: all 0.3s ease;
    }

    .alert-close-btn:hover {
        color: var(--text-primary);
    }

    /* ===== TABLE WITH RESPONSIVE HORIZONTAL SCROLL ===== */
    .schedule-container-modern {
        background: var(--bg-primary);
        border-radius: 24px;
        box-shadow: 0 8px 30px var(--shadow-color);
        border: 1px solid var(--border-color);
        overflow: hidden;
    }

    .table-card-header {
        background: var(--grad);
        padding: 20px 28px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
    }

    .table-title {
        font-size: 1.15rem;
        font-weight: 700;
        color: white;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .schedule-count {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        background: rgba(255, 255, 255, 0.2);
        color: white;
        padding: 12px 24px;
        border-radius: 25px;
        font-size: 0.95rem;
        font-weight: 600;
        backdrop-filter: blur(10px);
    }

    .table-responsive {
        overflow-x: auto;
        width: 100%;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: thin;
        scrollbar-color: rgba(57, 166, 22, 0.3) var(--bg-secondary);
    }

    .table-responsive::-webkit-scrollbar {
        height: 8px;
    }

    .table-responsive::-webkit-scrollbar-track {
        background: var(--bg-secondary);
        border-radius: 10px;
    }

    .table-responsive::-webkit-scrollbar-thumb {
        background-color: rgba(57, 166, 22, 0.3);
        border-radius: 10px;
        border: 2px solid var(--bg-secondary);
    }

    .schedule-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 900px; 
    }

    .schedule-table thead {
        background: var(--grad);
    }

    .schedule-table thead th {
        padding: 20px 24px;
        text-align: left;
        color: white;
        font-weight: 600;
        font-size: 0.95rem;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        white-space: nowrap;
    }

    .text-center {
        text-align: center !important;
    }

    .schedule-row {
        border-bottom: 1px solid var(--border-color);
        transition: all 0.3s ease;
    }
    
    .schedule-row:hover {
        background: var(--hover-bg);
    }

    .schedule-table tbody td {
        padding: 20px 24px;
        color: var(--text-secondary);
        white-space: nowrap;
        vertical-align: middle;
    }

    /* Number Badge */
    .number-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 40px;
        height: 40px;
        background: linear-gradient(135deg, #6c757d, #5a6268);
        color: white;
        border-radius: 12px;
        font-weight: 700;
        font-size: 1rem;
        padding: 0 10px;
    }

    /* Doctor Info */
    .doctor-info {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .doctor-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: var(--grad);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 20px;
        flex-shrink: 0;
        overflow: hidden;
        border: 3px solid var(--border-color);
    }

    .doctor-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .doctor-name {
        font-weight: 600;
        color: var(--text-primary);
        min-width: 120px;
    }

    /* Badges */
    .service-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 18px;
        background: linear-gradient(135deg, rgba(52, 152, 219, 0.1), rgba(41, 128, 185, 0.2));
        color: #1976d2;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.85rem;
        border: 1px solid rgba(52, 152, 219, 0.2);
    }

    [data-theme="dark"] .service-badge,
    .dark-mode .service-badge {
        background: linear-gradient(135deg, rgba(52, 152, 219, 0.2), rgba(41, 128, 185, 0.3));
        color: #60a5fa;
        border-color: rgba(52, 152, 219, 0.4);
    }

    .day-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 18px;
        background: linear-gradient(135deg, rgba(155, 89, 182, 0.1), rgba(142, 68, 173, 0.2));
        color: #7b1fa2;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.85rem;
        border: 1px solid rgba(155, 89, 182, 0.2);
    }

    [data-theme="dark"] .day-badge,
    .dark-mode .day-badge {
        background: linear-gradient(135deg, rgba(155, 89, 182, 0.2), rgba(142, 68, 173, 0.3));
        color: #c084fc;
        border-color: rgba(155, 89, 182, 0.4);
    }

    .time-badge-modern {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 18px;
        background: linear-gradient(135deg, rgba(243, 156, 18, 0.1), rgba(230, 126, 34, 0.2));
        color: #856404;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.85rem;
        border: 1px solid rgba(243, 156, 18, 0.2);
    }

    [data-theme="dark"] .time-badge-modern,
    .dark-mode .time-badge-modern {
        background: linear-gradient(135deg, rgba(243, 156, 18, 0.2), rgba(230, 126, 34, 0.3));
        color: #fbbf24;
        border-color: rgba(243, 156, 18, 0.4);
    }

    /* Action Buttons */
    .action-buttons {
        display: flex;
        gap: 8px;
        justify-content: center;
        flex-wrap: wrap;
    }

    .btn-action {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border-radius: 12px;
        border: none;
        cursor: pointer;
        font-size: 0.95rem;
    }

    .btn-edit {
        background: linear-gradient(135deg, #3498db, #2980b9);
        color: white;
    }

    .btn-hapus {
        background: linear-gradient(135deg, #e74c3c, #c0392b);
        color: white;
    }

    /* Empty State */
    .empty-schedule {
        text-align: center;
        padding: 70px 20px;
        color: var(--text-muted);
    }

    .empty-schedule i {
        font-size: 4.5rem;
        margin-bottom: 24px;
        opacity: 0.3;
    }

    .empty-schedule p {
        font-size: 1.15rem;
        font-weight: 600;
        margin-bottom: 10px;
        color: var(--text-secondary);
    }

    .empty-schedule small {
        font-size: 0.95rem;
        color: var(--text-muted);
    }

    /* ===== MODAL ===== */
    .modal-overlay {
        display: none;
        position: fixed;
        z-index: 2000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: var(--modal-overlay);
        backdrop-filter: blur(8px);
        justify-content: center;
        align-items: center;
    }

    .modal-card {
        background-color: var(--modal-bg);
        margin: 20px;
        border-radius: 24px;
        width: 90%;
        max-width: 650px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        max-height: 90vh;
        display: flex;
        flex-direction: column;
        position: relative;
    }

    .modal-card-small {
        max-width: 500px;
    }

    .close-modal {
        position: absolute;
        top: -15px;
        right: -15px;
        width: 48px;
        height: 48px;
        background: var(--bg-primary);
        border: 2px solid var(--border-color);
        border-radius: 50%;
        cursor: pointer;
        z-index: 10;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .close-modal i {
        font-size: 1.2rem;
        color: var(--text-muted);
    }

    .modal-content {
        padding: 40px;
        overflow-y: auto;
    }

    .modal-header {
        text-align: center;
        margin-bottom: 32px;
        padding-bottom: 20px;
        border-bottom: 2px solid var(--border-color);
    }

    .modal-header-center {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 15px;
        border-bottom: none;
        padding-bottom: 10px;
    }

    .modal-icon {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 32px;
    }

    .modal-icon-danger {
        background: linear-gradient(135deg, #fee2e2, #fecaca);
        color: #e74c3c;
    }

    #cancelScheduleModal .modal-icon-danger {
        background: #e74c3c; 
        color: white;
    }

    .modal-title {
        background: var(--grad);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        font-weight: 800;
        font-size: 1.8rem;
        margin: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
    }

    .modal-title-danger {
        background: linear-gradient(135deg, #e74c3c, #c0392b);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .modal-description {
        color: var(--text-secondary);
        font-size: 1rem;
        margin: 20px 0 30px 0;
        line-height: 1.6;
        text-align: center;
    }

    .modal-description strong {
        color: var(--text-primary);
        font-weight: 700;
    }

    .text-danger-small {
        color: #e74c3c;
        font-size: 0.9rem;
        margin-top: 15px;
        padding: 12px;
        background: rgba(231, 76, 60, 0.1);
        border-radius: 8px;
        border-left: 3px solid #e74c3c;
    }

    /* Input Validation & Form Styles */
    .form-group { margin-bottom: 24px; }
    .form-label {
        display: flex; align-items: center; gap: 8px; margin-bottom: 10px;
        font-weight: 700; color: var(--text-primary); font-size: 0.95rem;
    }
    .form-label i { color: var(--p1); }
    
    .form-control {
        width: 100%; padding: 14px 18px; border: 2px solid var(--border-color);
        border-radius: 14px; font-size: 0.95rem; font-family: 'Inter', sans-serif;
        background-color: var(--bg-secondary); color: var(--text-primary);
        font-weight: 500;
    }

    .form-control:focus {
        outline: none; border-color: var(--p1); background-color: var(--bg-primary);
    }

    /* JS Validation Styles */
    .input-error {
        border-color: #ef4444 !important;
        background-color: #fef2f2 !important;
    }
    [data-theme="dark"] .input-error {
        border-color: #ef4444 !important;
        background-color: rgba(239, 68, 68, 0.1) !important;
    }
    
    .error-container {
        background: linear-gradient(135deg, #fee2e2, #fecaca);
        color: #991b1b;
        padding: 18px;
        border-radius: 14px;
        margin-bottom: 24px;
        border: 2px solid #ef4444;
        display: none; 
    }
    .error-container.active { display: block; }
    
    [data-theme="dark"] .error-container {
        background: linear-gradient(135deg, rgba(239, 68, 68, 0.2), rgba(220, 38, 38, 0.3));
        color: #fca5a5;
        border-color: rgba(239, 68, 68, 0.5);
    }
    .error-container ul { margin-top: 8px; margin-left: 20px; }

    .form-grid {
        display: grid; grid-template-columns: 1fr 1fr; gap: 20px;
    }

    /* Checkbox Grid */
    .checkbox-grid {
        display: grid; grid-template-columns: repeat(auto-fit, minmax(110px, 1fr));
        gap: 12px; margin-top: 10px;
    }

    .checkbox-label {
        display: flex; align-items: center; gap: 10px; padding: 14px;
        background: var(--bg-secondary); border: 2px solid var(--border-color);
        border-radius: 12px; cursor: pointer; position: relative;
    }

    .checkbox-label input[type="checkbox"] { display: none; }

    .checkbox-custom {
        width: 22px; height: 22px; border: 2px solid var(--border-color);
        border-radius: 6px; position: relative; flex-shrink: 0; background: var(--bg-primary);
    }

    .checkbox-label input[type="checkbox"]:checked + .checkbox-custom {
        background: var(--grad); border-color: var(--p1);
    }

    .checkbox-label input[type="checkbox"]:checked + .checkbox-custom::after {
        content: '\f00c'; font-family: 'Font Awesome 5 Free'; font-weight: 900;
        color: white; position: absolute; top: 50%; left: 50%;
        transform: translate(-50%, -50%); font-size: 12px;
    }

    .checkbox-label input[type="checkbox"]:checked ~ .checkbox-text {
        color: var(--p1); font-weight: 700;
    }

    .checkbox-text {
        font-size: 0.9rem; color: var(--text-secondary); font-weight: 600;
    }

    /* Form Actions */
    .form-actions {
        display: flex; gap: 12px; margin-top: 32px; padding-top: 24px;
        border-top: 2px solid var(--border-color);
    }
    
    #cancelScheduleModal .modal-card .form-actions {
        padding: 0 40px 40px; margin-top: 0; border-top: none;
    }

    .btn-primary {
        flex: 1; background: var(--grad); color: #fff; border: none;
        padding: 16px 32px; border-radius: 16px; cursor: pointer;
        font-weight: 700; font-size: 1.05rem; display: inline-flex;
        align-items: center; justify-content: center; gap: 10px;
    }

    .btn-secondary {
        flex: 1; background: var(--bg-secondary); color: var(--text-primary);
        border: 2px solid var(--border-color); padding: 16px 32px;
        border-radius: 16px; cursor: pointer; font-weight: 700; font-size: 1.05rem;
        display: inline-flex; align-items: center; justify-content: center; gap: 10px;
    }

    .btn-danger {
        flex: 1; background: linear-gradient(135deg, #e74c3c, #c0392b); color: #fff;
        border: none; padding: 16px 32px; border-radius: 16px; cursor: pointer;
        font-weight: 700; font-size: 1.05rem; display: inline-flex;
        align-items: center; justify-content: center; gap: 10px;
    }

    /* ===== RESPONSIVE DESIGN FOR MOBILE ===== */
    @media (max-width: 992px) {
        .hero-illustration { display: none; }
        .schedule-table { min-width: 850px; }
    }

    @media (max-width: 768px) {
        .container-fluid-modern { padding: 25px 16px; }
        .page-header-banner { margin-bottom: 30px; }
        .header-content { flex-direction: column; text-align: center; padding: 28px 20px; gap: 16px; }
        .header-icon { width: 65px; height: 65px; font-size: 32px; }
        .page-title { font-size: 1.75rem; }
        .page-subtitle { font-size: 0.95rem; justify-content: center; }
        .page-action-bar { justify-content: stretch; margin-bottom: 25px; }
        .btn-danger-action { width: 100%; justify-content: center; }
        .table-card-header { flex-direction: column; align-items: flex-start; padding: 18px 20px; }
        .table-title { font-size: 1.05rem; }
        .schedule-count { padding: 10px 20px; font-size: 0.9rem; }
        .schedule-table { font-size: 0.9rem; min-width: 800px; }
        .schedule-table thead th, .schedule-table tbody td { padding: 16px 14px; }
        .doctor-avatar { width: 45px; height: 45px; font-size: 18px; }
        .doctor-name { min-width: 100px; }
        .service-badge, .day-badge, .time-badge-modern { font-size: 0.8rem; padding: 8px 14px; }
        .action-buttons { gap: 6px; }
        .btn-action { width: 38px; height: 38px; }
        .modal-card { width: 95%; margin: 10px; }
        .modal-content { padding: 28px 24px; }
        .modal-title { font-size: 1.5rem; }
        .form-grid { grid-template-columns: 1fr; }
        .checkbox-grid { grid-template-columns: repeat(auto-fit, minmax(100px, 1fr)); }
        .form-actions { flex-direction: column; }
        #cancelScheduleModal .modal-card .form-actions { padding: 0 24px 28px; }
    }

    @media (max-width: 576px) {
        .container-fluid-modern { padding: 20px 12px; }
        .header-content { padding: 24px 18px; border-radius: 20px; }
        .header-icon { width: 60px; height: 60px; font-size: 28px; border-radius: 14px; }
        .page-title { font-size: 1.5rem; }
        .page-subtitle { font-size: 0.9rem; }
        .greeting-badge { font-size: 0.8rem; padding: 8px 16px; }
        .btn-danger-action { font-size: 0.9rem; padding: 12px 20px; }
        .table-card-header { padding: 16px 18px; }
        .table-title { font-size: 1rem; }
        .schedule-count { padding: 8px 16px; font-size: 0.85rem; }
        .schedule-table { min-width: 750px; }
        .schedule-table thead th { font-size: 0.8rem; padding: 14px 12px; }
        .schedule-table tbody td { padding: 14px 12px; font-size: 0.85rem; }
        .doctor-avatar { width: 42px; height: 42px; font-size: 16px; border-width: 2px; }
        .doctor-name { font-size: 0.9rem; min-width: 90px; }
        .number-badge { min-width: 36px; height: 36px; font-size: 0.9rem; }
        .service-badge, .day-badge, .time-badge-modern { font-size: 0.75rem; padding: 7px 12px; }
        .btn-action { width: 36px; height: 36px; font-size: 0.9rem; }
        .empty-schedule { padding: 50px 16px; }
        .empty-schedule i { font-size: 3.5rem; }
        .empty-schedule p { font-size: 1rem; }
        .empty-schedule small { font-size: 0.85rem; }
        .modal-content { padding: 24px 20px; }
        .modal-title { font-size: 1.3rem; }
        .modal-icon { width: 60px; height: 60px; font-size: 28px; }
        .checkbox-grid { grid-template-columns: 1fr 1fr; }
        .checkbox-label { padding: 12px; }
        .checkbox-text { font-size: 0.85rem; }
        .btn-primary, .btn-secondary, .btn-danger { padding: 14px 24px; font-size: 0.95rem; }
    }

    @media (max-width: 400px) {
        .page-title { font-size: 1.35rem; }
        .schedule-table { min-width: 700px; }
        .checkbox-grid { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')

    <div class="page-header-banner">
        <div class="header-content">
            <div class="header-icon"><i class="fas fa-calendar-alt"></i></div>
            <div class="header-text">
                <div class="greeting-badge">
                    <i class="fas fa-hospital"></i>
                    <span>Schedule Management</span>
                </div>
                <h1 class="page-title">Kelola Jadwal Praktek 📅</h1>
                <p class="page-subtitle">
                    <i class="far fa-calendar-check"></i>
                    Manajemen Jadwal Tenaga Medis
                </p>
            </div>
            <div class="hero-illustration">
                <button class="btn-add" id="btnTambahJadwal">
                    <span>Tambah Jadwal</span>
                    <i class="fas fa-plus"></i>
                </button>
            </div>
        </div>
    </div>

    <div class="page-action-bar">
        <button type="button" class="btn-danger-action" id="btnCancelScheduleModalToggle">
            <i class="fas fa-calendar-times"></i> Tutup Jadwal Spesifik
        </button>
    </div>

    @if (session('success'))
        <div class="alert-success-modern" id="autoHideAlert">
            <div class="alert-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <span class="alert-text">{{ session('success') }}</span>
            <button class="alert-close-btn" onclick="this.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    <div class="schedule-container-modern">
        <div class="table-card-header">
            <h3 class="table-title">
                <i class="fas fa-list"></i> 
                Daftar Jadwal Praktek
            </h3>
            
            <span class="schedule-count" id="total-jadwal">
                <i class="fas fa-calendar-check"></i>
                {{ $jadwals->count() }} Jadwal Aktif
            </span>
        </div>

        {{-- WRAPPER RESPONSIVE DENGAN HORIZONTAL SCROLL --}}
        <div class="table-responsive">
            <table class="schedule-table">
                <thead>
                    <tr>
                        <th><i class="fas fa-hashtag"></i> No</th>
                        <th><i class="fas fa-user-md"></i> Nama Dokter</th>
                        <th><i class="fas fa-stethoscope"></i> Layanan</th>
                        <th><i class="fas fa-calendar"></i> Hari</th>
                        <th><i class="far fa-clock"></i> Waktu</th>
                        <th class="text-center"><i class="fas fa-cog"></i> Aksi</th>
                    </tr>
                </thead>

                <tbody id="table-body">
                    @php
                        if (!function_exists('formatHariFleksibel')) {
                            function formatHariFleksibel($hariArray) {
                                if (empty($hariArray) || !is_array($hariArray)) return '';
                                if (count($hariArray) == 7) return 'Setiap Hari';
                                $semuaHari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
                                $indeksHari = array_map(fn($h) => array_search($h, $semuaHari), $hariArray); 
                                sort($indeksHari); 
                                $rentang = [];
                                for ($i = 0, $j = 0, $n = count($indeksHari); $i < $n; $i = $j) {
                                    $j = $i + 1; 
                                    while ($j < $n && $indeksHari[$j] == $indeksHari[$j-1] + 1) $j++; 
                                    $rentang[] = [$indeksHari[$i], $indeksHari[$j-1]];
                                }
                                $outputStrings = [];
                                foreach ($rentang as $r) { 
                                    $awal = $semuaHari[$r[0]]; 
                                    $akhir = $semuaHari[$r[1]]; 
                                    $outputStrings[] = ($awal == $akhir) ? $awal : "$awal - $akhir"; 
                                }
                                return implode(', ', $outputStrings);
                            }
                        }
                    @endphp

                    @forelse ($jadwals as $index => $jadwal)
                        <tr class="schedule-row">
                            <td>
                                <span class="number-badge">{{ $index + 1 }}</span>
                            </td>
                            <td>
                                <div class="doctor-info">
                                    <div class="doctor-avatar">
                                        @if($jadwal->tenagaMedis && $jadwal->tenagaMedis->profile_photo_path)
                                            <img src="{{ asset('storage/' . $jadwal->tenagaMedis->profile_photo_path) }}" alt="{{ $jadwal->tenagaMedis->name }}">
                                        @else
                                            <i class="fas fa-user-md"></i>
                                        @endif
                                    </div>
                                    <span class="doctor-name">{{ $jadwal->tenagaMedis?->name ?? 'N/A' }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="service-badge">
                                    <i class="fas fa-briefcase-medical"></i>
                                    {{ $jadwal->layanan }}
                                </span>
                            </td>
                            <td>
                                <span class="day-badge">
                                    <i class="fas fa-calendar-week"></i>
                                    {{ formatHariFleksibel($jadwal->hari) }}
                                </span>
                            </td>
                            <td>
                                <div class="time-badge-modern">
                                    <i class="far fa-clock"></i>
                                    <span>
                                        {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }} - 
                                        {{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }} WIB
                                    </span>
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="action-buttons">
                                    <button class="btn-action btn-edit" 
                                            data-id="{{ $jadwal->id }}"
                                            data-tenaga-medis-id="{{ $jadwal->tenaga_medis_id }}"
                                            data-layanan="{{ $jadwal->layanan }}"
                                            data-hari='@json($jadwal->hari)'
                                            data-jam-mulai="{{ $jadwal->jam_mulai }}"
                                            data-jam-selesai="{{ $jadwal->jam_selesai }}"
                                            title="Edit Jadwal">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="{{ route('admin.kelolajadwalpraktek.destroy', $jadwal->id) }}" 
                                          method="POST" 
                                          class="delete-form"
                                          style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-action btn-hapus" title="Hapus Jadwal">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="empty-schedule">
                                    <i class="fas fa-calendar-times"></i>
                                    <p>Belum ada jadwal praktek</p>
                                    <small>Klik tombol "Tambah Jadwal" untuk membuat jadwal baru</small>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- MODAL FORM (CREATE / EDIT) --}}
<div id="jadwalModal" class="modal-overlay">
    <div class="modal-card">
        <button class="close-modal" id="closeModalBtn">
            <i class="fas fa-times"></i>
        </button>
        
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title" id="modalTitle">
                    <i class="fas fa-calendar-plus"></i>
                    <span id="modalTitleText">Tambah Jadwal Praktek</span>
                </h2>
            </div>

            <div id="modalErrors" class="error-container"></div>

            <form id="jadwalForm" method="POST" novalidate>
                @csrf
                <input type="hidden" id="formMethod" name="_method" value="">
                
                <div class="form-group">
                    <label for="tenaga_medis_id" class="form-label">
                        <i class="fas fa-user-md"></i> Tenaga Medis
                    </label>
                    <select name="tenaga_medis_id" id="tenaga_medis_id" class="form-control" required>
                        <option value="">-- Pilih Tenaga Medis --</option>
                        @foreach ($tenagaMedis as $tenaga)
                            <option value="{{ $tenaga->id }}">
                                {{ $tenaga->name }} ({{ $tenaga->email }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="layanan" class="form-label">
                        <i class="fas fa-stethoscope"></i> Layanan
                    </label>
                    <input type="text" name="layanan" id="layanan" class="form-control" placeholder="Contoh: Dokter Umum" required>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-calendar-week"></i> Hari Praktek
                    </label>
                    <div class="checkbox-grid" id="hariCheckboxContainer">
                        @php
                            $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
                        @endphp
                        @foreach ($days as $day)
                            <label class="checkbox-label">
                                <input type="checkbox" name="hari[]" value="{{ $day }}" id="day_{{ $day }}">
                                <span class="checkbox-custom"></span>
                                <span class="checkbox-text">{{ $day }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label for="jam_mulai" class="form-label">
                            <i class="fas fa-clock"></i> Jam Mulai
                        </label>
                        <input type="time" name="jam_mulai" id="jam_mulai" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="jam_selesai" class="form-label">
                            <i class="fas fa-clock"></i> Jam Selesai
                        </label>
                        <input type="time" name="jam_selesai" id="jam_selesai" class="form-control" required>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn-secondary" id="btnCancelModal">
                        <i class="fas fa-times"></i>
                        Batal
                    </button>
                    <button type="submit" class="btn-primary" id="btnSubmitModal">
                        <i class="fas fa-save"></i>
                        <span id="btnSubmitText">Simpan Jadwal</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL CANCEL SCHEDULE --}}
<div id="cancelScheduleModal" class="modal-overlay">
    <div class="modal-card modal-card-small">
        <button class="close-modal" id="closeCancelModalBtn">
            <i class="fas fa-times"></i>
        </button>
        
        <div class="modal-content">
            <div class="modal-header modal-header-center">
                <div class="modal-icon modal-icon-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h2 class="modal-title modal-title-danger">
                    <span>Penutupan Jadwal</span>
                </h2>
            </div>

            <div id="cancelModalErrors" class="error-container"></div>

            <form id="formCancelSchedule" novalidate>
                @csrf 
                <div class="form-group">
                    <label for="cancel_doctor" class="form-label">
                        <i class="fas fa-user-md"></i> Pilih Dokter/Tenaga Medis
                    </label>
                    <select name="tenaga_medis_id" id="cancel_doctor" class="form-control" required>
                        <option value="">-- Pilih Dokter --</option>
                        @foreach ($tenagaMedis as $tm)
                            <option value="{{ $tm->id }}">{{ $tm->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="cancel_date" class="form-label">
                        <i class="fas fa-calendar-day"></i> Tanggal yang Akan Ditutup
                    </label>
                    <input type="date" class="form-control" id="cancel_date" name="date" required 
                           min="{{ \Carbon\Carbon::today()->toDateString() }}">
                </div>
                
                <div class="form-group">
                    <label for="cancel_reason" class="form-label">
                        <i class="fas fa-exclamation-circle"></i> Alasan Pembatalan (Min. 10 karakter)
                    </label>
                    <textarea class="form-control" id="cancel_reason" name="reason" rows="3" required></textarea>
                </div>

                <p class="text-danger-small">
                    ⚠️ Menutup jadwal ini akan <strong>membatalkan semua pendaftaran pasien</strong> yang sudah ada pada tanggal tersebut dan mengirim notifikasi.
                </p>
                
                <div class="form-actions" style="border-top: 2px solid var(--border-color); padding-top: 24px;">
                    <button type="button" class="btn-secondary" id="btnCancelModalClose">
                        <i class="fas fa-times"></i> Batal
                    </button>
                    <button type="submit" class="btn-danger" id="submitCancelSchedule">
                        <i class="fas fa-calendar-times"></i> Tutup & Batalkan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL KONFIRMASI HAPUS --}}
<div id="deleteConfirmModal" class="modal-overlay">
    <div class="modal-card modal-card-small">
        <button class="close-modal" id="closeDeleteModalBtn">
            <i class="fas fa-times"></i>
        </button>
        
        <div class="modal-content">
            <div class="modal-header modal-header-center">
                <div class="modal-icon modal-icon-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h2 class="modal-title modal-title-danger">
                    <span>Konfirmasi Hapus</span>
                </h2>
            </div>

            <p class="modal-description">
                Apakah Anda yakin ingin menghapus jadwal ini? <br>
                <strong>Tindakan ini tidak dapat dibatalkan.</strong>
            </p>

            <div class="form-actions">
                <button type="button" class="btn-secondary" id="btnCancelDelete">
                    <i class="fas fa-times"></i>
                    Batal
                </button>
                <button type="button" class="btn-danger" id="btnConfirmDelete">
                    <i class="fas fa-trash"></i>
                    Ya, Hapus
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Form & Modals Variables
        const modal = document.getElementById('jadwalModal');
        const btnTambah = document.getElementById('btnTambahJadwal');
        const closeBtn = document.getElementById('closeModalBtn');
        const cancelBtn = document.getElementById('btnCancelModal');
        const form = document.getElementById('jadwalForm');
        const modalTitleText = document.getElementById('modalTitleText');
        const btnSubmitText = document.getElementById('btnSubmitText');
        const formMethod = document.getElementById('formMethod');

        const deleteModal = document.getElementById('deleteConfirmModal');
        const closeDeleteBtn = document.getElementById('closeDeleteModalBtn');
        const cancelDeleteBtn = document.getElementById('btnCancelDelete');
        const confirmDeleteBtn = document.getElementById('btnConfirmDelete');
        let formToDelete = null;

        const cancelModal = document.getElementById('cancelScheduleModal');
        const btnCancelToggle = document.getElementById('btnCancelScheduleModalToggle');
        const closeCancelBtn = document.getElementById('closeCancelModalBtn');
        const cancelModalCloseBtn = document.getElementById('btnCancelModalClose');
        const formCancelSchedule = document.getElementById('formCancelSchedule');
        const btnSubmitModal = document.getElementById('btnSubmitModal');

        // JS Validation Variables
        const modalErrors = document.getElementById('modalErrors');
        const cancelModalErrors = document.getElementById('cancelModalErrors');

        // --- GLOBAL AUTO REFRESH ---
        if (typeof window.initAutoRefresh === 'function') {
            window.initAutoRefresh([
                '#total-jadwal',
                '#table-body'
            ]);
        }

        // --- REBIND EVENTS ---
        window.rebindEvents = function() {
            bindTableEvents();
        };

        function bindTableEvents() {
            document.querySelectorAll('.btn-edit').forEach(btn => {
                btn.removeEventListener('click', handleEditClick);
                btn.addEventListener('click', handleEditClick);
            });

            document.querySelectorAll('.delete-form').forEach(form => {
                form.removeEventListener('submit', handleDeleteSubmit);
                form.addEventListener('submit', handleDeleteSubmit);
            });
        }

        function handleEditClick(e) {
            const btn = e.currentTarget;
            const id = btn.dataset.id;
            const tenagaMedisId = btn.dataset.tenagaMedisId;
            const layanan = btn.dataset.layanan;
            const hari = JSON.parse(btn.dataset.hari);
            const jamMulai = btn.dataset.jamMulai;
            const jamSelesai = btn.dataset.jamSelesai;

            openModal('edit', { id, tenagaMedisId, layanan, hari, jamMulai, jamSelesai });
        }

        function handleDeleteSubmit(e) {
            e.preventDefault(); 
            formToDelete = this; 
            openDeleteModal();
        }

        // --- MODAL FUNCTIONS ---
        function openCancelModal() {
            cancelModal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
            formCancelSchedule.reset();
            cancelModalErrors.classList.remove('active');
            cancelModalErrors.innerHTML = '';
            document.querySelectorAll('#formCancelSchedule .form-control').forEach(el => el.classList.remove('input-error'));
            document.getElementById('cancel_date').min = new Date().toISOString().split('T')[0]; 
        }

        function closeCancelModal() {
            cancelModal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        function openDeleteModal() {
            deleteModal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        function closeDeleteModal() {
            deleteModal.style.display = 'none';
            document.body.style.overflow = 'auto';
            formToDelete = null;
        }

        function openModal(mode, data = null) {
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';

            form.reset();
            document.querySelectorAll('input[name="hari[]"]').forEach(cb => cb.checked = false);
            
            // Bersihkan error styles
            modalErrors.classList.remove('active');
            modalErrors.innerHTML = '';
            document.querySelectorAll('#jadwalForm .form-control').forEach(el => el.classList.remove('input-error'));
            document.getElementById('hariCheckboxContainer').style.border = 'none';
            document.getElementById('hariCheckboxContainer').style.padding = '0';

            if (mode === 'create') {
                modalTitleText.textContent = 'Tambah Jadwal Praktek';
                btnSubmitText.textContent = 'Simpan Jadwal';
                form.action = "{{ route('admin.kelolajadwalpraktek.store') }}";
                formMethod.value = '';
            } else if (mode === 'edit' && data) {
                modalTitleText.textContent = 'Edit Jadwal Praktek';
                btnSubmitText.textContent = 'Simpan Perubahan';
                form.action = `/admin/kelolajadwalpraktek/${data.id}`;
                formMethod.value = 'PUT';

                document.getElementById('tenaga_medis_id').value = data.tenagaMedisId;
                document.getElementById('layanan').value = data.layanan;
                document.getElementById('jam_mulai').value = data.jamMulai;
                document.getElementById('jam_selesai').value = data.jamSelesai;

                if (Array.isArray(data.hari)) {
                    data.hari.forEach(day => {
                        const checkbox = document.getElementById('day_' + day);
                        if (checkbox) checkbox.checked = true;
                    });
                }
            }
        }

        function closeModal() {
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
            form.reset();
        }

        // --- VALIDASI FORM TAMBAH/EDIT JADWAL ---
        form.addEventListener('submit', function(e) {
            let isValid = true;
            let errorMessages = [];

            const tenagaId = document.getElementById('tenaga_medis_id');
            const layanan = document.getElementById('layanan');
            const jamMulai = document.getElementById('jam_mulai');
            const jamSelesai = document.getElementById('jam_selesai');
            const checkboxes = document.querySelectorAll('input[name="hari[]"]:checked');
            const cbContainer = document.getElementById('hariCheckboxContainer');

            // Reset styles
            [tenagaId, layanan, jamMulai, jamSelesai].forEach(el => el.classList.remove('input-error'));
            cbContainer.style.border = 'none';
            cbContainer.style.padding = '0';

            if (tenagaId.value === '') { 
                isValid = false; 
                tenagaId.classList.add('input-error'); 
                errorMessages.push('Tenaga Medis harus dipilih.'); 
            }
            if (layanan.value.trim() === '') { 
                isValid = false; 
                layanan.classList.add('input-error'); 
                errorMessages.push('Layanan tidak boleh kosong.'); 
            }
            if (checkboxes.length === 0) {
                isValid = false;
                cbContainer.style.border = '2px solid #ef4444';
                cbContainer.style.padding = '10px';
                cbContainer.style.borderRadius = '12px';
                errorMessages.push('Pilih minimal satu Hari Praktek.');
            }
            if (jamMulai.value === '') { 
                isValid = false; 
                jamMulai.classList.add('input-error'); 
                errorMessages.push('Jam Mulai harus diisi.'); 
            }
            if (jamSelesai.value === '') { 
                isValid = false; 
                jamSelesai.classList.add('input-error'); 
                errorMessages.push('Jam Selesai harus diisi.'); 
            }

            if (!isValid) {
                e.preventDefault();
                let errorHtml = `<strong><i class="fas fa-exclamation-triangle"></i> Terjadi kesalahan:</strong><ul>`;
                errorMessages.forEach(msg => errorHtml += `<li>${msg}</li>`);
                errorHtml += `</ul>`;
                modalErrors.innerHTML = errorHtml;
                modalErrors.classList.add('active');
                document.querySelector('#jadwalModal .modal-content').scrollTop = 0;
            } else {
                btnSubmitModal.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span>Menyimpan...</span>';
                btnSubmitModal.style.pointerEvents = 'none';
            }
        });

        // Menghilangkan kotak merah form jadwal saat ngetik
        document.querySelectorAll('#jadwalForm .form-control').forEach(el => {
            el.addEventListener('input', function() {
                this.classList.remove('input-error');
                modalErrors.classList.remove('active');
            });
        });
        document.querySelectorAll('input[name="hari[]"]').forEach(el => {
            el.addEventListener('change', function() {
                document.getElementById('hariCheckboxContainer').style.border = 'none';
                document.getElementById('hariCheckboxContainer').style.padding = '0';
                modalErrors.classList.remove('active');
            });
        });

        // --- VALIDASI FORM CANCEL SCHEDULE ---
        if(formCancelSchedule) {
            formCancelSchedule.addEventListener('submit', function(e) {
                e.preventDefault();
                let isValid = true;
                let errorMessages = [];

                const doctorSelect = document.getElementById('cancel_doctor');
                const cancelDate = document.getElementById('cancel_date');
                const cancelReason = document.getElementById('cancel_reason');

                // Reset styles
                [doctorSelect, cancelDate, cancelReason].forEach(el => el.classList.remove('input-error'));

                if (doctorSelect.value === '') { 
                    isValid = false; 
                    doctorSelect.classList.add('input-error'); 
                    errorMessages.push('Dokter harus dipilih.'); 
                }
                if (cancelDate.value === '') { 
                    isValid = false; 
                    cancelDate.classList.add('input-error'); 
                    errorMessages.push('Tanggal harus dipilih.'); 
                }
                if (cancelReason.value.trim().length < 10) { 
                    isValid = false; 
                    cancelReason.classList.add('input-error'); 
                    errorMessages.push('Alasan pembatalan minimal 10 karakter.'); 
                }

                if (!isValid) {
                    let errorHtml = `<strong><i class="fas fa-exclamation-triangle"></i> Validasi Gagal:</strong><ul>`;
                    errorMessages.forEach(msg => errorHtml += `<li>${msg}</li>`);
                    errorHtml += `</ul>`;
                    cancelModalErrors.innerHTML = errorHtml;
                    cancelModalErrors.classList.add('active');
                    return;
                }

                // If valid, proceed to SweetAlert
                cancelModalErrors.classList.remove('active');
                
                const csrfToken = document.querySelector('#formCancelSchedule input[name="_token"]').value;
                const doctorName = doctorSelect.options[doctorSelect.selectedIndex].text;
                const tenagaMedisId = doctorSelect.value;
                const date = cancelDate.value;
                const reason = cancelReason.value;

                Swal.fire({
                    title: 'Konfirmasi Penutupan Jadwal',
                    text: `Anda yakin ingin menutup jadwal ${doctorName} pada tanggal ${date}? Semua pendaftaran yang ada akan dibatalkan!`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Tutup Jadwal',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#e74c3c',
                    cancelButtonColor: '#39A616'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Memproses...',
                            text: 'Membatalkan pendaftaran dan mengirim notifikasi.',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        fetch("{{ route('admin.jadwal.cancel') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken 
                            },
                            body: JSON.stringify({
                                tenaga_medis_id: tenagaMedisId,
                                date: date,
                                reason: reason
                            })
                        })
                        .then(response => {
                            if (!response.ok) {
                                return response.json().then(errorData => {
                                    if (response.status === 422 && errorData.errors) {
                                        const firstError = Object.values(errorData.errors)[0][0];
                                        throw new Error(firstError);
                                    }
                                    throw new Error(errorData.message || 'Pembatalan gagal di server.');
                                });
                            }
                            return response.json();
                        })
                        .then(data => {
                            Swal.fire({
                                title: '✅ Penutupan Berhasil!',
                                html: `Jadwal tanggal ${date} berhasil ditutup. Total <b>${data.pasien_terdampak}</b> pasien telah dinotifikasi.`,
                                icon: 'success'
                            }).then(() => {
                                closeCancelModal();
                                window.location.reload(); 
                            });
                        })
                        .catch(error => {
                            Swal.fire({
                                title: 'Gagal Membatalkan',
                                text: error.message || 'Terjadi kesalahan saat membatalkan jadwal.',
                                icon: 'error'
                            });
                        });
                    }
                });
            });
        }

        // Menghilangkan efek error pada form cancel saat mengetik
        document.querySelectorAll('#formCancelSchedule .form-control').forEach(el => {
            el.addEventListener('input', function() {
                this.classList.remove('input-error');
                cancelModalErrors.classList.remove('active');
            });
        });

        // --- EVENT LISTENERS ---
        bindTableEvents();

        const alert = document.getElementById('autoHideAlert');
        if (alert) {
            setTimeout(() => { 
                alert.style.opacity = '0';
                alert.style.transition = 'opacity 0.6s ease';
                setTimeout(() => alert.remove(), 600);
            }, 5000);
        }

        btnTambah.addEventListener('click', function() { openModal('create'); });
        
        if(btnCancelToggle) btnCancelToggle.addEventListener('click', openCancelModal);
        if(closeCancelBtn) closeCancelBtn.addEventListener('click', closeCancelModal);
        if(cancelModalCloseBtn) cancelModalCloseBtn.addEventListener('click', closeCancelModal);

        closeBtn.addEventListener('click', closeModal);
        cancelBtn.addEventListener('click', closeModal);

        closeDeleteBtn.addEventListener('click', closeDeleteModal);
        cancelDeleteBtn.addEventListener('click', closeDeleteModal);

        confirmDeleteBtn.addEventListener('click', function() {
            if (formToDelete) {
                formToDelete.submit();
            }
        });

        window.addEventListener('click', function(event) {
            if (event.target == modal) closeModal();
            if (event.target == deleteModal) closeDeleteModal();
            if (event.target == cancelModal) closeCancelModal();
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                if(modal.style.display === 'flex') closeModal();
                if(deleteModal.style.display === 'flex') closeDeleteModal();
                if(cancelModal.style.display === 'flex') closeCancelModal();
            }
        });
    });
</script>
@endpush