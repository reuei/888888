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
        return $this->serveHtml('sales');
    }

    public function cdn(): Response
    {
        return $this->serveHtml('cdn');
    }

    public function spa(string $path = ''): Response
    {
        // 以 cdn 开头的路径返回 CDN 站点，其余返回销售系统
        if (str_starts_with($path, 'cdn')) {
            return $this->serveHtml('cdn');
        }
        return $this->serveHtml('sales');
    }

    /**
     * 提供销售系统或 CDN 站点的 HTML 入口
     */
    protected function serveHtml(string $site): Response
    {
        if (!$this->dataService->isInstalled()) {
            return redirect('./install');
        }

        $htmlFile = public_path() . $site . '.html';

        if (!file_exists($htmlFile)) {
            return Response::create(
                $site . ".html 不存在。请先执行 npm run build 生成构建产物。\n",
                'html',
                500
            )->header(['Content-Type' => 'text/plain; charset=utf-8']);
        }

        $html = file_get_contents($htmlFile);

        if ($html === false) {
            return Response::create(
                "无法读取 " . $site . ".html。\n",
                'html',
                500
            )->header(['Content-Type' => 'text/plain; charset=utf-8']);
        }

        // 计算部署根路径，支持虚拟主机子目录
        $basePath = rtrim(request()->root(), '/') . '/';
        if ($basePath === '//') {
            $basePath = '/';
        }

        // 注入运行环境标识与 API 基准地址
        $headInject = sprintf(
            '<base href="%s"><script>window.__CDN_ADMIN_RUNTIME__="php";window.__CDN_ADMIN_API_BASE__="./api";window.__CDN_ADMIN_SITE__="%s";</script>',
            htmlspecialchars($basePath, ENT_QUOTES, 'UTF-8'),
            htmlspecialchars($site, ENT_QUOTES, 'UTF-8')
        );
        $html = str_replace('<head>', '<head>' . $headInject, $html);

        return Response::create($html, 'html', 200)->header([
            'Content-Type' => 'text/html; charset=utf-8',
        ]);
    }
}
