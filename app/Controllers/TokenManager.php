<?php
namespace App\Controllers;

use Firebase\JWT\JWT;

class TokenManager extends BaseController
{
    public function index()
    {
        return view('token_view');
    }

    public function generate()
    {
        $user = $this->request->getPost('username');
        $pass = $this->request->getPost('password');
        $role = $this->request->getPost('role'); // admin atau resep

        // Validasi Hardcode
        if ($user !== 'admin123' || $pass !== getenv('TOKEN_APP_PASSWORD')) {
            return view('token_view', ['error' => 'Username atau Password salah!']);
        }

        // Durasi token: 1 tahun untuk resep, 8 jam untuk admin
        $hours = ($role === 'resep') ? 8760 : 8; 
        
        $key = getenv('JWT_SECRET_KEY');
        $iat = time();
        $payload = [
            'iat'  => $iat,
            'exp'  => $iat + (3600 * $hours),
            'data' => [
                'user_id' => 1,
                'role'    => $role 
            ]
        ];

        $token = JWT::encode($payload, $key, 'HS256');
        
        return view('token_view', [
            'token' => $token, 
            'role' => $role
        ]);
    }
}
