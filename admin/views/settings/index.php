<?php
require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../includes/auth_check.php';

authCheck(['super_admin']);

$pageTitle = 'System Settings';

$settingsFile = __DIR__ . '/../../../config/settings.json';

$defaultSettings = [
    'site_name' => 'QLSV Admin',
    'admin_email' => 'admin@example.com',
    'timezone' => 'Asia/Ho_Chi_Minh',
    'items_per_page' => 20,
    'maintenance_mode' => false
];

$settings = $defaultSettings;
if (is_file($settingsFile)) {
    $raw = file_get_contents($settingsFile);
    $decoded = json_decode($raw, true);
    if (is_array($decoded)) {
        $settings = array_merge($settings, $decoded);
    }
}

$timezones = [
    'Asia/Ho_Chi_Minh',
    'Asia/Bangkok',
    'Asia/Singapore',
    'Asia/Tokyo',
    'UTC'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $siteName = trim((string)($_POST['site_name'] ?? ''));
    $adminEmail = trim((string)($_POST['admin_email'] ?? ''));
    $timezone = (string)($_POST['timezone'] ?? '');
    $itemsPerPage = (int)($_POST['items_per_page'] ?? 20);
    $maintenanceMode = isset($_POST['maintenance_mode']);

    $errors = [];

    if ($siteName === '' || mb_strlen($siteName) > 100) {
        $errors[] = 'Site name is required and must be <= 100 chars.';
    }

    if ($adminEmail === '' || !filter_var($adminEmail, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Admin email is invalid.';
    }

    if (!in_array($timezone, $timezones, true)) {
        $errors[] = 'Timezone is invalid.';
    }

    if ($itemsPerPage < 5 || $itemsPerPage > 200) {
        $errors[] = 'Items per page must be between 5 and 200.';
    }

    if (empty($errors)) {
        $settings = [
            'site_name' => $siteName,
            'admin_email' => $adminEmail,
            'timezone' => $timezone,
            'items_per_page' => $itemsPerPage,
            'maintenance_mode' => $maintenanceMode
        ];

        $payload = json_encode($settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        if ($payload !== false && file_put_contents($settingsFile, $payload, LOCK_EX) !== false) {
            $_SESSION['success'] = 'Settings saved successfully.';
            header('Location: index.php');
            exit;
        }

        $errors[] = 'Failed to save settings.';
    }

    if (!empty($errors)) {
        $_SESSION['error'] = implode(' ', $errors);
        $settings = array_merge($settings, [
            'site_name' => $siteName,
            'admin_email' => $adminEmail,
            'timezone' => $timezone,
            'items_per_page' => $itemsPerPage,
            'maintenance_mode' => $maintenanceMode
        ]);
    }
}
?>

<?php require_once __DIR__ . '/../layout/header.php'; ?>
<?php require_once __DIR__ . '/../../../includes/alert.php'; ?>

<div class="content-wrapper">
    <h3 class="mb-3">System Settings</h3>

    <div class="content-card">
        <form method="POST">
            <div class="form-group mb-3">
                <label>Site name</label>
                <input type="text" name="site_name" class="form-control" value="<?= htmlspecialchars($settings['site_name']) ?>" required>
            </div>

            <div class="form-group mb-3">
                <label>Admin email</label>
                <input type="email" name="admin_email" class="form-control" value="<?= htmlspecialchars($settings['admin_email']) ?>" required>
            </div>

            <div class="form-group mb-3">
                <label>Timezone</label>
                <select name="timezone" class="form-select">
                    <?php foreach ($timezones as $tz): ?>
                        <option value="<?= htmlspecialchars($tz) ?>" <?= $settings['timezone'] === $tz ? 'selected' : '' ?>>
                            <?= htmlspecialchars($tz) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group mb-3">
                <label>Items per page</label>
                <input type="number" name="items_per_page" class="form-control" min="5" max="200" value="<?= (int)$settings['items_per_page'] ?>" required>
            </div>

            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" name="maintenance_mode" id="maintenance_mode" <?= $settings['maintenance_mode'] ? 'checked' : '' ?>>
                <label class="form-check-label" for="maintenance_mode">
                    Maintenance mode
                </label>
            </div>

            <button class="btn btn-primary">Save settings</button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
