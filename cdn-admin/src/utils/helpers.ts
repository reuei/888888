export function formatMoney(n: number) {
  return n.toLocaleString('zh-CN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

export function statusBadge(status: string) {
  switch (status) {
    case 'running':
    case 'normal':
    case 'on':
    case 'paid':
    case 'approved':
    case 'issued':
      return 'badge-success';
    case 'pending':
      return 'badge-warning';
    case 'stopped':
    case 'banned':
    case 'off':
    case 'refunded':
    case 'rejected':
    case 'closed':
    case 'cancelled':
      return 'badge-danger';
    default:
      return 'badge-default';
  }
}

export function statusText(status: string) {
  const map: Record<string, string> = {
    running: '运行中',
    stopped: '已停用',
    pending: '审核中',
    normal: '正常',
    banned: '已封禁',
    on: '上架',
    off: '下架',
    paid: '已支付',
    refunded: '已退款',
    closed: '已关闭',
    approved: '已通过',
    rejected: '已驳回',
    cancelled: '已取消',
    issued: '已开票',
  };
  return map[status] || status;
}

export function orderStatusText(status: string) {
  const map: Record<string, string> = {
    pending: '待支付',
    paid: '已支付',
    cancelled: '已取消',
    refunded: '已退款',
  };
  return map[status] || status;
}

export function invoiceTypeText(type: string) {
  return type === 'company' ? '企业发票' : '个人发票';
}
