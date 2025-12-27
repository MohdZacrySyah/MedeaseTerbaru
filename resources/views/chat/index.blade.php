@php
    // PERBAIKAN: Gunakan variabel $myRole dari Controller, jangan cek Auth lagi agar konsisten
    $layout = ($myRole === 'medis') ? 'layouts.tenaga_medis' : 'layouts.main';
@endphp

@extends($layout)

@section('title', 'Chat Konsultasi')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    :root {
        --chat-bg: #e5ddd5;
        --sidebar-bg: #ffffff;
        --my-msg-bg: #d9fdd3;
        --their-msg-bg: #ffffff;
        --border-color: #e9edef;
        --active-chat: #f0f2f5;
        --p1: #39A616;
        --p2: #1D8208;
        --p3: #0C5B00;
        --text-primary: #111b21;
        --text-secondary: #667781;
        --shadow-sm: 0 1px 3px rgba(0,0,0,0.08);
        --shadow-md: 0 4px 12px rgba(0,0,0,0.12);
        --shadow-lg: 0 8px 30px rgba(57, 166, 22, 0.15);
    }

    [data-theme="dark"] {
        --chat-bg: #0b141a;
        --sidebar-bg: #111b21;
        --my-msg-bg: #005c4b;
        --their-msg-bg: #202c33;
        --border-color: #222d34;
        --active-chat: #2a3942;
        --text-primary: #e9edef;
        --text-secondary: #8696a0;
        --shadow-sm: 0 1px 3px rgba(0,0,0,0.3);
        --shadow-md: 0 4px 12px rgba(0,0,0,0.4);
        --shadow-lg: 0 8px 30px rgba(0,0,0,0.5);
    }

    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }

    body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);
    }

    .chat-wrapper {
        width: 100%;
        max-width: 1600px;
        margin: 20px auto;
        padding: 0 20px;
    }

    .app-container {
        display: flex;
        width: 100%;
        height: calc(100vh - 180px);
        max-height: 800px;
        min-height: 600px;
        background-color: var(--sidebar-bg);
        overflow: hidden;
        position: relative;
        border-radius: 16px;
        box-shadow: var(--shadow-lg);
        animation: slideUp 0.5s ease-out;
    }

    @keyframes slideUp {
        from { 
            opacity: 0;
            transform: translateY(20px);
        }
        to { 
            opacity: 1;
            transform: translateY(0);
        }
    }

    .sidebar-area {
        width: 30%;
        min-width: 280px;
        max-width: 380px;
        display: flex;
        flex-direction: column;
        border-right: 1px solid var(--border-color);
        background-color: var(--sidebar-bg);
        height: 100%;
        z-index: 2;
        flex-shrink: 0;
    }

    .sidebar-header {
        padding: 16px 18px;
        background: linear-gradient(135deg, var(--p1), var(--p2));
        display: flex;
        justify-content: space-between;
        align-items: center;
        height: 70px;
        box-shadow: var(--shadow-md);
        position: relative;
        overflow: hidden;
        flex-shrink: 0;
    }

    .sidebar-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 200px;
        height: 200px;
        background: radial-gradient(circle, rgba(255,255,255,0.2) 0%, transparent 70%);
        border-radius: 50%;
        animation: pulse 3s ease-in-out infinite;
    }

    @keyframes pulse {
        0%, 100% { transform: scale(1); opacity: 0.5; }
        50% { transform: scale(1.2); opacity: 0.8; }
    }

    .sidebar-header h3 {
        margin: 0;
        color: white;
        font-size: 1.3rem;
        font-weight: 800;
        display: flex;
        align-items: center;
        gap: 10px;
        z-index: 1;
    }

    .sidebar-header h3 i {
        font-size: 1.5rem;
        background: rgba(255,255,255,0.2);
        padding: 8px;
        border-radius: 10px;
        backdrop-filter: blur(10px);
    }

    .header-actions {
        display: flex;
        gap: 8px;
        z-index: 1;
    }

    .header-btn {
        background: rgba(255,255,255,0.2);
        border: none;
        color: white;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        cursor: pointer;
        font-size: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        backdrop-filter: blur(10px);
    }

    .header-btn:hover {
        background: rgba(255,255,255,0.3);
        transform: scale(1.1);
    }

    .search-container {
        padding: 12px 14px;
        background-color: var(--sidebar-bg);
        box-shadow: var(--shadow-sm);
        z-index: 1;
        flex-shrink: 0;
    }

    .search-box {
        background-color: var(--active-chat);
        border-radius: 10px;
        display: flex;
        align-items: center;
        padding: 0 14px;
        height: 42px;
        transition: all 0.3s ease;
        border: 2px solid transparent;
    }

    .search-box:focus-within {
        border-color: var(--p1);
        box-shadow: 0 0 0 3px rgba(57, 166, 22, 0.1);
    }

    .search-box i {
        color: var(--text-secondary);
        font-size: 0.95rem;
        transition: color 0.3s ease;
    }

    .search-box:focus-within i {
        color: var(--p1);
    }

    .search-box input {
        border: none;
        background: transparent;
        width: 100%;
        outline: none;
        margin-left: 10px;
        font-size: 13px;
        color: var(--text-primary);
        font-weight: 500;
    }

    .search-box input::placeholder {
        color: var(--text-secondary);
        font-weight: 400;
    }

    #searchResults {
        background: var(--sidebar-bg);
        border-radius: 10px;
        margin-top: 8px;
        max-height: 200px;
        overflow-y: auto;
        display: none;
        box-shadow: var(--shadow-md);
        animation: slideDown 0.3s ease;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .search-item {
        padding: 10px 14px;
        display: flex;
        align-items: center;
        gap: 10px;
        cursor: pointer;
        border-bottom: 1px solid var(--border-color);
        transition: all 0.2s ease;
    }

    .search-item:last-child {
        border-bottom: none;
    }

    .search-item:hover {
        background-color: var(--active-chat);
        padding-left: 18px;
    }

    .search-item img {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid var(--border-color);
    }

    .search-item-info {
        flex: 1;
    }

    .search-item-name {
        font-weight: 600;
        color: var(--text-primary);
        font-size: 13px;
    }

    .search-item-role {
        font-size: 11px;
        color: var(--text-secondary);
        margin-top: 2px;
    }

    .contact-list {
        flex: 1;
        overflow-y: auto;
        overflow-x: hidden;
    }

    .contact-list::-webkit-scrollbar,
    .messages-container::-webkit-scrollbar {
        width: 5px;
    }

    .contact-list::-webkit-scrollbar-track,
    .messages-container::-webkit-scrollbar-track {
        background: transparent;
    }

    .contact-list::-webkit-scrollbar-thumb,
    .messages-container::-webkit-scrollbar-thumb {
        background: var(--border-color);
        border-radius: 10px;
    }

    .contact-list::-webkit-scrollbar-thumb:hover,
    .messages-container::-webkit-scrollbar-thumb:hover {
        background: var(--text-secondary);
    }

    .contact-item {
        display: flex;
        padding: 12px 14px;
        cursor: pointer;
        border-bottom: 1px solid var(--border-color);
        align-items: center;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }

    .contact-item::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        height: 100%;
        width: 3px;
        background: var(--p1);
        transform: scaleY(0);
        transition: transform 0.3s ease;
    }

    .contact-item:hover {
        background-color: var(--active-chat);
        padding-left: 18px;
    }

    .contact-item.active {
        background: linear-gradient(90deg, rgba(57, 166, 22, 0.08) 0%, transparent 100%);
    }

    .contact-item.active::before {
        transform: scaleY(1);
    }

    .contact-avatar {
        position: relative;
        margin-right: 12px;
    }

    .contact-avatar img {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid var(--border-color);
        transition: all 0.3s ease;
    }

    .contact-item:hover .contact-avatar img,
    .contact-item.active .contact-avatar img {
        border-color: var(--p1);
        box-shadow: 0 0 0 3px rgba(57, 166, 22, 0.1);
    }

    .status-indicator {
        position: absolute;
        bottom: 2px;
        right: 2px;
        width: 11px;
        height: 11px;
        background: #4ade80;
        border: 2px solid var(--sidebar-bg);
        border-radius: 50%;
        animation: blink 2s ease-in-out infinite;
    }

    @keyframes blink {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }

    .contact-info {
        flex: 1;
        overflow: hidden;
    }

    .contact-top {
        display: flex;
        justify-content: space-between;
        margin-bottom: 3px;
        align-items: center;
    }

    .contact-name {
        font-weight: 600;
        font-size: 14px;
        color: var(--text-primary);
    }

    .contact-time {
        font-size: 10px;
        color: var(--text-secondary);
        font-weight: 500;
    }

    .contact-bottom {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .last-message {
        font-size: 12px;
        color: var(--text-secondary);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 180px;
    }

    .unread-badge {
        background: linear-gradient(135deg, var(--p1), var(--p2));
        color: white;
        font-size: 10px;
        border-radius: 10px;
        min-width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        padding: 0 5px;
        box-shadow: 0 2px 8px rgba(57, 166, 22, 0.3);
        animation: pop 0.3s ease;
    }

    @keyframes pop {
        0% { transform: scale(0); }
        50% { transform: scale(1.2); }
        100% { transform: scale(1); }
    }

    .chat-area {
        flex: 1;
        display: flex;
        flex-direction: column;
        height: 100%;
        position: relative;
        background-color: var(--chat-bg);
        background-image: url("https://user-images.githubusercontent.com/15075759/28719144-86dc0f70-73b1-11e7-911d-60d70fcded21.png");
        opacity: 0.98;
        overflow: hidden;
    }

    .chat-header {
        height: 65px;
        background: linear-gradient(135deg, var(--p1), var(--p2));
        padding: 0 18px;
        display: flex;
        align-items: center;
        box-shadow: var(--shadow-md);
        z-index: 10;
        flex-shrink: 0;
        position: relative;
        overflow: hidden;
    }

    .chat-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -5%;
        width: 150px;
        height: 150px;
        background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 70%);
        border-radius: 50%;
    }

    .btn-back {
        display: none;
        margin-right: 14px;
        color: white;
        font-size: 18px;
        cursor: pointer;
        background: rgba(255,255,255,0.2);
        width: 34px;
        height: 34px;
        border-radius: 50%;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        z-index: 1;
    }

    .btn-back:hover {
        background: rgba(255,255,255,0.3);
        transform: scale(1.1);
    }

    .chat-header-avatar {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        object-fit: cover;
        margin-right: 14px;
        border: 3px solid rgba(255,255,255,0.5);
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        z-index: 1;
    }

    .chat-header-info {
        flex: 1;
        z-index: 1;
    }

    .chat-header-info h4 {
        margin: 0;
        font-size: 16px;
        font-weight: 700;
        color: white;
    }

    .chat-header-info p {
        margin: 2px 0 0 0;
        font-size: 11px;
        color: rgba(255,255,255,0.9);
        font-weight: 500;
    }

    .chat-header-actions {
        display: flex;
        gap: 8px;
        z-index: 1;
    }

    .chat-action-btn {
        background: rgba(255,255,255,0.2);
        border: none;
        color: white;
        width: 34px;
        height: 34px;
        border-radius: 50%;
        cursor: pointer;
        font-size: 0.95rem;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        backdrop-filter: blur(10px);
    }

    .chat-action-btn:hover {
        background: rgba(255,255,255,0.3);
        transform: scale(1.1);
    }

    .messages-container {
        flex: 1;
        padding: 20px;
        overflow-y: auto;
        overflow-x: hidden;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .message-bubble {
        max-width: 65%;
        padding: 9px 13px;
        border-radius: 10px;
        position: relative;
        font-size: 13.5px;
        line-height: 1.4;
        box-shadow: 0 1px 2px rgba(0,0,0,0.1);
        word-wrap: break-word;
        animation: messageSlide 0.3s ease;
    }

    @keyframes messageSlide {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .msg-me {
        align-self: flex-end;
        background: linear-gradient(135deg, #d9fdd3, #c8f0c2);
        border-top-right-radius: 3px;
        border: 1px solid rgba(57, 166, 22, 0.1);
    }

    .msg-them {
        align-self: flex-start;
        background-color: var(--their-msg-bg);
        border-top-left-radius: 3px;
        border: 1px solid var(--border-color);
    }

    .msg-time {
        float: right;
        margin-left: 10px;
        margin-top: 3px;
        font-size: 9px;
        color: var(--text-secondary);
        display: flex;
        align-items: center;
        gap: 3px;
        font-weight: 500;
    }

    .tick-read { 
        color: #4ade80;
        font-size: 11px;
    }
    
    .tick-sent { 
        color: var(--text-secondary);
        font-size: 11px;
    }

    .msg-image img {
        max-width: 100%;
        border-radius: 8px;
        margin-bottom: 5px;
        max-height: 300px;
        object-fit: contain;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .msg-image img:hover {
        transform: scale(1.02);
        box-shadow: 0 8px 20px rgba(0,0,0,0.2);
    }

    .media-preview-container {
        padding: 12px 16px;
        background: var(--active-chat);
        border-top: 2px solid var(--p1);
        display: none;
        animation: slideUpMedia 0.3s ease;
        flex-shrink: 0;
    }

    @keyframes slideUpMedia {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .media-preview-box {
        position: relative;
        display: inline-block;
    }

    .media-preview-box img {
        height: 100px;
        border-radius: 10px;
        border: 3px solid var(--p1);
        box-shadow: var(--shadow-md);
    }

    .btn-close-preview {
        position: absolute;
        top: -8px;
        right: -8px;
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: white;
        border: 2px solid white;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        cursor: pointer;
        font-size: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.4);
    }

    .btn-close-preview:hover {
        transform: scale(1.15);
        background: linear-gradient(135deg, #dc2626, #b91c1c);
    }

    .preview-label {
        font-size: 11px;
        margin-top: 6px;
        color: var(--text-secondary);
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .preview-label i {
        color: var(--p1);
    }

    .chat-footer {
        padding: 12px 16px;
        background-color: var(--active-chat);
        display: flex;
        align-items: center;
        gap: 10px;
        flex-shrink: 0;
        box-shadow: 0 -2px 10px rgba(0,0,0,0.05);
        min-height: 65px;
    }

    .footer-btn {
        background: none;
        border: none;
        font-size: 22px;
        color: var(--text-secondary);
        cursor: pointer;
        padding: 6px;
        transition: all 0.3s ease;
        border-radius: 50%;
        width: 38px;
        height: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .footer-btn:hover {
        color: var(--p1);
        background: rgba(57, 166, 22, 0.1);
        transform: scale(1.1);
    }

    .input-wrapper {
        flex: 1;
        background-color: var(--sidebar-bg);
        border-radius: 20px;
        padding: 10px 16px;
        display: flex;
        align-items: center;
        border: 2px solid transparent;
        transition: all 0.3s ease;
        box-shadow: var(--shadow-sm);
    }

    .input-wrapper:focus-within {
        border-color: var(--p1);
        box-shadow: 0 0 0 3px rgba(57, 166, 22, 0.1);
    }

    .input-wrapper input {
        width: 100%;
        border: none;
        outline: none;
        font-size: 14px;
        background: transparent;
        color: var(--text-primary);
        font-family: inherit;
        font-weight: 500;
    }

    .input-wrapper input::placeholder {
        color: var(--text-secondary);
        font-weight: 400;
    }

    .btn-send {
        background: linear-gradient(135deg, var(--p1), var(--p2));
        border: none;
        color: white;
        font-size: 18px;
        cursor: pointer;
        width: 42px;
        height: 42px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 4px 12px rgba(57, 166, 22, 0.3);
        opacity: 0.6;
        transform: scale(0.9);
        flex-shrink: 0;
    }
    
    .btn-send.active {
        opacity: 1;
        transform: scale(1);
    }

    .btn-send:hover {
        transform: scale(1.1);
        box-shadow: 0 6px 20px rgba(57, 166, 22, 0.5);
    }

    .empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        background: linear-gradient(135deg, rgba(57, 166, 22, 0.03), rgba(29, 130, 8, 0.05));
        text-align: center;
        color: var(--text-secondary);
        position: relative;
        overflow: hidden;
        padding: 30px 20px;
    }

    .empty-state::before {
        content: '';
        position: absolute;
        width: 350px;
        height: 350px;
        background: radial-gradient(circle, rgba(57, 166, 22, 0.1) 0%, transparent 70%);
        border-radius: 50%;
        animation: pulse 4s ease-in-out infinite;
    }

    .empty-state-icon {
        font-size: 80px;
        margin-bottom: 20px;
        background: linear-gradient(135deg, var(--p1), var(--p2));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        animation: float 3s ease-in-out infinite;
        position: relative;
        z-index: 1;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-15px); }
    }

    .empty-state h2 {
        font-size: 1.6rem;
        font-weight: 800;
        margin-bottom: 10px;
        color: var(--text-primary);
        position: relative;
        z-index: 1;
    }

    .empty-state p {
        font-size: 0.9rem;
        line-height: 1.5;
        max-width: 450px;
        color: var(--text-secondary);
        position: relative;
        z-index: 1;
    }

    .empty-state-features {
        display: flex;
        gap: 16px;
        margin-top: 24px;
        position: relative;
        z-index: 1;
        flex-wrap: wrap;
        justify-content: center;
    }

    .feature-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 6px;
        padding: 12px;
        background: var(--sidebar-bg);
        border-radius: 10px;
        box-shadow: var(--shadow-sm);
        transition: all 0.3s ease;
        min-width: 100px;
    }

    .feature-item:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-md);
    }

    .feature-item i {
        font-size: 24px;
        color: var(--p1);
    }

    .feature-item span {
        font-size: 11px;
        font-weight: 600;
        color: var(--text-primary);
    }

    .loading-state {
        text-align: center;
        padding: 20px;
        color: var(--text-secondary);
    }

    .loading-spinner {
        display: inline-block;
        width: 35px;
        height: 35px;
        border: 3px solid var(--border-color);
        border-top-color: var(--p1);
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    @media (max-width: 768px) {
        body {
            background: var(--sidebar-bg);
        }

        .chat-wrapper {
            margin: 0;
            padding: 0;
        }

        .app-container {
            margin: 0;
            border-radius: 0;
            max-width: 100%;
            height: 100vh;
            max-height: 100vh;
            min-height: 100vh;
        }

        .sidebar-area {
            width: 100%;
            max-width: none;
            min-width: auto;
        }

        .chat-area {
            display: none;
            width: 100%;
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 10;
        }
        
        .app-container.chat-open .sidebar-area {
            display: none;
        }

        .app-container.chat-open .chat-area {
            display: flex;
        }

        .btn-back {
            display: flex !important;
        }

        .message-bubble {
            max-width: 75%;
        }

        .sidebar-header h3 {
            font-size: 1.2rem;
        }

        .sidebar-header h3 i {
            font-size: 1.3rem;
            padding: 6px;
        }

        .empty-state h2 {
            font-size: 1.3rem;
        }

        .empty-state-icon {
            font-size: 60px;
        }

        .empty-state p {
            font-size: 0.85rem;
        }

        .empty-state-features {
            flex-direction: column;
            width: 100%;
            max-width: 300px;
        }

        .feature-item {
            width: 100%;
        }

        .contact-name {
            font-size: 13px;
        }

        .last-message {
            max-width: 140px;
            font-size: 11px;
        }

        .chat-footer {
            padding: 10px 12px;
        }

        .input-wrapper {
            padding: 8px 14px;
        }
    }

    [data-theme="dark"] .msg-me {
        background: linear-gradient(135deg, #005c4b, #004d3f);
    }

    [data-theme="dark"] .search-item img,
    [data-theme="dark"] .contact-avatar img {
        border-color: var(--border-color);
    }

    [data-theme="dark"] .empty-state {
        background: linear-gradient(135deg, rgba(57, 166, 22, 0.05), rgba(29, 130, 8, 0.08));
    }
</style>
@endpush

@section('content')
<div class="chat-wrapper">
    <div class="app-container" id="appContainer">
        
        <div class="sidebar-area">
            <div class="sidebar-header" style="padding:16px; background:linear-gradient(135deg, #39A616, #1D8208); color:white; display:flex; justify-content:space-between; align-items:center;">
                <h3 style="margin:0; font-size:1.2rem;"><i class="fas fa-comments"></i> Chat Konsultasi</h3>
                <button class="header-btn" title="Refresh" onclick="loadContacts()" style="background:rgba(255,255,255,0.2); border:none; color:white; border-radius:50%; width:36px; height:36px; cursor:pointer;"><i class="fas fa-sync-alt"></i></button>
            </div>

            <div class="search-container" style="padding:10px;">
                <input type="text" id="contactSearch" placeholder="{{ $myRole === 'medis' ? 'Cari Pasien...' : 'Cari Dokter...' }}" style="width:100%; padding:10px; border-radius:8px; border:1px solid #ddd;">
                <div id="searchResults" style="display:none; background:white; border:1px solid #eee; margin-top:5px;"></div>
            </div>

            <div class="contact-list" id="contactList" style="flex:1; overflow-y:auto;">
                <div class="loading-state" style="padding:20px; text-align:center; color:#999;">Memuat percakapan...</div>
            </div>
        </div>

        <div class="chat-area" id="chatArea">
            <div id="emptyState" class="empty-state" style="display:flex; flex-direction:column; align-items:center; justify-content:center; height:100%; color:#888;">
                <i class="fas fa-comment-medical" style="font-size:60px; margin-bottom:20px; color:#39A616;"></i>
                <h2>MedEase Chat</h2>
                <p>Pilih kontak untuk memulai percakapan.</p>
            </div>

            <div id="activeChatView" style="display: none; flex-direction: column; height: 100%;">
                <div class="chat-header" style="height:65px; background:linear-gradient(135deg, #39A616, #1D8208); padding:0 20px; display:flex; align-items:center; color:white;">
                    <i class="fas fa-arrow-left btn-back" onclick="closeChat()" style="margin-right:15px; cursor:pointer;"></i>
                    <img src="" alt="" class="chat-header-avatar" id="headerAvatar" style="width:40px; height:40px; border-radius:50%; margin-right:10px; background:#fff;">
                    <div class="chat-header-info">
                        <h4 id="headerName" style="margin:0; font-size:16px;">User</h4>
                    </div>
                </div>

                <div class="messages-container" id="messagesContainer" style="flex:1; overflow-y:auto; padding:20px; display:flex; flex-direction:column; gap:10px;"></div>

                <div id="mediaPreview" class="media-preview-container" style="display:none; padding:10px; background:#f0f0f0; border-top:2px solid #39A616;">
                    <div style="position:relative; display:inline-block;">
                        <img id="previewImage" src="" style="height:100px; border-radius:8px;">
                        <button onclick="clearMedia()" style="position:absolute; top:-5px; right:-5px; background:red; color:white; border:none; border-radius:50%; width:20px; height:20px; cursor:pointer;">&times;</button>
                    </div>
                </div>

                <form id="chatForm" class="chat-footer" style="padding:10px; background:#fff; display:flex; align-items:center; gap:10px;">
                    @csrf
                    <input type="file" id="mediaInput" name="media" accept="image/*" style="display: none;" onchange="handleFileSelect(this)">
                    <button type="button" onclick="document.getElementById('mediaInput').click()" style="background:none; border:none; font-size:20px; color:#666; cursor:pointer;"><i class="fas fa-camera"></i></button>
                    <input type="text" id="messageInput" placeholder="Ketik pesan..." autocomplete="off" style="flex:1; padding:10px; border-radius:20px; border:1px solid #ddd;">
                    <button type="submit" id="btnSend" style="background:#39A616; color:white; border:none; width:40px; height:40px; border-radius:50%; cursor:pointer;"><i class="fas fa-paper-plane"></i></button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let activePartnerId = null;
    let lastMessageId = 0;
    const isDoctor = {{ $myRole === 'medis' ? 'true' : 'false' }};
    const csrfToken = '{{ csrf_token() }}';
    let pollingInterval = null;
    let messageIds = new Set(); 

    // 🔥 URL DINAMIS BERDASARKAN PREFIX 🔥
    const urls = {
        contacts: "{{ route($routePrefix . 'contacts') }}",
        search: "{{ $myRole === 'medis' ? route($routePrefix . 'search') : '' }}",
        messages: "{{ url($myRole === 'medis' ? 'tenaga-medis/chat/messages' : 'chat/messages') }}", 
        send: "{{ route($routePrefix . 'send') }}"
    };

    function loadContacts() {
        fetch(urls.contacts)
            .then(res => res.json())
            .then(data => {
                const list = document.getElementById('contactList');
                list.innerHTML = ''; 
                if(data.length === 0) {
                    const msg = isDoctor ? 'Belum ada pesan. Cari pasien...' : 'Belum ada dokter.';
                    list.innerHTML = `<div style="padding:40px 20px; text-align:center; color:#999;">${msg}</div>`;
                    return;
                }
                data.forEach(c => {
                    const activeClass = (c.id == activePartnerId) ? 'active' : '';
                    const avatar = c.avatar || `https://ui-avatars.com/api/?name=${c.name}&background=39A616&color=fff`;
                    const time = c.last_time || '';
                    const lastMsg = c.last_message || '...';
                    const badge = c.unread > 0 ? `<div class="unread-badge" style="background:red; color:white; border-radius:10px; padding:2px 6px; font-size:10px;">${c.unread}</div>` : '';
                    
                    // Simple template
                    list.innerHTML += `
                    <div class="contact-item ${activeClass}" onclick="openChat(${c.id}, '${escapeHtml(c.name)}', '${avatar}')" style="padding:10px; border-bottom:1px solid #eee; cursor:pointer; display:flex; align-items:center;">
                        <img src="${avatar}" style="width:40px; height:40px; border-radius:50%; margin-right:10px;">
                        <div style="flex:1;">
                            <div style="display:flex; justify-content:space-between; font-size:14px; font-weight:bold;">${escapeHtml(c.name)} <span style="font-weight:normal; font-size:11px; color:#888;">${time}</span></div>
                            <div style="display:flex; justify-content:space-between; font-size:12px; color:#666;"><span>${lastMsg}</span> ${badge}</div>
                        </div>
                    </div>`;
                });
            });
    }

    function openChat(id, name, avatar) {
        if(activePartnerId === id) return;
        activePartnerId = id;
        lastMessageId = 0;
        messageIds.clear();
        
        document.getElementById('appContainer').classList.add('chat-open');
        document.getElementById('emptyState').style.display = 'none';
        document.getElementById('activeChatView').style.display = 'flex';
        document.getElementById('headerName').innerText = name;
        document.getElementById('headerAvatar').src = avatar;
        document.getElementById('messagesContainer').innerHTML = '<div style="text-align:center; padding:20px;">Memuat...</div>';
        
        fetchMessages(id, true);
    }

    function fetchMessages(partnerId, isInitial = false) {
        let url = `${urls.messages}/${partnerId}`;
        if (!isInitial) url += `?last_id=${lastMessageId}`;
        
        fetch(url).then(res => res.json()).then(data => {
            if(isInitial) renderMessagesInitial(data.messages);
            else if (data.messages.length > 0) appendMessages(data.messages);
            
            if (data.messages.length > 0) lastMessageId = data.messages[data.messages.length - 1].id;
        });
    }

    function renderMessagesInitial(messages) {
        const container = document.getElementById('messagesContainer');
        container.innerHTML = ''; messageIds.clear();
        messages.forEach(msg => { if (!messageIds.has(msg.id)) { messageIds.add(msg.id); container.appendChild(createBubble(msg)); } });
        scrollToBottom();
    }

    function appendMessages(messages) {
        const container = document.getElementById('messagesContainer');
        messages.forEach(msg => { if (!messageIds.has(msg.id)) { messageIds.add(msg.id); container.appendChild(createBubble(msg)); } });
        if(messages.length > 0) scrollToBottom(true);
    }

    function createBubble(msg) {
        const div = document.createElement('div');
        const isMe = msg.sender === 'me';
        div.style.cssText = `max-width:70%; padding:8px 12px; border-radius:8px; font-size:13px; margin-bottom:5px; align-self:${isMe ? 'flex-end' : 'flex-start'}; background:${isMe ? '#d9fdd3' : '#fff'}; box-shadow:0 1px 2px rgba(0,0,0,0.1);`;
        
        let content = '';
        if (msg.media_path) content += `<div style="margin-bottom:5px;"><a href="/storage/${msg.media_path}" target="_blank">Lihat Media</a></div>`;
        content += `<div>${escapeHtml(msg.message || '')}</div><div style="text-align:right; font-size:9px; color:#888;">${msg.time}</div>`;
        
        div.innerHTML = content;
        return div;
    }

    document.getElementById('chatForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const text = document.getElementById('messageInput').value.trim();
        const file = document.getElementById('mediaInput').files[0];
        if((!text && !file) || !activePartnerId) return;

        const formData = new FormData();
        formData.append('receiver_id', activePartnerId);
        formData.append('_token', csrfToken);
        if(text) formData.append('message', text);
        if(file) formData.append('media', file);

        document.getElementById('messageInput').value = '';
        clearMedia();

        fetch(urls.send, { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => { if(data.success) { setTimeout(() => { fetchMessages(activePartnerId); loadContacts(); }, 200); } });
    });

    function handleFileSelect(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('previewImage').src = e.target.result;
                document.getElementById('mediaPreview').style.display = 'block';
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
    function clearMedia() { document.getElementById('mediaInput').value = ''; document.getElementById('mediaPreview').style.display = 'none'; }
    function closeChat() { document.getElementById('activeChatView').style.display = 'none'; document.getElementById('emptyState').style.display = 'flex'; activePartnerId = null; }
    function scrollToBottom(smooth) { const c = document.getElementById('messagesContainer'); c.scrollTop = c.scrollHeight; }
    function escapeHtml(text) { if(!text) return ''; return text.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;"); }

    // Search Logic (Only for Doctor)
    if(isDoctor) {
        document.getElementById('contactSearch').addEventListener('keyup', function() {
            const q = this.value;
            if(q.length < 2) { document.getElementById('searchResults').style.display = 'none'; return; }
            fetch(`${urls.search}?q=${q}`).then(res=>res.json()).then(data => {
                const resDiv = document.getElementById('searchResults');
                resDiv.innerHTML = ''; resDiv.style.display = 'block';
                data.forEach(u => {
                    resDiv.innerHTML += `<div onclick="openChat(${u.id}, '${escapeHtml(u.name)}', '')" style="padding:10px; cursor:pointer; border-bottom:1px solid #eee;">${escapeHtml(u.name)}</div>`;
                });
            });
        });
    }

    loadContacts();
    pollingInterval = setInterval(() => { loadContacts(); if(activePartnerId) fetchMessages(activePartnerId); }, 5000);
</script>
@endpush