#!/bin/bash
# QEEFG 补丁包 7.0.1 GitHub Release 上传脚本
# 用法:
#   1. 配置 GitHub 凭证: gh auth login
#   2. 运行: ./upload-7.0.1.sh

set -e

REPO="reuei/888888"
TAG="v7.0.1"
TITLE="QEEFG 授权站 v7.0.1 补丁包"
ZIP_FILE="qeefg-patch-7.0.1.zip"

if [ ! -f "$ZIP_FILE" ]; then
  echo "错误: 找不到 $ZIP_FILE"
  exit 1
fi

echo "=========================================="
echo "  QEEFG 授权站 v7.0.1 补丁包上传"
echo "=========================================="
echo ""
echo "仓库: $REPO"
echo "标签: $TAG"
echo "文件: $ZIP_FILE ($(du -h $ZIP_FILE | cut -f1))"
echo ""

# 检查 gh 认证
if ! gh auth status >/dev/null 2>&1; then
  echo "未登录 GitHub，正在打开登录..."
  gh auth login
fi

# 创建 Release 并上传文件
echo "正在创建 Release $TAG ..."
gh release create "$TAG" \
  "$ZIP_FILE" \
  --repo "$REPO" \
  --title "$TITLE" \
  --notes-file RELEASE-7.0.1.md \
  --latest

echo ""
echo "✅ 上传完成！"
echo "查看 Release: https://github.com/$REPO/releases/tag/$TAG"
