<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Obat</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --bg-gradient: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
            --glass-bg: rgba(255, 255, 255, 0.05);
            --glass-border: rgba(255, 255, 255, 0.1);
            --primary-color: #00d2ff;
            --secondary-color: #3a7bd5;
            --danger-color: #ff4b2b;
            --text-color: #ffffff;
        }
        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            background: var(--bg-gradient);
            color: var(--text-color);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 2rem;
            box-sizing: border-box;
        }
        .container {
            width: 100%;
            max-width: 520px;
            background: var(--glass-bg);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37);
            animation: fadeIn 0.5s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        h1 {
            text-align: center;
            font-weight: 600;
            margin-bottom: 1.5rem;
            background: -webkit-linear-gradient(var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .form-group {
            margin-bottom: 1.2rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #ccc;
            font-size: 0.95rem;
        }
        .form-control {
            width: 100%;
            padding: 0.9rem 1rem;
            border: 1px solid var(--glass-border);
            border-radius: 10px;
            background: rgba(0, 0, 0, 0.17);
            color: white;
            font-size: 1rem;
            box-sizing: border-box;
        }
        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 10px rgba(0, 210, 255, 0.2);
        }
        .actions {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
            justify-content: center;
            margin-top: 1.5rem;
        }
        .btn {
            padding: 0.8rem 1.3rem;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            color: white;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
        }
        .btn-primary { background: linear-gradient(45deg, var(--primary-color), var(--secondary-color)); }
        .btn-warning { background: linear-gradient(45deg, #f8b500, #fceabb); color: #333; }
        .btn-danger { background: linear-gradient(45deg, #ff416c, var(--danger-color)); }
        .btn:hover { transform: translateY(-2px); }
        .alert {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.12);
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 1rem;
            color: #eee;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><i class="fas fa-edit"></i> Edit Obat</h1>
        <p class="alert">Edit data obat menggunakan token JWT. Pastikan Anda login sebelum mengakses halaman ini.</p>

        <div class="form-group">
            <label for="sku">SKU Obat</label>
            <input type="text" id="sku" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="label_catatan">Label / Catatan</label>
            <input type="text" id="label_catatan" class="form-control">
        </div>
        <div class="form-group">
            <label for="jumlah">Jumlah</label>
            <input type="number" id="jumlah" class="form-control" required>
        </div>

        <div class="actions">
            <button class="btn btn-primary" onclick="submitForm()"><i class="fas fa-save"></i> Perbarui</button>
            <a href="index.php" class="btn btn-warning"><i class="fas fa-arrow-left"></i> Kembali</a>
        </div>
    </div>

    <script>
        const API_URL = 'api.php';
        const params = new URLSearchParams(window.location.search);
        const obatId = params.get('id');

        document.addEventListener('DOMContentLoaded', () => {
            const token = localStorage.getItem('jwt_token');
            if (!token) {
                alert('Anda harus login terlebih dahulu.');
                window.location.href = 'index.php';
                return;
            }
            if (!obatId) {
                alert('ID obat tidak ditemukan.');
                window.location.href = 'index.php';
                return;
            }
            loadObat(token);
        });

        function loadObat(token) {
            const xhr = new XMLHttpRequest();
            xhr.open('GET', `${API_URL}?route=obat-otorisasi&id=${obatId}`, true);
            xhr.setRequestHeader('Authorization', `Bearer ${token}`);
            xhr.onload = function() {
                if (xhr.status === 401) {
                    alert('Token tidak valid atau sesi sudah habis. Silakan login ulang.');
                    window.location.href = 'index.php';
                    return;
                }
                try {
                    const result = JSON.parse(xhr.responseText);
                    if (xhr.status >= 200 && xhr.status < 300) {
                        document.getElementById('sku').value = result.sku || '';
                        document.getElementById('label_catatan').value = result.label_catatan || '';
                        document.getElementById('jumlah').value = result.jumlah || '';
                    } else {
                        alert(result.message || 'Gagal mengambil data obat.');
                        window.location.href = 'index.php';
                    }
                } catch (e) {
                    alert('Terjadi kesalahan saat memproses data obat.');
                    window.location.href = 'index.php';
                }
            };
            xhr.onerror = function() {
                alert('Gagal memuat data dari server.');
                window.location.href = 'index.php';
            };
            xhr.send();
        }

        function submitForm() {
            const token = localStorage.getItem('jwt_token');
            const sku = document.getElementById('sku').value.trim();
            const label_catatan = document.getElementById('label_catatan').value.trim();
            const jumlah = document.getElementById('jumlah').value.trim();

            if (!sku || !jumlah) {
                alert('SKU dan jumlah wajib diisi.');
                return;
            }

            const xhr = new XMLHttpRequest();
            xhr.open('PUT', `${API_URL}?route=obat-otorisasi&id=${obatId}`, true);
            xhr.setRequestHeader('Content-Type', 'application/json');
            xhr.setRequestHeader('Authorization', `Bearer ${token}`);
            xhr.onload = function() {
                try {
                    const result = JSON.parse(xhr.responseText);
                    if (xhr.status >= 200 && xhr.status < 300) {
                        alert('Data obat berhasil diperbarui.');
                        window.location.href = 'index.php';
                    } else {
                        console.error('Update failed:', xhr.status, xhr.responseText);
                        alert(result.message || result.error || 'Gagal memperbarui data.');
                        if (xhr.status === 401) window.location.href = 'index.php';
                    }
                } catch (e) {
                    console.error('JSON parse error', e, xhr.responseText);
                    alert('Terjadi kesalahan saat memproses respon.');
                }
            };
            xhr.onerror = function() {
                alert('Gagal mengirim data ke server.');
            };
            xhr.send(JSON.stringify({ sku, label_catatan, jumlah: Number(jumlah) }));
        }
    </script>
</body>
</html>