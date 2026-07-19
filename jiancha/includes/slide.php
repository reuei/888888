<?php
function getSliderHtml() {
    try {
        $slides = DB::fetchAll("SELECT * FROM slides WHERE status=1 ORDER BY sort_order ASC");
    } catch (Exception $e) { $slides = []; }
    if (!$slides) return '';
    $html = '<div class="slider" id="slider">';
    $html .= '<div class="slider-track" id="sliderTrack">';
    foreach ($slides as $s) {
        $link = $s['link'] ?: 'javascript:void(0)';
        $html .= '<a href="' . e($link) . '" class="slider-item">';
        if ($s['image']) $html .= '<img src="' . SITE_URL . UPLOAD_URL . e($s['image']) . '" alt="' . e($s['title']) . '" loading="lazy">';
        if ($s['title']) $html .= '<div class="slider-caption">' . e($s['title']) . '</div>';
        $html .= '</a>';
    }
    $html .= '</div>';
    if (count($slides) > 1) {
        $html .= '<div class="slider-progress"><div class="slider-progress-bar" id="sliderBar"></div></div>';
        $html .= '<button class="slider-arrow slider-prev" id="sliderPrev">&#8249;</button>';
        $html .= '<button class="slider-arrow slider-next" id="sliderNext">&#8250;</button>';
    }
    $html .= '</div>';
    return $html;
}
