<?php
function getSliderHtml() {
    if (!DB::tableExists('slides')) {
        DB::getInstance()->exec("CREATE TABLE slides (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title TEXT,
            image TEXT,
            link TEXT,
            sort_order INTEGER DEFAULT 0,
            status INTEGER DEFAULT 1,
            create_time DATETIME DEFAULT CURRENT_TIMESTAMP
        )");
        return '';
    }
    
    $slides = @DB::fetchAll("SELECT * FROM slides WHERE status=1 ORDER BY sort_order ASC, id ASC");
    if (empty($slides)) {
        return '';
    }
    
    $html = '<div class="slider-container"><div class="slider-wrapper">';
    foreach ($slides as $slide) {
        $bg = $slide['image'] ? BASE_URL . UPLOAD_URL . $slide['image'] : '';
        $html .= '<div class="slide-item" style="background-image:url(\'' . $bg . '\')">';
        $html .= '<div class="slide-content">';
        $html .= '<h3>' . e($slide['title']) . '</h3>';
        if ($slide['link']) {
            $html .= '<a href="' . e($slide['link']) . '" class="btn">查看详情</a>';
        }
        $html .= '</div></div>';
    }
    $html .= '</div>';
    $html .= '<div class="slider-progress"><div class="progress-bar"></div></div>';
    $html .= '<div class="slider-dots">';
    foreach ($slides as $i => $s) {
        $html .= '<div class="dot' . ($i === 0 ? ' active' : '') . '"></div>';
    }
    $html .= '</div>';
    $html .= '<button class="slider-prev">&lsaquo;</button>';
    $html .= '<button class="slider-next">&rsaquo;</button>';
    $html .= '</div>';
    
    return $html;
}