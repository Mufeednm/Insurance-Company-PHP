<?php

if (!function_exists('renderMenu')) {

    function renderMenu($menuItems, $permissionModel, $userId, $isAdmin, $level = 0, $parentId = 'navbar-nav', $currentPage = '')
    {
        foreach ($menuItems as $menu) {

            // Check permission
            if (!$isAdmin) {
                $hasPermission = true;

                if (isset($menu['permission'])) {
                    $hasPermission = $permissionModel->hasPermission($menu['permission']);
                } elseif (isset($menu['submenu'])) {
                    $filteredSubmenu = filterSubmenu($menu['submenu'], $permissionModel, $userId);
                    $hasPermission = !empty($filteredSubmenu);
                    $menu['submenu'] = $filteredSubmenu;
                }

                if (!$hasPermission) {
                    continue;
                }
            }

            $hasSubmenu = isset($menu['submenu']) && !empty($menu['submenu']);
            $collapseId = 'collapse_' . md5($menu['name']);
            $url = $menu['url'] ?? '#';
            $icon = $menu['icon'] ?? '';
            $isActive = (!empty($menu['activate']) && $menu['activate'] == $currentPage);

            echo '<li class="nav-item">';

            // Top level link
            if ($hasSubmenu) {
                echo '<a class="nav-link menu-link" href="#' . $collapseId . '" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="' . $collapseId . '">';
            } else {
                echo '<a class="nav-link menu-link" href="' . $url . '">';
            }

            echo $icon . ' <span>' . $menu['name'] . '</span>';
            echo '</a>';

            // Submenu block
            if ($hasSubmenu) {
                echo '<div class="collapse menu-dropdown" id="' . $collapseId . '">';
                echo '<ul class="nav nav-sm flex-column">';
                foreach ($menu['submenu'] as $child) {
                    if (!$isAdmin && isset($child['permission']) && !$permissionModel->hasPermission($child['permission'])) {
                        continue;
                    }

                    if (isset($child['submenu'])) {
                        // Recursive render for 3rd-level submenu
                        renderMenu([$child], $permissionModel, $userId, $isAdmin, $level + 1, $collapseId, $currentPage);
                    } else {
                        echo '<li class="nav-item">';
                        echo '<a href="' . ($child['url'] ?? '#') . '" class="nav-link">' . $child['name'] . '</a>';
                        echo '</li>';
                    }
                }
                echo '</ul>';
                echo '</div>';
            }

            echo '</li>';
        }
    }

    function filterSubmenu($submenu, $permissionModel, $userId)
    {
        $filtered = [];
        foreach ($submenu as $child) {
            if (isset($child['permission']) && !$permissionModel->hasPermission($child['permission'])) {
                continue;
            }

            if (isset($child['submenu'])) {
                $child['submenu'] = filterSubmenu($child['submenu'], $permissionModel, $userId);
            }

            if (isset($child['permission']) || (isset($child['submenu']) && !empty($child['submenu']))) {
                $filtered[] = $child;
            }
        }
        return $filtered;
    }
}
