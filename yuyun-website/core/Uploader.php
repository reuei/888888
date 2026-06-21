<?php
/**
 * 语云科技企业官网 - 文件上传处理类
 */

class Uploader {
    private $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp', 'ico'];
    private $maxSize = 5242880; // 5MB
    private $uploadDir = '';
    private $subDir = 'images';

    public function __construct($subDir = 'images') {
        $this->subDir = $subDir;
        $this->uploadDir = UPLOADS_PATH . $subDir . '/';
    }

    /**
     * 设置允许的文件类型
     */
    public function setAllowedTypes($types) {
        $this->allowedTypes = $types;
        return $this;
    }

    /**
     * 设置最大文件大小
     */
    public function setMaxSize($size) {
        $this->maxSize = $size;
        return $this;
    }

    /**
     * 处理单个文件上传
     */
    public function upload($file) {
        // 检查是否有错误
        if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
            $errorMsg = $this->getUploadErrorMessage($file['error'] ?? UPLOAD_ERR_NO_FILE);
            return ['success' => false, 'message' => $errorMsg];
        }

        // 检查文件大小
        if ($file['size'] > $this->maxSize) {
            return ['success' => false, 'message' => '文件大小超过限制（最大' . round($this->maxSize / 1048576, 1) . 'MB）'];
        }

        // 检查文件类型
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $this->allowedTypes)) {
            return ['success' => false, 'message' => '不支持的文件格式，允许的格式：' . implode(', ', $this->allowedTypes)];
        }

        // MIME类型二次验证
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        $allowedMimes = [
            'image/jpeg', 'image/png', 'image/gif', 'image/svg+xml',
            'image/webp', 'image/x-icon'
        ];

        if (!in_array($mime, $allowedMimes)) {
            return ['success' => false, 'message' => '文件类型验证失败'];
        }

        // 创建目录
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }

        // 生成唯一文件名
        $filename = uniqid(date('Ymd_')) . '_' . preg_replace('/[^a-zA-Z0-9_-]/', '_', pathinfo($file['name'], PATHINFO_FILENAME)) . '.' . $ext;
        $filepath = $this->uploadDir . $filename;

        // 移动文件
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            // 设置权限
            chmod($filepath, 0644);

            $relativePath = 'uploads/' . $this->subDir . '/' . $filename;

            return [
                'success' => true,
                'path' => '/' . $relativePath,
                'url' => '/' . $relativePath,
                'filename' => $filename,
                'original_name' => $file['name'],
                'size' => $file['size'],
                'mime' => $mime
            ];
        }

        return ['success' => false, 'message' => '文件保存失败，请检查目录权限'];
    }

    /**
     * 批量上传
     */
    public function uploadMultiple($files) {
        $results = [];

        // 整理文件数组
        $fileArray = $this->rearrangeFiles($files);

        foreach ($fileArray as $index => $file) {
            $results[] = $this->upload($file);
        }

        return $results;
    }

    /**
     * 删除已上传的文件
     */
    public function delete($filePath) {
        if (empty($filePath)) return true;

        // 安全检查：只允许删除uploads目录下的文件
        $realPath = realpath(YUYUN_ROOT . $filePath);
        $uploadsRealPath = realpath(UPLOADS_PATH);

        if ($realPath && strpos($realPath, $uploadsRealPath) === 0 && file_exists($realPath)) {
            return unlink($realPath);
        }

        return false;
    }

    /**
     * Base64图片上传
     */
    public function uploadBase64($base64Data, $extension = 'png') {
        if (!in_array($extension, $this->allowedTypes)) {
            return ['success' => false, 'message' => '不支持的文件格式'];
        }

        // 移除Base64前缀
        if (strpos($base64Data, ',') !== false) {
            $base64Data = substr($base64Data, strpos($base64Data, ',') + 1);
        }

        $imageData = base64_decode($base64Data);
        if ($imageData === false) {
            return ['success' => false, 'message' => 'Base64解码失败'];
        }

        // 创建目录
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }

        $filename = uniqid(date('Ymd_')) . '_base64.' . $extension;
        $filepath = $this->uploadDir . $filename;

        if (file_put_contents($filepath, $imageData)) {
            chmod($filepath, 0644);
            $relativePath = 'uploads/' . $this->subDir . '/' . $filename;

            return [
                'success' => true,
                'path' => '/' . $relativePath,
                'url' => '/' . $relativePath,
                'filename' => $filename
            ];
        }

        return ['success' => false, 'message' => '文件保存失败'];
    }

    /**
     * 从URL下载并保存
     */
    public function downloadFromUrl($url) {
        $ctx = stream_context_create([
            'http' => [
                'timeout' => 30,
                'follow_location' => true,
                'user_agent' => 'YuyunUploader/1.0'
            ]
        ]);

        $imageData = @file_get_contents($url, false, $ctx);
        if ($imageData === false) {
            return ['success' => false, 'message' => '无法从URL获取图片'];
        }

        // 检测类型
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $tempFile = tempnam(sys_get_temp_dir(), 'yuyun_upload');
        file_put_contents($tempFile, $imageData);
        $mime = finfo_file($finfo, $tempFile);
        finfo_close($finfo);
        @unlink($tempFile);

        $mimeMap = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/svg+xml' => 'svg',
            'image/webp' => 'webp'
        ];

        $ext = $mimeMap[$mime] ?? 'jpg';
        return $this->uploadBase64(base64_encode($imageData), $ext);
    }

    /**
     * 整理多文件上传数组
     */
    private function rearrangeFiles($files) {
        $result = [];

        if (!isset($files['name'])) {
            return [$files];
        }

        if (is_array($files['name'])) {
            foreach ($files['name'] as $key => $name) {
                $result[$key] = [
                    'name' => $name,
                    'type' => $files['type'][$key],
                    'tmp_name' => $files['tmp_name'][$key],
                    'error' => $files['error'][$key],
                    'size' => $files['size'][$key]
                ];
            }
        } else {
            $result[] = $files;
        }

        return $result;
    }

    /**
     * 获取上传错误消息
     */
    private function getUploadErrorMessage($errorCode) {
        $errors = [
            UPLOAD_ERR_INI_SIZE => '文件大小超过服务器限制',
            UPLOAD_ERR_FORM_SIZE => '文件大小超过表单限制',
            UPLOAD_ERR_PARTIAL => '文件只有部分被上传',
            UPLOAD_ERR_NO_FILE => '没有选择文件',
            UPLOAD_ERR_NO_TMP_DIR => '缺少临时文件夹',
            UPLOAD_ERR_CANT_WRITE => '文件写入失败',
            UPLOAD_ERR_EXTENSION => '文件上传被扩展程序中断'
        ];

        return $errors[$errorCode] ?? '未知上传错误';
    }
}
