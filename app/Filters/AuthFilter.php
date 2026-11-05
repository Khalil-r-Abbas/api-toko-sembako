<?php
namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Config\Services;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $key = getenv('JWT_SECRET_KEY');
        $header = $request->getHeaderLine('Authorization');
        $token = null;

        if (!empty($header) && preg_match('/Bearer\s(\S+)/', $header, $matches)) {
            $token = $matches[1];
        }

        if (empty($token)) {
            return $this->reject('Akses ditolak. Token (API Key) tidak ditemukan.');
        }

        try {
            $decoded = JWT::decode($token, new Key($key, 'HS256'));
            $role = $decoded->data->role;
            
            // BATASAN UNTUK APLIKASI RESEP
            if ($role === 'resep') {
                $path = $request->getPath();
                $method = $request->getMethod();
                
                // DAFTAR RUTE YANG DIIZINKAN (Ganti sesuai nama API-mu)
                $allowedRoutes = ['api/products', 'api/products/price'];
                $isAllowed = false;
                
                foreach ($allowedRoutes as $route) {
                    // Jika mengakses awalan URL yang diizinkan dan method GET
                    if (strpos($path, $route) === 0 && strtolower($method) === 'get') {
                        $isAllowed = true;
                        break;
                    }
                }

                if (!$isAllowed) {
                    return $this->reject('403 Forbidden: Aplikasi Resep tidak diizinkan mengubah data atau mengakses rute ini.', 403);
                }
            }

            // Jika role = 'admin', lolos semua rute API
            
        } catch (\Exception $ex) {
            return $this->reject('Token tidak valid atau sudah kedaluwarsa.');
        }
    }

    private function reject($message, $code = 401)
    {
        return Services::response()
            ->setJSON(['status' => $code, 'message' => $message])
            ->setStatusCode($code);
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}
