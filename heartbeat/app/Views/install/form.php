<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Install</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    body { font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial; padding: 24px; }
    form { max-width: 560px; }
    label { display:block; margin:12px 0 6px; font-weight:600; }
    input { width:100%; padding:10px; border:1px solid #ccc; border-radius:8px; }
    .row { display:grid; grid-template-columns: 1fr 1fr; gap:12px; }
    .error { background:#ffecec; border:1px solid #f5c2c7; color:#b02a37; padding:8px 12px; border-radius:8px; margin-bottom:10px;}
    button { margin-top:18px; padding:12px 16px; border:none; border-radius:10px; background:#111; color:#fff; font-weight:700; cursor:pointer; }
  </style>
</head>
<body>
  <h2>First-time Setup</h2>

  <?php if (!empty($errors)): ?>
    <?php foreach($errors as $e): ?>
      <div class="error"><?= esc($e) ?></div>
    <?php endforeach; ?>
  <?php endif; ?>

  <form method="post" action="<?= site_url('install') ?>">
    <?= csrf_field() ?>

    <label>Base URL</label>
    <input name="base_url" type="url" required placeholder="https://example.com/" value="<?= esc($old['base_url'] ?? (current_url(true)->setPath('')->setQuery('')->setFragment('')->__toString())) ?>">

    <label>Application Name</label>
    <input name="app_name" required placeholder="My App" value="<?= esc($old['app_name'] ?? '') ?>">

    <div class="row">
      <div>
        <label>DB Host</label>
        <input name="db_host" required placeholder="127.0.0.1" value="<?= esc($old['db_host'] ?? '127.0.0.1') ?>">
      </div>
      <div>
        <label>DB Port</label>
        <input name="db_port" required type="number" min="1" placeholder="3306" value="<?= esc($old['db_port'] ?? '3306') ?>">
      </div>
    </div>

    <label>Database Name</label>
    <input name="db_name" required placeholder="my_database" value="<?= esc($old['db_name'] ?? '') ?>">

    <div class="row">
      <div>
        <label>DB Username</label>
        <input name="db_user" required placeholder="root" value="<?= esc($old['db_user'] ?? '') ?>">
      </div>
      <div>
        <label>DB Password</label>
        <input name="db_pass" type="password" placeholder="••••••••">
      </div>
    </div>

    <button type="submit">Install</button>
  </form>
</body>
</html>
