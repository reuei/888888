<?php
declare(strict_types=1);

namespace app\controller;

use think\Response;

class Health
{
    public function index(): Response
    {
        return json(['ok' => true, 'runtime' => 'php']);
    }
}
