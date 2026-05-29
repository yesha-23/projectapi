<?php
require_once __DIR__ . '/db.php';

header('Content-Type: application/json; charset=utf-8');

$rawInput = file_get_contents('php://input');

const JWT_SECRET = 'ganti_rahasia_anda_12345';

$method = $_SERVER['REQUEST_METHOD'];
$route = $_GET['route'] ?? '';

try {
    switch ($route) {
        case 'login-jwt':
            if ($method !== 'POST') throw new Exception('Metode tidak diizinkan', 405);
            handleLogin();
            break;
        case 'obat-jwt':
            authenticate();
            if ($method !== 'GET') throw new Exception('Metode tidak diizinkan', 405);
            handleGetAllObat();
            break;
        case 'obat-otorisasi':
            authenticate();
            handleObatOtorisasi($method);
            break;
        case 'simulasi':
            if ($method !== 'GET') throw new Exception('Metode tidak diizinkan', 405);
            echo json_encode([
                "id" => 1,
                "sku" => "OBT-001",
                "label_catatan" => "Diminum setelah makan",
                "jumlah" => 10
            ]);
            exit;
        default:
            throw new Exception('Endpoint tidak ditemukan', 404);
    }
} catch (Throwable $e) {
    $code = $e->getCode();
    $code = is_int($code) ? $code : (is_numeric($code) ? (int) $code : 0);
    if ($code < 100 || $code >= 600) {
        $code = 400;
    }
    http_response_code($code);
    echo json_encode(['error' => $e->getMessage()]);
    exit;
}

function handleLogin(): void {
    $body = getJsonBody();
    $username = trim($body['username'] ?? '');
    $password = trim($body['password'] ?? '');

    if ($username === '' || $password === '') {
        throw new Exception('Username dan password wajib diisi', 400);
    }

    $db = getDb();
    $stmt = $db->prepare('SELECT * FROM user_role WHERE username = ?');
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || !password_verify($password, $user['password'])) {
        throw new Exception('Username atau password salah', 401);
    }

    $token = generateJwt(['user_id' => $user['id'], 'username' => $user['username']]);
    echo json_encode(['token' => $token, 'message' => 'Login berhasil']);
    exit;
}

function handleGetAllObat(): void {
    $db = getDb();
    $stmt = $db->query('SELECT id_obat as id, sku, label_catatan, jumlah FROM obat ORDER BY id_obat DESC');
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['data' => $data]);
    exit;
}

function handleObatOtorisasi(string $method): void {
    $id = isset($_GET['id']) ? intval($_GET['id']) : null;
    switch ($method) {
        case 'GET':
            if ($id === null) {
                handleGetAllObat();
                return;
            }
            handleGetObat($id);
            break;
        case 'POST':
            handleCreateObat();
            break;
        case 'PUT':
            if ($id === null) throw new Exception('ID obat diperlukan', 400);
            handleUpdateObat($id);
            break;
        case 'DELETE':
            if ($id === null) throw new Exception('ID obat diperlukan', 400);
            handleDeleteObat($id);
            break;
        default:
            throw new Exception('Metode tidak diizinkan', 405);
    }
}

function handleGetObat(int $id): void {
    $db = getDb();
    $stmt = $db->prepare('SELECT id_obat as id, sku, label_catatan, jumlah FROM obat WHERE id_obat = ?');
    $stmt->execute([$id]);
    $obat = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$obat) {
        throw new Exception('Data obat tidak ditemukan', 404);
    }

    echo json_encode($obat);
    exit;
}

function handleCreateObat(): void {
    $body = getJsonBody();
    $sku = trim($body['sku'] ?? '');
    $label_catatan = trim($body['label_catatan'] ?? '');
    $jumlah = intval($body['jumlah'] ?? 0);

    if ($sku === '' || $jumlah <= 0) {
        throw new Exception('SKU dan jumlah wajib diisi dengan benar', 400);
    }

    $db = getDb();
    $stmt = $db->prepare('INSERT INTO obat (sku, label_catatan, jumlah) VALUES (?, ?, ?)');
    $stmt->execute([$sku, $label_catatan, $jumlah]);
    echo json_encode(['message' => 'Data obat berhasil ditambahkan']);
    exit;
}

