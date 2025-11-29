<?php

if (!function_exists('sortable_icon')) {
    function sortable_icon($column, $title)
    {
        $direction = request('direction') === 'asc' ? 'desc' : 'asc';

        $url = request()->fullUrlWithQuery([
            'sort' => $column,
            'direction' => $direction,
        ]);

        $icon = '';

        if (request('sort') === $column) {
            $icon = request('direction') === 'asc'
                ? '↑'
                : '↓';
        }

        return "<a href=\"$url\" class=\"text-decoration-none fw-bold\">$title <span style='font-size:13px'>$icon</span></a>";
    }
}
