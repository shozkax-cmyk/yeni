<?php

// IP tespiti
$ip = $_SERVER['REMOTE_ADDR'];
if ($ip === "::1") {
    $ip = "127.0.0.1"; // Localhost düzeltme
}

// ipinfo.io’dan veri çek
$details = @json_decode(file_get_contents("https://ipinfo.io/{$ip}/json"));

// IP bilgileri
$hostname = gethostbyaddr($ip);
$city     = $details->city ?? "Bilinmiyor";
$region   = $details->region ?? "Bilinmiyor";
$country  = $details->country ?? "Bilinmiyor";
$loc      = $details->loc ?? "Bilinmiyor";

// Saat dilimi ayarı
date_default_timezone_set("Europe/Istanbul");
$now = date("d.m.Y H:i:s");

// Session ile giriş/çıkış zamanlarını takip et
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['start_time'])) {
    $_SESSION['start_time'] = $now;
}
$_SESSION['last_active'] = $now;

// IP bilgilerini HTML dosyasına yaz
$log = "
IP: $ip
Hostname: $hostname
Şehir: $city
Bölge: $region
Ülke: $country
Konum: $loc
Start Time: {$_SESSION['start_time']}
Last Active: {$_SESSION['last_active']}
-----------------------------
";

file_put_contents("ip.html", nl2br($log), FILE_APPEND);
?>

<?php
require_once 'config.php';
require_once 'functions.php';

$page_title = 'Ana Sayfa';

// Handle new confession submission
// Handle new confession submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_confession'])) {
    if (!isLoggedIn()) {
        $_SESSION['error'] = 'İtiraf göndermek için giriş yapmalısınız.';
        header('Location: login.php');
        exit;
    }
    
    $text = trim($_POST['confession_text'] ?? '');
    $isAnonymous = isset($_POST['is_anonymous']) ? 1 : 0;
    
    if (empty($text)) {
        $_SESSION['error'] = 'İtiraf metni boş olamaz.';
    } else {
        // Handle image upload
        $imagePath = null;
        if (isset($_FILES['confession_image']) && $_FILES['confession_image']['error'] === UPLOAD_ERR_OK) {
            $uploadResult = handleImageUpload($_FILES['confession_image']);
            if ($uploadResult['success']) {
                $imagePath = $uploadResult['filename'];
            } else {
                $_SESSION['error'] = $uploadResult['message'];
            }
        }
        
        // Extract style information (if rich text editor is used)
        $style = null;
        if (isset($_POST['confession_style']) && !empty($_POST['confession_style'])) {
            $style = json_decode($_POST['confession_style'], true);
        }

        // Only create confession if no error occurred
        if (!isset($_SESSION['error'])) {
            $result = $db->createConfession($_SESSION['user_id'], $text, $style, $imagePath, $isAnonymous);
            
            if ($result) {
                $_SESSION['success'] = 'İtirafınız başarıyla gönderildi!';
                header('Location: index.php');
                exit;
            } else {
                // Debug için SQL hatasını göster
                if (method_exists($db, 'errorInfo')) {
                    $errorInfo = $db->errorInfo();
                    $_SESSION['error'] = 'İtiraf gönderilemedi. SQL Hata: ' . implode(' | ', $errorInfo);
                } else {
                    $_SESSION['error'] = 'İtiraf gönderilemedi.';
                }
            }
        }
    }
}

// Get current page
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;

// Admin tüm itirafları görür, diğer kullanıcılar sadece hidden = 0 olanları
$confessions = isAdmin() 
    ? $db->getConfessions($page, CONFESSIONS_PER_PAGE, true) 
    : $db->getConfessions($page, CONFESSIONS_PER_PAGE, false);

$totalConfessions = isAdmin() 
    ? $db->getTotalConfessions() 
    : $db->getTotalConfessions(0);

// Calculate pagination
$totalPages = ceil($totalConfessions / CONFESSIONS_PER_PAGE);

include 'includes/header.php';
?>

<!-- Hero Section -->
 
