<?php
namespace Framework\Cache;

class Cache
{
    protected $path;

    public function __construct()
    {
        $this->path = dirname(__DIR__, 2) . '/runtime/cache';
        if (!is_dir($this->path)) {
            @mkdir($this->path, 0755, true);
        }
    }

    public function get($key)
    {
        $file = $this->file($key);
        if (!file_exists($file)) {
            return null;
        }
        $data = @include $file;
        if (!is_array($data) || !isset($data['expire'])) {
            return null;
        }
        if ($data['expire'] > 0 && $data['expire'] < time()) {
            @unlink($file);
            return null;
        }
        return $data['value'];
    }

    public function set($key, $value, $ttl = 0)
    {
        $expire = $ttl > 0 ? time() + $ttl : 0;
        $content = "<?php\nreturn " . var_export(['expire' => $expire, 'value' => $value], true) . ";\n";
        return @file_put_contents($this->file($key), $content) !== false;
    }

    public function delete($key)
    {
        $file = $this->file($key);
        if (file_exists($file)) {
            return @unlink($file);
        }
        return true;
    }

    public function has($key)
    {
        return $this->get($key) !== null;
    }

    public function flush()
    {
        $files = glob($this->path . '/*.php');
        foreach ($files as $file) {
            @unlink($file);
        }
    }

    protected function file($key)
    {
        return $this->path . '/' . md5($key) . '.php';
    }
}
