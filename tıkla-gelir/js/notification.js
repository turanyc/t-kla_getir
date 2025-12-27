// js/notification.js - Kral Kurye Bildirim Sistemi v3.0

// ==================== KONFIGÜRASYON ====================
const CONFIG = {
    POLLING_INTERVAL: 3000, // 3 saniye (daha hafif)
    SOUND_ENABLED: true,
    DESKTOP_NOTIFICATIONS: true,
    AUTO_REMOVE_TIME: 8000, // 8 saniye sonra kapanır
    API_ENDPOINT: '/api/notifications.php',
    SOUND_FILE: '/assets/new_order.mp3',
    LOGO_FILE: '/assets/logo.png'
};

// ==================== DEĞİŞKENLER ====================
let lastId = parseInt(localStorage.getItem('lastNotificationId') || '0');
const sound = new Audio(CONFIG.SOUND_FILE);
sound.volume = 0.6;
sound.preload = 'auto';

// ==================== BAŞLATMA ====================
document.addEventListener('DOMContentLoaded', function() {
    // Bildirim izni iste
    if (CONFIG.DESKTOP_NOTIFICATIONS && Notification.permission === "default") {
        Notification.requestPermission();
    }
    
    // İlk kontrol
    checkNotifications();
    
    // Düzenli kontrol
    setInterval(checkNotifications, CONFIG.POLLING_INTERVAL);
    
    // CSS ekle (ilk defa)
    injectNotificationCSS();
});