function handleUpdateObat(int $id): void {
    $body = getJsonBody();
    $sku = trim($body['sku'] ?? '');
    $label_catatan = trim($body['label_catatan'] ?? '');
    $jumlah = intval($body['jumlah'] ?? 0);

    if ($sku === '' || $jumlah <= 0) {
        throw new Exception('SKU dan jumlah wajib diisi dengan benar', 400);
    }

    $db = getDb();
    $stmt = $db->prepare('UPDATE obat SET sku = ?, label_catatan = ?, jumlah = ? WHERE id_obat = ?');
    $stmt->execute([$sku, $label_catatan, $jumlah, $id]);

    if ($stmt->rowCount() === 0) {
        throw new Exception('Data obat tidak ditemukan atau tidak ada perubahan', 404);
    }

    echo json_encode(['message' => 'Data obat berhasil diperbarui']);
    exit;
}

function handleDeleteObat(int $id): void {
    $db = getDb();
    $stmt = $db->prepare('DELETE FROM obat WHERE id_obat = ?');
    $stmt->execute([$id]);

    if ($stmt->rowCount() === 0) {
        throw new Exception('Data obat tidak ditemukan', 404);
    }

    echo json_encode(['message' => 'Data obat berhasil dihapus']);
    exit;
}

function getJsonBody(): array {
    global $rawInput;
    $input = $rawInput !== null ? $rawInput : file_get_contents('php://input');
    if ($input !== '') {
        $data = json_decode($input, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($data)) {
            return $data;
        }
    }

    if (!empty($_POST)) {
        return $_POST;
    }

    return [];
}

function getBearerToken(): ?string {
    if (isset($_SERVER['HTTP_AUTHORIZATION']) && $_SERVER['HTTP_AUTHORIZATION'] !== '') {
        $auth = trim($_SERVER['HTTP_AUTHORIZATION']);
    } elseif (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION']) && $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] !== '') {
        $auth = trim($_SERVER['REDIRECT_HTTP_AUTHORIZATION']);
    } else {
        $headers = function_exists('getallheaders') ? getallheaders() : [];
        if (isset($headers['Authorization'])) {
            $auth = trim($headers['Authorization']);
        } elseif (isset($headers['authorization'])) {
            $auth = trim($headers['authorization']);
        } else {
            return null;
        }
    }

    if (stripos($auth, 'Bearer ') === 0) {
        return substr($auth, 7);
    }
    return null;
}

function authenticate(): void {
    $token = getBearerToken();
    if (!$token) {
        http_response_code(401);
        echo json_encode(['message' => 'Token otorisasi tidak ditemukan']);
        exit;
    }
    $payload = validateJwt($token);
    if (!$payload) {
        http_response_code(401);
        echo json_encode(['message' => 'Token tidak valid atau sudah kadaluarsa']);
        exit;
    }
}

function generateJwt(array $payload): string {
    $header = ['alg' => 'HS256', 'typ' => 'JWT'];
    $payload['iat'] = time();
    $payload['exp'] = time() + 3600;
    $base64UrlHeader = base64UrlEncode(json_encode($header));
    $base64UrlPayload = base64UrlEncode(json_encode($payload));
    $signature = hash_hmac('sha256', $base64UrlHeader . '.' . $base64UrlPayload, JWT_SECRET, true);
    $base64UrlSignature = base64UrlEncode($signature);
    return $base64UrlHeader . '.' . $base64UrlPayload . '.' . $base64UrlSignature;
}

function validateJwt(string $jwt): ?array {
    $parts = explode('.', $jwt);
    if (count($parts) !== 3) {
        return null;
    }

    [$headerEncoded, $payloadEncoded, $signatureEncoded] = $parts;
    $expectedSig = base64UrlEncode(hash_hmac('sha256', $headerEncoded . '.' . $payloadEncoded, JWT_SECRET, true));
    if (!hash_equals($expectedSig, $signatureEncoded)) {
        return null;
    }

    $payload = json_decode(base64UrlDecode($payloadEncoded), true);
    if (!is_array($payload) || !isset($payload['exp']) || time() > $payload['exp']) {
        return null;
    }

    return $payload;
}

function base64UrlEncode(string $data): string {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

function base64UrlDecode(string $data): string {
    $remainder = strlen($data) % 4;
    if ($remainder) {
        $data .= str_repeat('=', 4 - $remainder);
    }
    return base64_decode(strtr($data, '-_', '+/'));
}