<?php
/*
<div class="hero-section" style="background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary)); color: white; padding: 3rem 0; margin-bottom: 2rem; border-radius: var(--border-radius); text-align: center;">
    <h1 style="font-size: 2.5rem; margin-bottom: 1rem; font-weight: 700;">
        🗣️ Confession Hub'a Hoş Geldiniz
    </h1>
    <p style="font-size: 1.2rem; opacity: 0.9; max-width: 600px; margin: 0 auto 2rem;">
        Düşüncelerinizi güvenle paylaşın, toplulukla bağlantı kurun ve anonim kalmanın özgürlüğünü yaşayın.
    </p>
    <?php if (!isLoggedIn()): ?>
        <div class="hero-actions" style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
            <a href="register.php" class="btn btn-secondary" style="background: white; color: var(--accent-primary);">
                📝 Hemen Kayıt Ol
            </a>
            <a href="login.php" class="btn btn-secondary" style="background: rgba(255,255,255,0.2); color: white; border: 1px solid white;">
                🔐 Giriş Yap
            </a>
        </div>
    <?php endif; ?>
</div>
*/
?>

<!-- New Confession Form -->
<?php if (isLoggedIn()): ?>
<div class="new-confession-section" style="margin-bottom: 2rem;">
    <div class="confession-card">
        <h2 style="margin-bottom: 1.5rem; color: var(--accent-primary); font-size: 1.5rem;">
            ✍️ Yeni İtiraf Paylaş
        </h2>
        
        <form method="POST" enctype="multipart/form-data" id="confession-form">
            <!-- Rich Text Editor Container -->
            <div class="form-group">
                <label class="form-label">İtiraf Metni:</label>
                <div id="confession-editor"></div>
<textarea name="confession_text" id="confession-text-hidden" style="display: none;"></textarea>
                <input type="hidden" name="confession_style" id="confession-style-hidden">
            </div>
            
            <!-- Image Upload -->
            <div class="form-group">
                <label class="form-label">Resim (İsteğe bağlı):</label>
                <div class="file-upload">
                    <input type="file" name="confession_image" id="confession-image" class="file-upload-input" accept="image/*">
                    <label for="confession-image" class="file-upload-label">
                        📷 Resim Seç (Maksimum 10MB)
                    </label>
                </div>
                <div id="confession-image-preview" style="display: none;"></div>
            </div>
            
            <!-- Anonymous Option -->
            <div class="form-group">
                <label class="form-label" style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                    <input type="checkbox" name="is_anonymous" value="1" style="width: auto;">
                    🕶️ Anonim olarak paylaş (kullanıcı adın gözükmez)
                </label>
            </div>
            
            <div class="form-actions" style="display: flex; gap: 1rem; justify-content: flex-end;">
                <button type="button" onclick="clearConfessionForm()" class="btn btn-secondary">
                    🗑️ Temizle
                </button>
                <button type="submit" name="submit_confession" class="btn btn-primary">
                    🚀 İtirafı Paylaş
                </button>
            </div>
        </form>
    </div>
</div>
<?php else: ?>
<div class="login-prompt" style="margin-bottom: 2rem;">
    <div class="confession-card" style="text-align: center; background: linear-gradient(135deg, var(--bg-card), var(--bg-hover));">
        <h2 style="color: var(--accent-primary); margin-bottom: 1rem;">🔐 İtiraf Paylaşmak İçin Giriş Yapın</h2>
        <p style="color: var(--text-secondary); margin-bottom: 1.5rem;">
            Toplulukla düşüncelerinizi paylaşmak ve yorumlar yapmak için üye olun.
        </p>
        <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
            <a href="login.php" class="btn btn-primary">🔐 Giriş Yap</a>
            <a href="register.php" class="btn btn-secondary">📝 Kayıt Ol</a>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Confessions List -->
<div class="confessions-section">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <h2 style="color: var(--text-primary); font-size: 1.5rem;">
            💬 Güncel İtiraflar 
            <span style="color: var(--text-muted); font-size: 1rem; font-weight: normal;">
                (<?php echo $totalConfessions; ?> toplam)
            </span>
        </h2>
        
        <div class="confession-filters" style="display: flex; gap: 0.5rem;">
            <select onchange="filterConfessions(this.value)" class="form-select" style="width: auto;">
                <option value="newest">En Yeni</option>
                <option value="oldest">En Eski</option>
                <option value="most_commented">En Çok Yorumlanan</option>
            </select>
        </div>
    </div>
    
    <?php if (empty($confessions)): ?>
        <div class="confession-card" style="text-align: center;">
            <h3 style="color: var(--text-muted);">📭 Henüz hiç itiraf paylaşılmamış</h3>
            <p style="color: var(--text-secondary);">İlk itirafı siz paylaşmaya ne dersiniz?</p>
            <?php if (isLoggedIn()): ?>
                <button onclick="document.querySelector('#confession-editor .editor-content').focus()" class="btn btn-primary" style="margin-top: 1rem;">
                    ✍️ İlk İtirafı Paylaş
                </button>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <?php foreach ($confessions as $confession): ?>
            <div class="confession-card fade-in-up" data-confession-id="<?php echo $confession['id']; ?>">
                <!-- Confession Header -->
                <div class="confession-header">
                    <div class="user-avatar">
                        <?php if ($confession['is_anonymous']): ?>
                            🕶️
                        <?php else: ?>
                            <?php echo strtoupper(substr($confession['username'], 0, 1)); ?>
                        <?php endif; ?>
                    </div>
                    <div class="confession-meta">
                        <h4>
                            <?php echo $confession['is_anonymous'] ? 'Anonim Kullanıcı' : htmlspecialchars($confession['username']); ?>
                            <?php if (isAdmin()): ?>
                                <small style="color: var(--text-muted); font-weight: normal;">
                                    (IP: <?php echo $confession['ip']; ?>)
                                </small>
                            <?php endif; ?>
                        </h4>
                        <div class="time">
                            🕐 <?php echo formatDate($confession['date']); ?>
                        </div>
                    </div>
                </div>
                
                <!-- Confession Content -->