// ==================== ANA FONKSİYON ====================
async function checkNotifications() {
    try {
        const response = await fetch(`${CONFIG.API_ENDPOINT}?last_id=${lastId}`, {
            credentials: 'include',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }

        const data = await response.json();
        
        if (!data.success) {
            console.warn("Bildirim hatası:", data.message);
            return;
        }

        // Yeni bildirimler varsa işle
        if (data.notifications && data.notifications.length > 0) {
            for (const notif of data.notifications) {
                showNotification(notif);
            }

            // Ses çalma kontrolü
            if (CONFIG.SOUND_ENABLED && data.sound) {
                playAlertSound();
            }

            // Last ID güncelle
            lastId = data.highest_id;
            localStorage.setItem('lastNotificationId', lastId.toString());
        }

    } catch (error) {
        console.error("Bildirim kontrol hatası:", error);
        // 5 dakika sonra tekrar dene (hata durumunda)
        setTimeout(checkNotifications, 300000);
    }
}

// ==================== GÖSTERİM FONKSİYONLARI ====================
function showNotification(notif) {
    const popup = document.createElement('div');
    popup.id = `notif-${notif.id}`;
    popup.className = `notification-popup type-${notif.type || 'info'}`;
    popup.innerHTML = `
        <div class="notification-header">
            <div class="icon">${getIcon(notif.type)}</div>
            <div class="title">${escapeHtml(notif.title || 'Bildirim')}</div>
            <button class="close-btn" onclick="removeNotification(${notif.id})" title="Kapat">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="notification-body">
            ${notif.sender_name ? `<strong>${escapeHtml(notif.sender_name)}</strong><br>` : ''}
            ${escapeHtml(notif.message || 'Yeni bildirim geldi!')}<br>
            <small class="time">${formatTime(notif.created_at)}</small>
        </div>
    `;

    document.body.appendChild(popup);

    // Otomatik kaldırma
    if (CONFIG.AUTO_REMOVE_TIME > 0) {
        setTimeout(() => removeNotification(notif.id), CONFIG.AUTO_REMOVE_TIME);
    }

    // Animasyon ekle
    setTimeout(() => popup.classList.add('show'), 100);
}

function removeNotification(id) {
    const popup = document.getElementById(`notif-${id}`);
    if (popup) {
        popup.classList.remove('show');
        setTimeout(() => popup.remove(), 300);
    }
}

// ==================== YARDIMCI FONKSİYONLAR ====================
function playAlertSound() {
    try {
        sound.currentTime = 0;
        sound.play().catch(e => console.log("Ses çalma engellendi:", e));
    } catch (e) {
        console.warn("Ses dosyası hatası:", e);
    }
}

function getIcon(type) {
    const icons = {
        'order': '<i class="fas fa-shopping-cart"></i>',
        'payment': '<i class="fas fa-credit-card"></i>',
        'status': '<i class="fas fa-truck"></i>',
        'promotion': '<i class="fas fa-gift"></i>',
        'system': '<i class="fas fa-cog"></i>',
        'warning': '<i class="fas fa-exclamation-triangle"></i>',
        'info': '<i class="fas fa-info-circle"></i>'
    };
    return icons[type] || icons['info'];
}

function formatTime(timestamp) {
    if (!timestamp) return 'Az önce';
    const now = new Date();
    const notifTime = new Date(timestamp);
    const diff = Math.floor((now - notifTime) / 1000); // saniye cinsinden

    if (diff < 60) return `${diff} saniye önce`;
    if (diff < 3600) return `${Math.floor(diff / 60)} dakika önce`;
    if (diff < 86400) return `${Math.floor(diff / 3600)} saat önce`;
    return notifTime.toLocaleString('tr-TR');
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function injectNotificationCSS() {
    // CSS daha önce eklenmedi mi kontrol et
    if (document.getElementById('notification-css')) return;

    const style = document.createElement('style');
    style.id = 'notification-css';
    style.textContent = `
        /* Bildirim Popup Stilleri */
        .notification-popup {
            position: fixed;
            top: 25px;
            right: 25px;
            width: 350px;
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
            z-index: 9999;
            transform: translateX(400px);
            transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            overflow: hidden;
            border-left: 5px solid #00C853;
            font-family: 'Poppins', sans-serif;
        }

        .notification-popup.show {
            transform: translateX(0);
        }

        .notification-header {
            display: flex;
            align-items: center;
            padding: 15px 20px;
            background: linear-gradient(135deg, #00C853, #4CAF50);
            color: white;
            font-weight: 600;
        }

        .notification-header .icon {
            margin-right: 12px;
            font-size: 18px;
        }

        .notification-header .title {
            flex: 1;
            font-size: 16px;
        }

        .notification-header .close-btn {
            background: none;
            border: none;
            color: white;
            cursor: pointer;
            font-size: 18px;
            padding: 0;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
        }

        .notification-header .close-btn:hover {
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
        }

        .notification-body {
            padding: 20px;
            color: #333;
            line-height: 1.6;
        }

        .notification-body strong {
            color: #FF6B35;
            font-size: 16px;
        }

        .notification-body .time {
            color: #999;
            font-size: 12px;
            display: block;
            margin-top: 8px;
        }

        /* Bildirim Türlerine Göre Renkler */
        .notification-popup.type-order {
            border-left-color: #00C853;
        }

        .notification-popup.type-order .notification-header {
            background: linear-gradient(135deg, #00C853, #4CAF50);
        }

        .notification-popup.type-payment {
            border-left-color: #FF6B35;
        }

        .notification-popup.type-payment .notification-header {
            background: linear-gradient(135deg, #FF6B35, #FF4500);
        }

        .notification-popup.type-status {
            border-left-color: #2196F3;
        }

        .notification-popup.type-status .notification-header {
            background: linear-gradient(135deg, #2196F3, #1976D2);
        }

        .notification-popup.type-warning {
            border-left-color: #FFC107;
        }

        .notification-popup.type-warning .notification-header {
            background: linear-gradient(135deg, #FFC107, #FF9800);
        }

        /* Mobil Uyumluluk */
        @media (max-width: 768px) {
            .notification-popup {
                width: 90%;
                right: 5%;
                left: 5%;
            }
        }

        /* Toast bildirimleri için ek stil */
        .notification-toast {
            position: fixed;
            bottom: 25px;
            right: 25px;
            background: #333;
            color: white;
            padding: 15px 20px;
            border-radius: 10px;
            z-index: 9999;
            transform: translateY(100px);
            transition: transform 0.3s;
        }

        .notification-toast.show {
            transform: translateY(0);
        }
    `;
    
    document.head.appendChild(style);
}