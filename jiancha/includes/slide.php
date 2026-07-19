<?php
function getSliderHtml() {
    try {
        $slides = DB::fetchAll("SELECT * FROM slides WHERE status=1 ORDER BY sort_order ASC, id ASC");
    } catch (Exception $e) {
        try { DB::getInstance()->exec("CREATE TABLE IF NOT EXISTS slides (id INTEGER PRIMARY KEY AUTOINCREMENT, title TEXT, image TEXT, link TEXT, sort_order INTEGER DEFAULT 0, status INTEGER DEFAULT 1, create_time DATETIME DEFAULT CURRENT_TIMESTAMP)"); } catch (Exception $e2) {}
        return '';
    }
    if (empty($slides)) return '';
    $h = '<div class="slider"><div class="slider-track">';
    foreach ($slides as $s) {
        $bg = $s['image'] ? ' style="background-image:url(' . SITE_URL . UPLOAD_URL . e($s['image']) . ')"' : '';
        $h .= '<div class="slider-item"' . $bg . '><div class="slider-cap"><h3>' . e($s['title']) . '</h3>';
        if ($s['link']) $h .= '<p>' . e($s['link']) . '</p>';
        $h .= '</div></div>';
    }
    $h .= '</div><div class="slider-progress"><div class="slider-progress-bar"></div></div><div class="slider-dots">';
    foreach ($slides as $i => $s) $h .= '<span class="' . ($i === 0 ? 'on' : '') . '"></span>';
    $h .= '</div><button class="slider-arrow prev">&lsaquo;</button><button class="slider-arrow next">&rsaquo;</button></div>';
    return $h;
}