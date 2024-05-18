<?php

namespace App\Components;

use App\Models\User;

class UserTableComponent
{
    public static function generateTable()
    {
        // Obtener todos los usuarios
        $users = User::all();

        // Generar la tabla HTML
        $html = '<table>';
        $html .= '<thead><tr><th>ID</th><th>Nombre</th><th>Email</th></tr></thead>';
        $html .= '<tbody>';
        foreach ($users as $user) {
            $html .= '<tr>';
            $html .= '<td>' . $user->id . '</td>';
            $html .= '<td>' . $user->name . '</td>';
            $html .= '<td>' . $user->email . '</td>';
            $html .= '</tr>';
        }
        $html .= '</tbody>';
        $html .= '</table>';

        return $html;
    }
}
