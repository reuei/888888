# CloudShield CDN REST API 参考

所有 API 均以 JSON 格式交互，基础路径为 `/api`。若部署在子目录，则实际路径为 `/{subdir}/api`。

## 通用约定

- 请求方式：GET / POST / PUT / DELETE
- 内容类型：`application/json`
- 响应格式：`application/json`
- 统一错误响应：`{"message":"错误描述"}`，HTTP 状态码 4xx/5xx

## 认证

当前版本采用简单的基于账号密码的登录接口，登录成功后前端保存角色信息。后续版本可扩展为 Token/JWT。

### POST /api/login

请求体：

```json
{
  "account": "admin",
  "password": "admin123",
  "role": "s"
}
```

响应：

```json
{
  "success": true,
  "role": "s",
  "account": "admin"
}
```

### GET /api/me

获取当前登录用户信息。

响应：

```json
{
  "role": "s",
  "account": "admin",
  "isAdmin": true
}
```

## 资源 CRUD

资源路径：`/api/:resource`

支持的资源：

`articles`, `coupons`, `skus`, `packages`, `merchants`, `users`, `orders`, `categories`, `adSlots`, `complaints`, `gateways`, `nodes`, `products`, `inviteCodes`, `userGroups`, `userLevels`, `realnameRecords`, `roles`, `backupRecords`, `sites`, `myPackages`, `bOrders`, `invoices`, `whitelistRecords`, `financeRecords`, `settlementRecords`, `commissionRecords`, `operationLogs`, `apiDocs`, `notifications`, `agents`, `agentProducts`, `pcTemplates`, `mobileTemplates`, `cardTemplates`, `luckyNumbers`, `dailyStats`, `merchantStats`, `userGrowthStats`

### GET /api/:resource

列出全部资源。

### GET /api/:resource?id={id}

查询单条记录。

### GET /api/:resource?search={keyword}

按关键字搜索 JSON 数据。

### GET /api/:resource?page={page}&limit={limit}&search={keyword}

分页查询。

响应示例：

```json
{
  "list": [...],
  "total": 100,
  "page": 1,
  "limit": 20,
  "pages": 5
}
```

### POST /api/:resource

创建记录。

### PUT /api/:resource?id={id}

更新记录。

### DELETE /api/:resource?id={id}

删除记录。

## 健康检查

### GET /api/health

响应：

```json
{
  "ok": true,
  "runtime": "php"
}
```
