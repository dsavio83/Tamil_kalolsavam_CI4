<style>
    .backup-hero {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 12px;
        padding: 30px 35px;
        color: #fff;
        margin-bottom: 25px;
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.35);
    }
    .backup-hero h3 {
        font-family: 'Poppins', sans-serif;
        font-weight: 600;
        font-size: 22px;
        margin-bottom: 6px;
    }
    .backup-hero p {
        opacity: 0.88;
        font-size: 14px;
        margin-bottom: 0;
    }
    .backup-actions {
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
        margin-bottom: 25px;
    }
    .backup-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 28px;
        border: none;
        border-radius: 8px;
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.25s ease;
        text-decoration: none;
    }
    .backup-btn-primary {
        background: linear-gradient(135deg, #11998e, #38ef7d);
        color: #fff;
        box-shadow: 0 4px 15px rgba(56, 239, 125, 0.35);
    }
    .backup-btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(56, 239, 125, 0.5);
        color: #fff;
        text-decoration: none;
    }
    .backup-btn-secondary {
        background: #fff;
        color: #667eea;
        border: 2px solid #667eea;
    }
    .backup-btn-secondary:hover {
        background: #667eea;
        color: #fff;
        text-decoration: none;
    }
    .backup-card {
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        overflow: hidden;
    }
    .backup-card-header {
        padding: 16px 24px;
        font-weight: 600;
        font-size: 16px;
        color: #012970;
        border-bottom: 2px solid #f0f2f5;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .backup-card-body {
        padding: 15px 24px;
    }
    .backup-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .backup-list li {
        padding: 10px 0;
        border-bottom: 1px solid #f0f2f5;
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 14px;
    }
    .backup-list li:last-child {
        border-bottom: none;
    }
    .backup-file-name {
        color: #343a40;
        font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, monospace;
        font-size: 13px;
    }
    .backup-file-icon {
        color: #667eea;
        margin-right: 8px;
    }
    .backup-badge {
        background: #e8f5e9;
        color: #2e7d32;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        white-space: nowrap;
    }
    .backup-empty {
        text-align: center;
        padding: 25px 0;
        color: #999;
        font-size: 14px;
    }
    .backup-empty i {
        font-size: 36px;
        color: #ddd;
        display: block;
        margin-bottom: 10px;
    }
    .backup-stat {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(255,255,255,0.2);
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 13px;
        margin-top: 12px;
    }
    .backup-alert {
        padding: 14px 20px;
        border-radius: 8px;
        font-size: 13px;
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 20px;
    }
    .backup-alert-info {
        background: #e8f4fd;
        color: #0c63e4;
        border: 1px solid #b6d4fe;
    }
    .backup-alert-warning {
        background: #fff8e1;
        color: #e65100;
        border: 1px solid #ffe0b2;
    }
</style>

<div class="pagetitle">
    <h1>Settings</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>welcome" title="Home">Home</a></li>
            <li class="breadcrumb-item active">Backup Database as SQL</li>
        </ol>
    </nav>
</div>

<div class="backup-hero">
    <h3><i class="bi bi-database-down"></i> Database Backup</h3>
    <p>Export your complete database as a .sql file for safekeeping and restoration.</p>
    <?php if (!empty($backupFileNames)): ?>
        <div class="backup-stat">
            <i class="bi bi-files"></i> <?php echo count($backupFileNames); ?> backup file(s) available
        </div>
    <?php endif; ?>
</div>

<div class="backup-alert backup-alert-info">
    <i class="bi bi-info-circle" style="font-size:18px;"></i>
    <span>Click <strong>"Download Backup"</strong> to generate a new SQL backup file and download it to your computer. A copy is also saved on the server.</span>
</div>

<div class="backup-actions">
    <a href="<?php echo base_url(); ?>import/backup_data_sql?do=backup" class="backup-btn backup-btn-primary" id="btnBackupNow">
        <i class="bi bi-cloud-download"></i> Download Backup
    </a>
    <a href="<?php echo base_url(); ?>welcome" class="backup-btn backup-btn-secondary">
        <i class="bi bi-arrow-left"></i> Back to Home
    </a>
</div>

<div class="backup-card">
    <div class="backup-card-header">
        <i class="bi bi-folder2-open"></i> Server Backup Files
    </div>
    <div class="backup-card-body">
        <?php if (!empty($backupFileNames)): ?>
            <ul class="backup-list">
                <?php foreach ($backupFileNames as $file): ?>
                    <li>
                        <span>
                            <i class="bi bi-file-earmark-code backup-file-icon"></i>
                            <span class="backup-file-name"><?php echo htmlspecialchars($file); ?></span>
                        </span>
                        <span class="backup-badge"><i class="bi bi-check-circle"></i> Saved</span>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <div class="backup-empty">
                <i class="bi bi-inbox"></i>
                No backup files found on the server. Click <strong>"Download Backup"</strong> to create your first backup.
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var btn = document.getElementById('btnBackupNow');
        if (btn) {
            btn.addEventListener('click', function() {
                var originalHtml = btn.innerHTML;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Generating SQL Backup...';
                btn.style.pointerEvents = 'none';
                btn.style.opacity = '0.85';

                var notice = document.getElementById('backupStatusNotice');
                if (!notice) {
                    notice = document.createElement('div');
                    notice.id = 'backupStatusNotice';
                    notice.className = 'alert alert-info alert-dismissible fade show mt-3 shadow-xs';
                    notice.innerHTML = '<i class="bi bi-info-circle-fill me-2"></i><strong>Backup Exporting:</strong> Generating SQL dump file... Download will start automatically. <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
                    var actions = document.querySelector('.backup-actions');
                    if (actions && actions.parentNode) {
                        actions.parentNode.insertBefore(notice, actions.nextSibling);
                    }
                }

                setTimeout(function() {
                    btn.innerHTML = originalHtml;
                    btn.style.pointerEvents = 'auto';
                    btn.style.opacity = '1';
                }, 5000);
            });
        }
    });
</script>
