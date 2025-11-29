<?php

namespace App\Services;

use App\Models\User;

class NavigationService
{
    public function getUserMenu(User $user = null)
    {
        $user = $user ?: auth()->user();
        
        // Если пользователь не авторизован, возвращаем пустой массив
        if (!$user) {
            return collect();
        }
        
        return $user->getAccessibleMenu();
    }
}