<!-- Confession Content -->
<div class="confession-content">
    <?php 
        // Yalnızca belli etiketlere izin ver (i, b, u, strike)
        $allowedTags = '<i><b><u><strike>';
        $content = strip_tags($confession['text'], $allowedTags);

        // Stil bilgisi varsa uygula
        if (!empty($confession['style'])) {
            $style = json_decode($confession['style'], true);
            if ($style) {
                $styleString = '';
                if (isset($style['bold']) && $style['bold']) $content = "<strong>$content</strong>";
                if (isset($style['italic']) && $style['italic']) $content = "<em>$content</em>";
                if (isset($style['underline']) && $style['underline']) $content = "<u>$content</u>";
                if (isset($style['color'])) $styleString .= "color: {$style['color']};";
                if (isset($style['fontSize'])) $styleString .= "font-size: {$style['fontSize']}px;";
                
                if ($styleString) {
                    $content = "<span style=\"$styleString\">$content</span>";
                }
            }
        }

        echo $content;
    ?>
</div>
                
                <!-- Confession Image -->
                <?php if (!empty($confession['image'])): ?>
                    <div class="confession-image">
                        <img src="uploads/<?php echo htmlspecialchars($confession['image']); ?>" alt="Confession image" style="max-width: 100%; height: auto; border-radius: var(--border-radius-sm); box-shadow: var(--shadow-sm);">
                    </div>
                <?php endif; ?>
                
                <!-- Confession Actions -->
                <div class="confession-actions">
                    <?php if (isLoggedIn()): ?>
                        <button class="action-btn toggle-comments" data-confession-id="<?php echo $confession['id']; ?>">
                            💬 Yorumları Göster (<?php echo $confession['comments_count']; ?>)
                        </button>
                        
                        <button class="action-btn like-btn" data-confession-id="<?php echo $confession['id']; ?>">
                            ❤️ Beğen
                        </button>
                    <?php endif; ?>
                    
                    <?php if (isAdmin() || (isLoggedIn() && $_SESSION['user_id'] == $confession['user_id'])): ?>
                        <button class="action-btn" onclick="hideConfession(<?php echo $confession['id']; ?>)" style="color: var(--accent-danger);">
                            🗑️ Sil
                        </button>
                    <?php endif; ?>
                    
                    <button class="action-btn share-btn" data-confession-id="<?php echo $confession['id']; ?>">
                        📤 Paylaş
                    </button>
                </div>
                
                <!-- Comments Section -->
                <div id="comments-<?php echo $confession['id']; ?>" class="comments-section" style="display: none;">
                    <div id="comments-list-<?php echo $confession['id']; ?>">
                        <!-- Comments will be loaded here via AJAX -->
                    </div>
                    
                    <?php if (isLoggedIn()): ?>
                        <!-- Comment Form -->
                        <form class="comment-form" data-confession-id="<?php echo $confession['id']; ?>" style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid var(--border-color);">
                            <div class="form-group">
                                <textarea name="comment_text" placeholder="Yorumunuzu yazın..." class="form-textarea" style="min-height: 80px;" required></textarea>
                            </div>
                            
                            <div class="form-group">
                                <div class="file-upload">
                                    <input type="file" name="comment_image" class="file-upload-input" accept="image/*">
                                    <label class="file-upload-label">
                                        📷 Resim Ekle (İsteğe bağlı)
                                    </label>
                                </div>
                            </div>
                            
                            <div style="display: flex; justify-content: flex-end;">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    📨 Yorum Gönder
                                </button>
                            </div>
                            
                            <input type="hidden" name="confession_id" value="<?php echo $confession['id']; ?>">
                        </form>
                    <?php else: ?>
                        <div style="text-align: center; padding: 1rem; color: var(--text-muted);">
                            <p>Yorum yapmak için <a href="login.php" style="color: var(--accent-primary);">giriş yapın</a></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Pagination -->
