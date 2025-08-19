    </main>

    <footer style="margin-top: 4rem; background: var(--bg-card); border-top: 1px solid var(--border-color); padding: 3rem 0 2rem; box-shadow: var(--shadow-sm);">
        <div class="container">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem; margin-bottom: 2rem;">
                <!-- Site Info -->
                <div>
                    <h3 style="color: var(--accent-primary); margin-bottom: 1rem; font-size: 1.25rem;">🗣️ Confession Hub</h3>
                    <p style="color: var(--text-secondary); line-height: 1.7; margin-bottom: 1rem;">
                        Düşüncelerinizi güvenle paylaşabileceğiniz, toplulukla bağlantı kurabileceğiniz modern itiraf platformu.
                    </p>
                    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                        <span style="background: var(--bg-hover); padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.8rem; color: var(--text-secondary);">🔐 Güvenli</span>
                        <span style="background: var(--bg-hover); padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.8rem; color: var(--text-secondary);">🕶️ Anonim</span>
                        <span style="background: var(--bg-hover); padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.8rem; color: var(--text-secondary);">💬 Topluluk</span>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div>
                    <h4 style="color: var(--text-primary); margin-bottom: 1rem; font-size: 1.1rem;">📊 Canlı İstatistikler</h4>
                    <div style="display: grid; gap: 0.5rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.5rem; background: var(--bg-hover); border-radius: var(--border-radius-sm);">
                            <span style="color: var(--text-secondary);">📝 Toplam İtiraf</span>
                            <strong style="color: var(--accent-primary);"><?php echo $db->getTotalConfessions(); ?></strong>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.5rem; background: var(--bg-hover); border-radius: var(--border-radius-sm);">
                            <span style="color: var(--text-secondary);">👥 Kayıtlı Kullanıcı</span>
                            <strong style="color: var(--accent-success);"><?php echo $db->getTotalUsers(); ?></strong>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.5rem; background: var(--bg-hover); border-radius: var(--border-radius-sm);">
                            <span style="color: var(--text-secondary);">💬 Toplam Yorum</span>
                            <strong style="color: var(--accent-warning);"><?php echo $db->getTotalComments(); ?></strong>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.5rem; background: var(--bg-hover); border-radius: var(--border-radius-sm);">
                            <span style="color: var(--text-secondary);">⚡ Aktif Kullanıcı</span>
                            <strong style="color: var(--accent-secondary);"><?php echo $db->getActiveUsers(); ?></strong>
                        </div>
                    </div>
                </div>

                <!-- Quick Links -->
                <div>
                    <h4 style="color: var(--text-primary); margin-bottom: 1rem; font-size: 1.1rem;">🔗 Hızlı Linkler</h4>
                    <div style="display: grid; gap: 0.5rem;">
                        <a href="terms.php" style="color: var(--text-secondary); text-decoration: none; padding: 0.5rem; border-radius: var(--border-radius-sm); transition: all 0.3s ease;" onmouseover="this.style.background='var(--bg-hover)'; this.style.color='var(--text-primary)'" onmouseout="this.style.background=''; this.style.color='var(--text-secondary)'">📋 Kullanım Şartları</a>
                        <a href="privacy.php" style="color: var(--text-secondary); text-decoration: none; padding: 0.5rem; border-radius: var(--border-radius-sm); transition: all 0.3s ease;" onmouseover="this.style.background='var(--bg-hover)'; this.style.color='var(--text-primary)'" onmouseout="this.style.background=''; this.style.color='var(--text-secondary)'">🔒 Gizlilik Politikası</a>
                        <a href="contact.php" style="color: var(--text-secondary); text-decoration: none; padding: 0.5rem; border-radius: var(--border-radius-sm); transition: all 0.3s ease;" onmouseover="this.style.background='var(--bg-hover)'; this.style.color='var(--text-primary)'" onmouseout="this.style.background=''; this.style.color='var(--text-secondary)'">📧 İletişim</a>
                        <?php if (!isLoggedIn()): ?>
                            <a href="register.php" style="color: var(--accent-primary); text-decoration: none; padding: 0.5rem; border-radius: var(--border-radius-sm); font-weight: 600; background: rgba(99, 102, 241, 0.1); transition: all 0.3s ease;" onmouseover="this.style.background='rgba(99, 102, 241, 0.2)'" onmouseout="this.style.background='rgba(99, 102, 241, 0.1)'">🚀 Hemen Katıl</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Bottom Footer -->
            <div style="padding-top: 2rem; border-top: 1px solid var(--border-color); display: flex; justify-content: between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                <div style="color: var(--text-muted); font-size: 0.9rem;">
                    © <?php echo date('Y'); ?> Confession Hub. Tüm hakları saklıdır.
                </div>
                <div style="display: flex; gap: 1rem; align-items: center;">
                    <span style="color: var(--text-muted); font-size: 0.8rem;">
                        🛡️ Güvenli bağlantı
                    </span>
                    <span style="color: var(--text-muted); font-size: 0.8rem;">
                        🕐 Türkiye Saati: <?php echo date('H:i'); ?>
                    </span>
                </div>
            </div>
        </div>
    </footer>

    <!-- Additional Scripts -->
    <script>
        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Add fade-in animation for cards
        document.addEventListener('DOMContentLoaded', function() {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            });

            document.querySelectorAll('.fade-in-up').forEach(el => {
                el.style.opacity = '0';
                el.style.transform = 'translateY(20px)';
                el.style.transition = 'all 0.6s ease';
                observer.observe(el);
            });
        });
    </script>

    <style>
        /* Additional styles for confession cards and forms */
        .confession-card {
            background: var(--bg-card);
            border-radius: var(--border-radius);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: var(--shadow-md);
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
        }

        .confession-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .confession-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 1.1rem;
        }

        .confession-meta h4 {
            color: var(--text-primary);
            font-size: 1rem;
            margin-bottom: 0.25rem;
        }

        .confession-meta .time {
            color: var(--text-muted);
            font-size: 0.8rem;
        }

        .confession-content {
            margin-bottom: 1rem;
            color: var(--text-primary);
            line-height: 1.7;
        }

        .confession-actions {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .action-btn {
            background: var(--bg-hover);
            border: 1px solid var(--border-color);
            color: var(--text-secondary);
            padding: 0.5rem 1rem;
            border-radius: var(--border-radius-sm);
            cursor: pointer;
            font-size: 0.8rem;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
        }

        .action-btn:hover {
            background: var(--border-color);
            transform: translateY(-1px);
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text-primary);
            font-weight: 500;
        }

        .form-textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius-sm);
            background: var(--bg-card);
            color: var(--text-primary);
            font-family: inherit;
            resize: vertical;
            transition: all 0.3s ease;
        }

        .form-textarea:focus {
            outline: none;
            border-color: var(--accent-primary);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }

        .form-select {
            padding: 0.5rem;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius-sm);
            background: var(--bg-card);
            color: var(--text-primary);
            font-family: inherit;
        }

        .file-upload {
            position: relative;
        }

        .file-upload-input {
            display: none;
        }

        .file-upload-label {
            display: inline-block;
            padding: 0.5rem 1rem;
            background: var(--bg-hover);
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius-sm);
            color: var(--text-secondary);
            cursor: pointer;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .file-upload-label:hover {
            background: var(--border-color);
            transform: translateY(-1px);
        }

        .comments-section {
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid var(--border-color);
        }

        .comment-form {
            background: var(--bg-hover);
            padding: 1rem;
            border-radius: var(--border-radius-sm);
            border: 1px solid var(--border-color);
        }

        @media (max-width: 768px) {
            .confession-actions {
                gap: 0.25rem;
            }
            
            .action-btn {
                padding: 0.4rem 0.8rem;
                font-size: 0.75rem;
            }

            .confession-card {
                padding: 1rem;
            }
        }
    </style>
</body>
</html>