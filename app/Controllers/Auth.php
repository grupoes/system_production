<?php

namespace App\Controllers;

class Auth extends BaseController
{
    public function login()
    {
        if (session()->get('isLoggedIn')) {
            return redirect()->to('/home');
        }
        return view('auth/login');
    }

    public function attemptLogin()
    {
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $usuarioModel = new \App\Models\UsuarioModel();
        
        // Buscamos el usuario uniendo con la tabla personas y roles
        $user = $usuarioModel->select('usuarios.*, personas.nombres, personas.apellidos, personas.email, roles.nombre as rol_nombre')
            ->join('personas', 'personas.id = usuarios.persona_id')
            ->join('roles', 'roles.id = usuarios.rol_id', 'left')
            ->where('personas.email', $email)
            ->where('usuarios.clave', $password)
            ->where('usuarios.estado', true)
            ->first();

        if ($user) {
            $session = session();
            $sessionData = [
                'id'         => $user['id'],
                'nombre'     => $user['nombres'] . ' ' . $user['apellidos'],
                'email'      => $user['email'],
                'rol_id'     => $user['rol_id'],
                'rol_nombre' => $user['rol_nombre'],
                'isLoggedIn' => true,
            ];
            $session->set($sessionData);
            return redirect()->to('/home');
        }

        return redirect()->back()->withInput()->with('error', 'El correo o la contraseña son incorrectos.');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/');
    }
}
