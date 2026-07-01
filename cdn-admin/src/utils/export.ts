function escapeCell(value: unknown): string {
  const str = String(value ?? '');
  if (/[",\n\r]/.test(str)) {
    return `"${str.replace(/"/g, '""')}"`;
  }
  return str;
}

export function exportToCsv<T extends object>(
  filename: string,
  rows: T[],
  columns: { key: keyof T & string; label: string }[]
) {
  if (rows.length === 0) {
    alert('没有可导出的数据');
    return;
  }

  const header = columns.map((c) => escapeCell(c.label)).join(',');
  const body = rows
    .map((row) => columns.map((c) => escapeCell(row[c.key])).join(','))
    .join('\n');
  const csv = `${header}\n${body}`;

  const blob = new Blob(['\uFEFF' + csv], { type: 'text/csv;charset=utf-8;' });
  const url = URL.createObjectURL(blob);
  const link = document.createElement('a');
  link.href = url;
  link.download = `${filename}_${new Date().toISOString().slice(0, 10)}.csv`;
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
  URL.revokeObjectURL(url);
}
