<?php
/**
 * 语云科技 - 内容管理系统
 * 子Tab: 轮播图管理 / 合作伙伴 / 资质证书 / 友情链接
 */

session_start();
define('YUYUN_ROOT', dirname(__DIR__));
require_once YUYUN_ROOT . '/core/Functions.php';
require_admin();

$activeTab = $_GET['tab'] ?? 'banners';
$successMsg = '';

// 获取各类型内容数据
$banners = get_content('banners') ?: [];
$partners = get_content('partners') ?: [];
$certificates = get_content('certificates') ?: [];
$links = get_content('links') ?: [];

// 处理AJAX请求
if (isset($_GET['ajax'])) {
    header('Content-Type: application/json; charset=utf-8');

    $type = $_GET['type'] ?? '';
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'create':
            $data = json_decode($_POST['data'] ?? '{}', true);
            $items = ${$type} ?: [];
            $maxId = 0;
            foreach ($items as $item) { if (($item['id'] ?? 0) > $maxId) $maxId = $item['id']; }
            $data['id'] = $maxId + 1;
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['updated_at'] = date('Y-m-d H:i:s');
            $items[] = $data;
            save_content($type, $items);
            ${$type} = $items;
            echo json_encode(['code' => 200, 'message' => '创建成功', 'data' => $data]);
            break;

        case 'update':
            $id = intval($_POST['id'] ?? 0);
            $data = json_decode($_POST['data'] ?? '{}', true);
            $items = ${$type} ?: [];
            foreach ($items as &$item) {
                if ((int)$item['id'] === $id) {
                    $data['id'] = $id;
                    $data['updated_at'] = date('Y-m-d H:i:s');
                    $item = array_merge($item, $data);
                    break;
                }
            }
            unset($item);
            save_content($type, $items);
            ${$type} = $items;
            echo json_encode(['code' => 200, 'message' => '更新成功']);
            break;

        case 'delete':
            $id = intval($_POST['id'] ?? 0);
            $items = ${$type} ?: [];
            $newItems = array_filter($items, fn($it) => (int)$it['id'] !== $id);
            save_content($type, array_values($newItems));
            ${$type} = array_values($newItems);
            echo json_encode(['code' => 200, 'message' => '删除成功']);
            break;

        default:
            echo json_encode(['code' => 400, 'message' => '未知操作']);
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>内容管理 - 语云科技后台</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <meta name="csrf-token" content="<?php echo csrf_token(); ?>">
</head>
<body>
    <!-- 侧边栏 -->
    <?php include __DIR__ . '/_sidebar.php'; ?>

    <!-- 顶部导航栏 -->
    <header class="header">
        <div class="header-left">
            <button class="mobile-menu-btn" onclick="document.getElementById('sidebar').classList.add('mobile-show'); document.querySelector('.sidebar-overlay').classList.add('show');">
                <i class="fas fa-bars"></i>
            </button>
            <div class="breadcrumb">
                <a href="dashboard.php"><i class="fas fa-home"></i></a>
                <span class="breadcrumb-separator">/</span>
                <span>内容管理</span>
            </div>
        </div>
        <div class="header-right">
            <div class="user-dropdown">
                <div class="user-avatar"><?php echo mb_substr($_SESSION['admin_name'] ?? '管', 0, 1); ?></div>
                <div class="user-info">
                    <div class="name"><?php echo e($_SESSION['admin_name'] ?? '管理员'); ?></div>
                    <div class="role">超级管理员</div>
                </div>
            </div>
        </div>
    </header>

    <!-- 主内容区 -->
    <main class="main-content">
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-th-large"></i> 内容管理</h3>
            </div>

            <!-- Tab导航 -->
            <div class="tabs" id="contentTabs">
                <div class="tab-item <?php echo $activeTab === 'banners' ? 'active' : ''; ?>" data-tab="banners">
                    <i class="fas fa-images"></i> 轮播图 (<?php echo count($banners); ?>)
                </div>
                <div class="tab-item <?php echo $activeTab === 'partners' ? 'active' : ''; ?>" data-tab="partners">
                    <i class="fas fa-handshake"></i> 合作伙伴 (<?php echo count($partners); ?>)
                </div>
                <div class="tab-item <?php echo $activeTab === 'certificates' ? 'active' : ''; ?>" data-tab="certificates">
                    <i class="fas fa-certificate"></i> 资质证书 (<?php echo count($certificates); ?>)
                </div>
                <div class="tab-item <?php echo $activeTab === 'links' ? 'active' : ''; ?>" data-tab="links">
                    <i class="fas fa-link"></i> 友情链接 (<?php echo count($links); ?>)
                </div>
            </div>

            <!-- ========== 轮播图管理 ========== -->
            <div class="tab-pane <?php echo $activeTab === 'banners' ? 'active' : ''; ?>" id="tab-banners">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
                    <p style="color:var(--text-secondary); font-size:14px;">
                        管理首页轮播图，支持标题、副标题、图片和跳转链接。
                    </p>
                    <button class="btn btn-primary btn-sm" onclick="openBannerModal()">
                        <i class="fas fa-plus"></i> 新增轮播图
                    </button>
                </div>

                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th width="60">ID</th>
                                <th width="80">预览</th>
                                <th>标题</th>
                                <th>副标题</th>
                                <th>链接</th>
                                <th width="70">排序</th>
                                <th width="120">操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($banners)): ?>
                                <tr><td colspan="7" class="text-center text-muted" style="padding:40px;">暂无轮播图数据，点击上方按钮添加</td></tr>
                            <?php else:
                                usort($banners, fn($a, $b) => ($a['sort_order'] ?? 0) <=> ($b['sort_order'] ?? 0));
                                foreach ($banners as $banner):
                            ?>
                                <tr data-id="<?php echo $banner['id']; ?>">
                                    <td><?php echo $banner['id']; ?></td>
                                    <td>
                                        <?php if (!empty($banner['image'])): ?>
                                            <img src="<?php echo e($banner['image']); ?>" style="width:60px; height:36px; object-fit:cover; border-radius:4px;" alt="">
                                        <?php else: ?>
                                            <span class="text-muted">无图片</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><strong><?php echo e($banner['title'] ?? '-'); ?></strong></td>
                                    <td><?php echo e(mb_substr($banner['subtitle'] ?? '', 0, 30)); ?></td>
                                    <td>
                                        <?php if (!empty($banner['link'])): ?>
                                            <a href="<?php echo e($banner['link']); ?>" target="_blank" title="<?php echo e($banner['link']); ?>"><?php echo e(mb_substr($banner['link'], 0, 25)); ?>...</a>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo $banner['sort_order'] ?? 0; ?></td>
                                    <td>
                                        <div class="table-actions">
                                            <button class="action-btn edit" title="编辑" onclick="editBanner(<?php echo htmlspecialchars(json_encode($banner), ENT_QUOTES); ?>)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="action-btn delete" title="删除" data-delete data-name="轮播图 #<?php echo $banner['id']; ?>" data-action="delete" data-type="banners" data-id="<?php echo $banner['id']; ?>">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ========== 合作伙伴管理 ========== -->
            <div class="tab-pane <?php echo $activeTab === 'partners' ? 'active' : ''; ?>" id="tab-partners">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
                    <p style="color:var(--text-secondary); font-size:14px;">管理合作伙伴展示信息。</p>
                    <button class="btn btn-primary btn-sm" onclick="openPartnerModal()">
                        <i class="fas fa-plus"></i> 新增合作伙伴
                    </button>
                </div>

                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th width="60">ID</th>
                                <th width="100">Logo</th>
                                <th>名称</th>
                                <th>链接URL</th>
                                <th width="120">操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($partners)): ?>
                                <tr><td colspan="5" class="text-center text-muted" style="padding:40px;">暂无合作伙伴数据</td></tr>
                            <?php else:
                                foreach ($partners as $partner):
                            ?>
                                <tr data-id="<?php echo $partner['id']; ?>">
                                    <td><?php echo $partner['id']; ?></td>
                                    <td>
                                        <?php if (!empty($partner['logo'])): ?>
                                            <img src="<?php echo e($partner['logo']); ?>" style="width:60px; height:36px; object-fit:contain; border-radius:4px; background:#fff; padding:2px;" alt="">
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><strong><?php echo e($partner['name'] ?? '-'); ?></strong></td>
                                    <td>
                                        <?php if (!empty($partner['url'])): ?>
                                            <a href="<?php echo e($partner['url']); ?>" target="_blank"><?php echo e($partner['url']); ?></a>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="table-actions">
                                            <button class="action-btn edit" onclick="editPartner(<?php echo htmlspecialchars(json_encode($partner), ENT_QUOTES); ?>)" title="编辑"><i class="fas fa-edit"></i></button>
                                            <button class="action-btn delete" data-delete data-name="合作伙伴「<?php echo e($partner['name'] ?? ''); ?>」" data-action="delete" data-type="partners" data-id="<?php echo $partner['id']; ?>" title="删除"><i class="fas fa-trash"></i></button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ========== 资质证书管理 ========== -->
            <div class="tab-pane <?php echo $activeTab === 'certificates' ? 'active' : ''; ?>" id="tab-certificates">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
                    <p style="color:var(--text-secondary); font-size:14px;">展示企业资质证书和荣誉资质。</p>
                    <button class="btn btn-primary btn-sm" onclick="openCertificateModal()">
                        <i class="fas fa-plus"></i> 新增证书
                    </button>
                </div>

                <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(220px, 1fr)); gap:16px;">
                    <?php if (empty($certificates)): ?>
                        <div class="empty-state" style="grid-column:1/-1;">
                            <i class="fas fa-certificate"></i>
                            <h3>暂无证书数据</h3>
                            <p>点击上方按钮添加资质证书</p>
                        </div>
                    <?php else:
                        foreach ($certificates as $cert):
                    ?>
                        <div class="staff-card" style="padding:16px;">
                            <?php if (!empty($cert['image'])): ?>
                                <img src="<?php echo e($cert['image']); ?>" alt="<?php echo e($cert['name'] ?? ''); ?>" style="max-width:100%; max-height:150px; object-fit:contain; border-radius:8px; margin-bottom:12px; background:#fff; padding:8px;">
                            <?php endif; ?>
                            <h5 style="font-size:14px; margin-bottom:4px;"><?php echo e($cert['name'] ?? '未命名证书'); ?></h5>
                            <p style="font-size:12px; color:var(--text-muted); margin-bottom:12px;"><?php echo e($cert['description'] ?? ''); ?></p>
                            <div style="display:flex; gap:8px; justify-content:center;">
                                <button class="action-btn edit" onclick="editCertificate(<?php echo htmlspecialchars(json_encode($cert), ENT_QUOTES); ?>)" title="编辑"><i class="fas fa-edit"></i></button>
                                <button class="action-btn delete" data-delete data-name="证书「<?php echo e($cert['name'] ?? ''); ?>」" data-action="delete" data-type="certificates" data-id="<?php echo $cert['id']; ?>" title="删除"><i class="fas fa-trash"></i></button>
                            </div>
                        </div>
                    <?php endforeach; endif; ?>
                </div>
            </div>

            <!-- ========== 友情链接管理 ========== -->
            <div class="tab-pane <?php echo $activeTab === 'links' ? 'active' : ''; ?>" id="tab-links">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
                    <p style="color:var(--text-secondary); font-size:14px;">管理友情链接。</p>
                    <button class="btn btn-primary btn-sm" onclick="openLinkModal()">
                        <i class="fas fa-plus"></i> 新增链接
                    </button>
                </div>

                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th width="60">ID</th>
                                <th>名称</th>
                                <th>URL地址</th>
                                <th width="70">排序</th>
                                <th width="120">操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($links)): ?>
                                <tr><td colspan="5" class="text-center text-muted" style="padding:40px;">暂无友情链接</td></tr>
                            <?php else:
                                usort($links, fn($a, $b) => ($a['sort_order'] ?? 0) <=> ($b['sort_order'] ?? 0));
                                foreach ($links as $link):
                            ?>
                                <tr data-id="<?php echo $link['id']; ?>">
                                    <td><?php echo $link['id']; ?></td>
                                    <td><strong><?php echo e($link['name'] ?? '-'); ?></strong></td>
                                    <td><a href="<?php echo e($link['url'] ?? '#'); ?>" target="_blank"><?php echo e($link['url'] ?? '-'); ?></a></td>
                                    <td><?php echo $link['sort_order'] ?? 0; ?></td>
                                    <td>
                                        <div class="table-actions">
                                            <button class="action-btn edit" onclick="editLink(<?php echo htmlspecialchars(json_encode($link), ENT_QUOTES); ?>)" title="编辑"><i class="fas fa-edit"></i></button>
                                            <button class="action-btn delete" data-delete data-name="链接「<?php echo e($link['name'] ?? ''); ?>」" data-action="delete" data-type="links" data-id="<?php echo $link['id']; ?>" title="删除"><i class="fas fa-trash"></i></button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <!-- ========== 轮播图模态框 ========== -->
    <div class="modal-overlay" id="bannerModal">
        <div class="modal modal-lg">
            <div class="modal-header">
                <h3 id="bannerModalTitle">新增轮播图</h3>
                <button type="button" class="modal-close">&times;</button>
            </div>
            <form id="bannerForm" onsubmit="return saveContent(event, 'banners')">
                <div class="modal-body">
                    <input type="hidden" name="id" id="bannerId">
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">标题 <span class="required">*</span></label>
                            <input type="text" name="title" class="form-control" required placeholder="轮播图主标题">
                        </div>
                        <div class="form-group">
                            <label class="form-label">副标题</label>
                            <input type="text" name="subtitle" class="form-control" placeholder="轮播图副标题">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">轮播图片 <span class="required">*</span></label>
                        <div class="upload-area" data-upload="bannerImage">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <p>点击或拖拽上传图片</p>
                            <p style="font-size:12px; color:var(--text-muted);">建议尺寸 1920x600px，支持 JPG/PNG/WebP</p>
                            <input type="file" id="bannerImage" name="image" accept="image/*" style="display:none;">
                            <img src="" class="upload-preview" style="display:none; max-width:100%; margin-top:10px; border-radius:8px;">
                        </div>
                        <input type="hidden" name="image" id="bannerImagePath">
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">跳转链接</label>
                            <input type="url" name="link" class="form-control" placeholder="点击跳转的URL（可选）">
                        </div>
                        <div class="form-group">
                            <label class="form-label">排序权重</label>
                            <input type="number" name="sort_order" class="form-control" value="0" min="0" placeholder="数字越小越靠前">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" onclick="closeModal('bannerModal')">取消</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> 保存</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========== 合作伙伴模态框 ========== -->
    <div class="modal-overlay" id="partnerModal">
        <div class="modal">
            <div class="modal-header">
                <h3 id="partnerModalTitle">新增合作伙伴</h3>
                <button type="button" class="modal-close">&times;</button>
            </div>
            <form id="partnerForm" onsubmit="return saveContent(event, 'partners')">
                <div class="modal-body">
                    <input type="hidden" name="id" id="partnerId">
                    <div class="form-group">
                        <label class="form-label">名称 <span class="required">*</span></label>
                        <input type="text" name="name" class="form-control" required placeholder="合作伙伴名称">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Logo图片</label>
                        <div class="upload-area" data-upload="partnerLogo">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <p>点击上传Logo</p>
                            <input type="file" id="partnerLogo" name="logo" accept="image/*" style="display:none;">
                            <img src="" class="upload-preview" style="display:none; max-width:150px; margin-top:10px; border-radius:8px;">
                        </div>
                        <input type="hidden" name="logo" id="partnerLogoPath">
                    </div>
                    <div class="form-group">
                        <label class="form-label">链接URL</label>
                        <input type="url" name="url" class="form-control" placeholder="合作伙伴官网地址">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" onclick="closeModal('partnerModal')">取消</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> 保存</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========== 证书模态框 ========== -->
    <div class="modal-overlay" id="certificateModal">
        <div class="modal">
            <div class="modal-header">
                <h3 id="certificateModalTitle">新增证书</h3>
                <button type="button" class="modal-close">&times;</button>
            </div>
            <form id="certificateForm" onsubmit="return saveContent(event, 'certificates')">
                <div class="modal-body">
                    <input type="hidden" name="id" id="certificateId">
                    <div class="form-group">
                        <label class="form-label">证书名称 <span class="required">*</span></label>
                        <input type="text" name="name" class="form-control" required placeholder="如：ISO9001质量管理体系认证">
                    </div>
                    <div class="form-group">
                        <label class="form-label">证书图片</label>
                        <div class="upload-area" data-upload="certImage">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <p>点击上传证书扫描件</p>
                            <input type="file" id="certImage" name="image" accept="image/*" style="display:none;">
                            <img src="" class="upload-preview" style="display:none; max-width:100%; margin-top:10px; border-radius:8px;">
                        </div>
                        <input type="hidden" name="image" id="certImagePath">
                    </div>
                    <div class="form-group">
                        <label class="form-label">描述说明</label>
                        <textarea name="description" class="form-control" rows="2" placeholder="证书简要描述"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" onclick="closeModal('certificateModal')">取消</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> 保存</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========== 友情链接模态框 ========== -->
    <div class="modal-overlay" id="linkModal">
        <div class="modal">
            <div class="modal-header">
                <h3 id="linkModalTitle">新增链接</h3>
                <button type="button" class="modal-close">&times;</button>
            </div>
            <form id="linkForm" onsubmit="return saveContent(event, 'links')">
                <div class="modal-body">
                    <input type="hidden" name="id" id="linkId">
                    <div class="form-group">
                        <label class="form-label">网站名称 <span class="required">*</span></label>
                        <input type="text" name="name" class="form-control" required placeholder="链接显示名称">
                    </div>
                    <div class="form-group">
                        <label class="form-label">URL地址 <span class="required">*</span></label>
                        <input type="url" name="url" class="form-control" required placeholder="https://example.com">
                    </div>
                    <div class="form-group">
                        <label class="form-label">排序权重</label>
                        <input type="number" name="sort_order" class="form-control" value="0" min="0">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" onclick="closeModal('linkModal')">取消</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> 保存</button>
                </div>
            </form>
        </div>
    </div>

    <script src="../assets/js/admin.js"></script>
    <script>
        // Tab切换
        document.querySelectorAll('#contentTabs .tab-item').forEach(tab => {
            tab.addEventListener('click', () => {
                document.querySelectorAll('#contentTabs .tab-item').forEach(t => t.classList.remove('active'));
                document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
                tab.classList.add('active');
                document.getElementById('tab-' + tab.dataset.tab)?.classList.add('active');
            });
        });

        // 打开模态框
        function openBannerModal() {
            document.getElementById('bannerModalTitle').textContent = '新增轮播图';
            document.getElementById('bannerForm').reset();
            document.getElementById('bannerId').value = '';
            document.querySelector('#bannerModal .upload-preview').style.display = 'none';
            Modal.open('bannerModal');
        }

        function openPartnerModal() {
            document.getElementById('partnerModalTitle').textContent = '新增合作伙伴';
            document.getElementById('partnerForm').reset();
            document.getElementById('partnerId').value = '';
            document.querySelector('#partnerModal .upload-preview').style.display = 'none';
            Modal.open('partnerModal');
        }

        function openCertificateModal() {
            document.getElementById('certificateModalTitle').textContent = '新增证书';
            document.getElementById('certificateForm').reset();
            document.getElementById('certificateId').value = '';
            document.querySelector('#certificateModal .upload-preview').style.display = 'none';
            Modal.open('certificateModal');
        }

        function openLinkModal() {
            document.getElementById('linkModalTitle').textContent = '新增链接';
            document.getElementById('linkForm').reset();
            document.getElementById('linkId').value = '';
            Modal.open('linkModal');
        }

        // 编辑填充
        function editBanner(data) {
            document.getElementById('bannerModalTitle').textContent = '编辑轮播图';
            fillForm('banner', data);
            Modal.open('bannerModal');
        }

        function editPartner(data) {
            document.getElementById('partnerModalTitle').textContent = '编辑合作伙伴';
            fillForm('partner', data);
            Modal.open('partnerModal');
        }

        function editCertificate(data) {
            document.getElementById('certificateModalTitle').textContent = '编辑证书';
            fillForm('certificate', data);
            Modal.open('certificateModal');
        }

        function editLink(data) {
            document.getElementById('linkModalTitle').textContent = '编辑链接';
            fillForm('link', data);
            Modal.open('linkModal');
        }

        function fillForm(prefix, data) {
            const form = document.getElementById(prefix + 'Form');
            Object.keys(data).forEach(key => {
                const field = form.querySelector(`[name="${key}"]`);
                if (field && field.type !== 'file') field.value = data[key] || '';
            });
            if (data.image || data.logo) {
                const preview = form.querySelector('.upload-preview');
                const pathInput = form.querySelector(`[name="${data.logo ? 'logo' : 'image'}"][type="hidden"]`) ||
                                 form.querySelector(`#${prefix}${data.logo ? 'Logo' : ''}ImagePath`);
                if (preview) {
                    preview.src = data.image || data.logo;
                    preview.style.display = '';
                }
                if (pathInput) pathInput.value = data.image || data.logo;
            }
        }

        function closeModal(id) {
            document.getElementById(id).closest('.modal-overlay').classList.remove('show');
        }

        // 通用保存函数
        async function saveContent(e, type) {
            e.preventDefault();
            const form = e.target;
            const formData = new FormData(form);

            // 收集表单数据为JSON
            const data = {};
            ['title','subtitle','image','link','sort_order','name','logo','url','description'].forEach(k => {
                if (formData.get(k)) data[k] = formData.get(k);
            });
            const id = formData.get('id');

            // 先处理文件上传
            const fileInput = form.querySelector('input[type="file"]');
            let imagePath = formData.get(imagePathField(type)) || data.image || data.logo || '';

            if (fileInput && fileInput.files.length > 0) {
                try {
                    Toast.info('正在上传图片...');
                    const uploadFormData = new FormData();
                    uploadFormData.append('file', fileInput.files[0]);
                    const resp = await fetch('../api/upload.php', { method: 'POST', body: uploadFormData }).then(r => r.json());
                    if (resp.code === 200) {
                        imagePath = resp.data.path;
                        if (type === 'partners') data.logo = imagePath;
                        else data.image = imagePath;
                        Toast.success('图片上传成功');
                    } else throw new Error(resp.message);
                } catch(err) { Toast.error(err.message); return false; }
            }

            const action = id ? 'update' : 'create';
            const postData = new FormData();
            postData.append('action', action);
            postData.append('type', type);
            if (id) postData.append('id', id);
            postData.append('data', JSON.stringify(data));

            try {
                const resp = await fetch('?ajax=1', { method: 'POST', body: postData }).then(r => r.json());
                if (resp.code === 200) {
                    Toast.success(resp.message);
                    setTimeout(() => location.reload(), 800);
                } else {
                    Toast.error(resp.message);
                }
            } catch(err) {
                Toast.error('网络错误');
            }
            return false;
        }

        function imagePathField(type) {
            return { banners:'bannerImagePath', partners:'partnerLogoPath', certificates:'certImagePath' }[type] || '';
        }

        // 删除操作绑定
        document.querySelectorAll('[data-delete]').forEach(btn => {
            btn.addEventListener('click', () => {
                Modal.confirm('确认删除', `确定要删除${btn.dataset.name}吗？`, async () => {
                    const fd = new FormData();
                    fd.append('action', 'delete');
                    fd.append('type', btn.dataset.type);
                    fd.append('id', btn.dataset.id);
                    const resp = await fetch('?ajax=1', { method: 'POST', body: fd }).then(r => r.json());
                    Toast.success(resp.message || '删除成功');
                    setTimeout(() => location.reload(), 500);
                });
            });
        });
    </script>
</body>
</html>
