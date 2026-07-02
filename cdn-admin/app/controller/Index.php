<?php
declare(strict_types=1);

namespace app\controller;

use app\service\DataService;
use think\Response;

class Index
{
    protected DataService $dataService;

    public function __construct(DataService $dataService)
    {
        $this->dataService = $dataService;
    }

    public function index(): Response
    {
        // 未安装时跳转到安装向导
        if (!$this->dataService->isInstalled()) {
            return redirect('/install');
        }

        $htmlFile = public_path() . 'index.html';

        if (!file_exists($htmlFile)) {
            return Response::create(
                "index.html 不存在。请先执行 npm run build 生成构建产物。\n",
                'html',
                500
            )->header(['Content-Type' => 'text/plain; charset=utf-8']);
        }

        $html = file_get_contents($htmlFile);

        if ($html === false) {
            return Response::create(
                "无法读取 index.html。\n",
                'html',
                500
            )->header(['Content-Type' => 'text/plain; charset=utf-8']);
        }

        // 注入运行环境标识，前端可据此判断是否在 PHP 主机运行
        $envScript = '<script>window.__CDN_ADMIN_RUNTIME__="php";</script>';
        $html = str_replace('<head>', '<head>' . $envScript, $html);

        return Response::create($html, 'html', 200)->header([
            'Content-Type' => 'text/html; charset=utf-8',
        ]);
    }

    public function spa(string $path = ''): Response
    {
        return $this->index();
    }
}