<?php if ($totalPages > 1): ?>
<div class="pagination" style="display: flex; justify-content: center; gap: 0.5rem; margin: 2rem 0;">
    <?php if ($page > 1): ?>
        <a href="?page=<?php echo $page - 1; ?>" class="btn btn-secondary">← Önceki</a>
    <?php endif; ?>
    
    <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
        <a href="?page=<?php echo $i; ?>" class="btn <?php echo $i === $page ? 'btn-primary' : 'btn-secondary'; ?>">
            <?php echo $i; ?>
        </a>
    <?php endfor; ?>
    
    <?php if ($page < $totalPages): ?>
        <a href="?page=<?php echo $page + 1; ?>" class="btn btn-secondary">Sonraki →</a>
    <?php endif; ?>
</div>
<?php endif; ?>
<!-- JS & AJAX Kodları -->
<script>
// İtiraf formunu temizleme
function clearConfessionForm() {
    if (window.confessionEditor) {
        window.confessionEditor.setContent('');
    }
    document.getElementById('confession-text-hidden').value = '';
    document.getElementById('confession-style-hidden').value = '';
    
    // Resim alanını temizle
    const imageInput = document.getElementById('confession-image');
    const imagePreview = document.getElementById('confession-image-preview');
    if (imageInput) imageInput.value = '';
    if (imagePreview) {
        imagePreview.innerHTML = '';
        imagePreview.style.display = 'none';
    }
    
    // Anonim checkbox
    const anon = document.querySelector('input[name="is_anonymous"]');
    if (anon) anon.checked = false;
}

// Form submit işlemi
document.addEventListener('DOMContentLoaded', function() {
    const confessionForm = document.getElementById('confession-form');
    if (confessionForm) {
        confessionForm.addEventListener('submit', function(e) {
            if (window.confessionEditor) {
                const content = window.confessionEditor.getContent();
                const textContent = content.replace(/<[^>]*>/g, '').trim();
                if (!textContent) {
                    e.preventDefault();
                    alert('İtiraf metni boş olamaz!');
                    return;
                }
                document.getElementById('confession-text-hidden').value = content;
                const style = window.confessionEditor.getTextStyle();
                document.getElementById('confession-style-hidden').value = JSON.stringify(style);
            }
        });
    }
});

// İtiraf gizleme (hidden=1)
// İtiraf gizleme (silme yerine hidden=1)
function hideConfession(confessionId) {
    if (!confirm('Bu itirafı silmek istediğinize emin misiniz?')) return;

fetch('hide_confession.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({confession_id: confessionId})
})
.then(res => res.text())  // Önce text al
.then(text => {
    try {
        const data = JSON.parse(text);  // JSON parse
        if (data.success) {
            const card = document.querySelector(`[data-confession-id="${confessionId}"]`);
            if (card) card.remove();
        } else {
            alert('Hata: ' + (data.message || 'Bilinmeyen hata'));
        }
    } catch(e) {
        console.error('JSON parse hatası:', e, text);
    }
})
.catch(err => console.error('AJAX Hatası:', err));
}

// İtirafları filtreleme
function filterConfessions(filter) {
    const url = new URL(window.location);
    url.searchParams.set('filter', filter);
    window.location.href = url.toString();
}

// İtiraf paylaş linki
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('share-btn')) {
        const confessionId = e.target.dataset.confessionId;
        const shareUrl = `${window.location.origin}${window.location.pathname}#confession-${confessionId}`;
        if (navigator.share) {
            navigator.share({
                title: 'Confession Hub - İtiraf',
                text: 'Bu itirafı kontrol et!',
                url: shareUrl
            });
        } else {
            navigator.clipboard.writeText(shareUrl).then(() => {
                alert('Link kopyalandı!');
            }).catch(() => {
                prompt('Link kopyalamak için Ctrl+C yapın:', shareUrl);
            });
        }
    }
});
</script>

<?php include 'includes/footer.php'; ?>