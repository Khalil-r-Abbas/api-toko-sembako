<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Generate API Token</title>
    <style>
        body { font-family: sans-serif; padding: 20px; background: #f4f4f9; }
        .container { max-width: 400px; margin: auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        input, select, button { width: 100%; padding: 10px; margin: 10px 0; box-sizing: border-box; }
        button { background: #28a745; color: white; border: none; cursor: pointer; }
        .result { margin-top: 20px; padding: 10px; background: #e9ecef; word-wrap: break-word; }
        .error { color: red; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Buat Token Akses</h2>
        <?php if(isset($error)): ?><p class="error"><?= $error ?></p><?php endif; ?>
        
        <form action="/token-manager/generate" method="POST">
            <label>Username</label>
            <input type="text" name="username" placeholder="Masukkan username" required>
            
            <label>Password</label>
            <input type="password" name="password" placeholder="Masukkan password" required>
            
            <label>Pilih Role Token</label>
            <select name="role">
                <option value="admin">Admin (Akses Penuh - Berlaku 8 Jam)</option>
                <option value="resep">Aplikasi Resep (Akses Terbatas - Berlaku 1 Tahun)</option>
            </select>
            
            <button type="submit">Generate Token</button>
        </form>

        <?php if(isset($token)): ?>
            <div class="result">
                <strong>Berhasil! (Role: <?= $role ?>)</strong><br><br>
                Berikut token Anda:<br><br>
                <textarea rows="5" style="width:100%" readonly><?= $token ?></textarea>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
