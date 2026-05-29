<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Obat (JWT)</title>
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
            background-attachment: fixed;
            color: var(--text-color);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 2rem;
            box-sizing: border-box;
        }
        .container {
            width: 100%;
            max-width: 1000px;
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
            margin-bottom: 2rem;
            background: -webkit-linear-gradient(var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        /* Buttons */
        .btn {
            padding: 0.6rem 1.2rem;
            border: none;
            border-radius: 8px;
            font-family: 'Inter', sans-serif;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: white;
        }
        .btn-primary {
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            box-shadow: 0 4px 15px rgba(0, 210, 255, 0.3);
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 210, 255, 0.5);
        }
        .btn-danger {
            background: linear-gradient(45deg, #ff416c, var(--danger-color));
            box-shadow: 0 4px 15px rgba(255, 75, 43, 0.3);
        }
        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 75, 43, 0.5);
        }
        .btn-warning {
            background: linear-gradient(45deg, #fceabb, #f8b500);
            box-shadow: 0 4px 15px rgba(248, 181, 0, 0.3);
            color: #333;
        }
        .btn-warning:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(248, 181, 0, 0.5);
        }
        .header-actions {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1.5rem;
            align-items: center;
        }
        /* Table */
        .table-responsive {
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        th, td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid var(--glass-border);
        }
        th {
            background: rgba(0, 0, 0, 0.2);
            font-weight: 600;
        }
        tr:hover {
            background: rgba(255, 255, 255, 0.05);
        }
        td .actions {
            display: flex;
            gap: 0.5rem;
        }
        /* Login Modal */
        #loginModal {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(5px);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }
        .login-box {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            padding: 2.5rem;
            border-radius: 15px;
            border: 1px solid var(--glass-border);
            width: 320px;
            text-align: center;
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.5);
        }
        .login-box h2 {
            margin-top: 0;
            background: -webkit-linear-gradient(var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .form-group {
            margin-bottom: 1.5rem;
            text-align: left;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
            color: #ccc;
        }
        .form-control {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid var(--glass-border);
            border-radius: 8px;
            background: rgba(0, 0, 0, 0.2);
            color: white;
            font-family: 'Inter', sans-serif;
            box-sizing: border-box;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 10px rgba(0, 210, 255, 0.3);
        }
        .hidden { display: none !important; }
    </style>
</head>
<body>

    <!-- Login Modal -->
    <div id="loginModal">
        <div class="login-box">
            <h2><i class="fas fa-lock"></i> Autentikasi API</h2>
            <p style="font-size: 0.8rem; color: #aaa; margin-bottom: 1.5rem;">Silakan login untuk mendapatkan JWT Token</p>
            <div class="form-group">
                <label>Username</label>
                <input type="text" id="username" class="form-control" placeholder="Masukkan username">
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" id="password" class="form-control" placeholder="Masukkan password">
            </div>
            <button class="btn btn-primary" style="width: 100%; justify-content: center;" onclick="login()">Masuk <i class="fas fa-sign-in-alt"></i></button>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container hidden" id="mainContainer">
        <h1><i class="fas fa-pills"></i> Data Obat Terproteksi (JWT)</h1>
        <div class="header-actions">
            <a href="tambah.php" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Obat</a>
            <button class="btn btn-danger" onclick="logout()"><i class="fas fa-sign-out-alt"></i> Logout</button>
        </div>
        
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>SKU Obat</th>
                        <th>Label / Catatan</th>
                        <th>Jumlah</th>
                        <th>ID RM Pemilik</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <tr><td colspan="6" style="text-align: center;">Memuat data...</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        // Sesuaikan dengan URL backend Anda
        const API_URL = 'http://localhost/slim2026/public';

        document.addEventListener('DOMContentLoaded', () => {
            const token = localStorage.getItem('jwt_token');
            if (token) {
                document.getElementById('loginModal').classList.add('hidden');
                document.getElementById('mainContainer').classList.remove('hidden');
                loadData(token);
            }
        });

        function login() {
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;
            
            if(!username || !password) {
                alert('Username dan Password wajib diisi');
                return;
            }

            const xhr = new XMLHttpRequest();
            xhr.open('POST', `${API_URL}/login-jwt`, true);
            xhr.setRequestHeader('Content-Type', 'application/json');
            
            xhr.onload = function() {
                try {
                    const result = JSON.parse(xhr.responseText);
                    if (xhr.status >= 200 && xhr.status < 300 && result.token) {
                        localStorage.setItem('jwt_token', result.token);
                        document.getElementById('loginModal').classList.add('hidden');
                        document.getElementById('mainContainer').classList.remove('hidden');
                        loadData(result.token);
                    } else {
                        alert(result.message || 'Login gagal!');
                    }
                } catch (e) {
                    alert('Terjadi kesalahan saat memproses respon login.');
                }
            };
            
            xhr.onerror = function() {
                alert('Terjadi kesalahan saat login.');
                console.error('Request failed');
            };

            xhr.send(JSON.stringify({ username, password }));
        }

        function logout() {
            localStorage.removeItem('jwt_token');
            location.reload();
        }

        function loadData(token) {
            const xhr = new XMLHttpRequest();
            xhr.open('GET', `${API_URL}/obat-jwt`, true);
            xhr.setRequestHeader('Authorization', `Bearer ${token}`);
            
            xhr.onload = function() {
                if (xhr.status === 401) {
                    alert('Sesi JWT habis atau tidak valid. Silakan login kembali.');
                    logout();
                    return;
                }
                
                try {
                    const result = JSON.parse(xhr.responseText);
                    const tbody = document.getElementById('tableBody');
                    tbody.innerHTML = '';
                    
                    if (result.data && result.data.length > 0) {
                        result.data.forEach(obat => {
                            const tr = document.createElement('tr');
                            tr.innerHTML = `
                                <td>${obat.id}</td>
                                <td><strong>${obat.sku}</strong></td>
                                <td><span style="opacity: 0.8">${obat.label_catatan || '-'}</span></td>
                                <td><span style="padding: 4px 8px; background: rgba(255,255,255,0.1); border-radius: 10px;">${obat.jumlah}</span></td>
                                <td>${obat.id_rm}</td>
                                <td class="actions">
                                    <a href="edit.php?id=${obat.id}" class="btn btn-warning"><i class="fas fa-edit"></i> Edit</a>
                                    <button onclick="deleteObat(${obat.id})" class="btn btn-danger"><i class="fas fa-trash"></i> Hapus</button>
                                </td>
                            `;
                            tbody.appendChild(tr);
                        });
                    } else {
                        tbody.innerHTML = '<tr><td colspan="6" style="text-align: center;">Belum ada data obat.</td></tr>';
                    }
                } catch (e) {
                    console.error('Error parsing data:', e);
                    document.getElementById('tableBody').innerHTML = '<tr><td colspan="6" style="text-align: center; color: var(--danger-color);">Terjadi kesalahan saat memproses data.</td></tr>';
                }
            };
            
            xhr.onerror = function() {
                console.error('Error fetching data');
                document.getElementById('tableBody').innerHTML = '<tr><td colspan="6" style="text-align: center; color: var(--danger-color);">Terjadi kesalahan saat memuat data.</td></tr>';
            };

            xhr.send();
        }

        function deleteObat(id) {
            if (!confirm('Apakah Anda yakin ingin menghapus data obat ini? Otorisasi berlaku!')) return;
            
            const token = localStorage.getItem('jwt_token');
            const xhr = new XMLHttpRequest();
            xhr.open('DELETE', `${API_URL}/obat-otorisasi/${id}`, true);
            xhr.setRequestHeader('Authorization', `Bearer ${token}`);
            
            xhr.onload = function() {
                try {
                    const result = JSON.parse(xhr.responseText);
                    if (xhr.status >= 200 && xhr.status < 300) {
                        alert('Data berhasil dihapus');
                        loadData(token);
                    } else {
                        alert('Gagal menghapus: ' + (result.error || result.message));
                    }
                } catch (e) {
                    alert('Terjadi kesalahan saat memproses respon hapus data.');
                }
            };
            
            xhr.onerror = function() {
                console.error('Error deleting data');
                alert('Terjadi kesalahan saat menghapus data.');
            };
            
            xhr.send();
        }
    </script>
</body>
</html>