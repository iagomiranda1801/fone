import * as XLSX from 'xlsx'
import jsPDF from 'jspdf'
import autoTable from 'jspdf-autotable'

// ─── helpers ──────────────────────────────────────────────────────────────────
function money(val) {
  return Number(val).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' })
}

function pct(val) {
  return `${val}%`
}

// ─── Excel ────────────────────────────────────────────────────────────────────
export function exportExcel(filename, sheets) {
  // sheets: [{ name, headers, rows }]
  const wb = XLSX.utils.book_new()

  sheets.forEach(({ name, headers, rows }) => {
    const ws = XLSX.utils.aoa_to_sheet([headers, ...rows])

    // Column widths based on header length
    ws['!cols'] = headers.map((h) => ({ wch: Math.max(String(h).length + 4, 14) }))

    XLSX.utils.book_append_sheet(wb, ws, name)
  })

  XLSX.writeFile(wb, `${filename}.xlsx`)
}

// ─── PDF ──────────────────────────────────────────────────────────────────────
function createPDF(title, subtitle = '') {
  const doc = new jsPDF({ orientation: 'landscape', unit: 'mm', format: 'a4' })

  // Header bar
  doc.setFillColor(79, 70, 229) // indigo-600
  doc.rect(0, 0, 297, 18, 'F')

  doc.setTextColor(255, 255, 255)
  doc.setFontSize(13)
  doc.setFont('helvetica', 'bold')
  doc.text(title, 10, 12)

  if (subtitle) {
    doc.setFontSize(8)
    doc.setFont('helvetica', 'normal')
    doc.text(subtitle, 10, 17.5)
  }

  // Date on right
  doc.setFontSize(8)
  doc.text(`Gerado em: ${new Date().toLocaleString('pt-BR')}`, 287, 12, { align: 'right' })

  doc.setTextColor(0, 0, 0)
  return doc
}

function savePDF(doc, filename) {
  doc.save(`${filename}.pdf`)
}

// ─── Vendas ───────────────────────────────────────────────────────────────────
export function exportVendasExcel(data, totalizadores) {
  const mainRows = data.map((v) => [
    v.id,
    v.cliente,
    v.itens.length,
    money(v.total),
    money(v.lucro),
    v.cancelada ? 'Cancelada' : 'Ativa',
    v.created_at,
  ])

  const sumRow = [
    '', 'TOTAIS',
    '',
    money(totalizadores.faturamento),
    money(totalizadores.lucro_total),
    `${totalizadores.vendas_ativas} ativas / ${totalizadores.vendas_canceladas} canceladas`,
    '',
  ]

  exportExcel(`relatorio_vendas_${today()}`, [
    {
      name: 'Vendas',
      headers: ['ID', 'Cliente', 'Nº Itens', 'Total', 'Lucro', 'Status', 'Data/Hora'],
      rows: [...mainRows, [], sumRow],
    },
  ])
}

export function exportVendasPDF(data, totalizadores, filtros = '') {
  const doc = createPDF('Relatório de Vendas', filtros)

  autoTable(doc, {
    startY: 22,
    head: [['ID', 'Cliente', 'Itens', 'Total', 'Lucro', 'Status', 'Data']],
    body: [
      ...data.map((v) => [
        `#${v.id}`,
        v.cliente,
        v.itens.length,
        money(v.total),
        money(v.lucro),
        v.cancelada ? 'Cancelada' : 'Ativa',
        v.created_at,
      ]),
      ['', 'TOTAIS', '', money(totalizadores.faturamento), money(totalizadores.lucro_total),
        `${totalizadores.vendas_ativas} ativas`, ''],
    ],
    styles: { fontSize: 8, cellPadding: 2 },
    headStyles: { fillColor: [79, 70, 229], textColor: 255, fontStyle: 'bold' },
    footStyles: { fillColor: [240, 240, 255], fontStyle: 'bold' },
    alternateRowStyles: { fillColor: [248, 247, 255] },
    columnStyles: { 3: { halign: 'right' }, 4: { halign: 'right' } },
    didParseCell(data) {
      if (data.section === 'body' && data.row.index === data.table.body.length - 1) {
        data.cell.styles.fillColor = [224, 231, 255]
        data.cell.styles.fontStyle = 'bold'
      }
      if (data.section === 'body' && data.column.index === 5) {
        const val = data.cell.raw
        if (val === 'Cancelada') data.cell.styles.textColor = [220, 38, 38]
        else if (val === 'Ativa') data.cell.styles.textColor = [5, 150, 105]
      }
    },
  })

  savePDF(doc, `relatorio_vendas_${today()}`)
}

// ─── Compras ──────────────────────────────────────────────────────────────────
export function exportComprasExcel(data, totalizadores) {
  const mainRows = data.map((c) => [
    c.id,
    c.fornecedor,
    c.itens.length,
    money(c.total),
    c.created_at,
  ])

  exportExcel(`relatorio_compras_${today()}`, [
    {
      name: 'Compras',
      headers: ['ID', 'Fornecedor', 'Nº Itens', 'Total', 'Data/Hora'],
      rows: [
        ...mainRows,
        [],
        ['', 'TOTAL GERAL', '', money(totalizadores.valor_total), ''],
      ],
    },
  ])
}

export function exportComprasPDF(data, totalizadores, filtros = '') {
  const doc = createPDF('Relatório de Compras', filtros)

  autoTable(doc, {
    startY: 22,
    head: [['ID', 'Fornecedor', 'Itens', 'Total', 'Data']],
    body: [
      ...data.map((c) => [`#${c.id}`, c.fornecedor, c.itens.length, money(c.total), c.created_at]),
      ['', 'TOTAL GERAL', '', money(totalizadores.valor_total), ''],
    ],
    styles: { fontSize: 8, cellPadding: 2 },
    headStyles: { fillColor: [5, 150, 105], textColor: 255, fontStyle: 'bold' },
    alternateRowStyles: { fillColor: [240, 253, 244] },
    columnStyles: { 3: { halign: 'right' } },
    didParseCell(data) {
      if (data.section === 'body' && data.row.index === data.table.body.length - 1) {
        data.cell.styles.fillColor = [209, 250, 229]
        data.cell.styles.fontStyle = 'bold'
      }
    },
  })

  savePDF(doc, `relatorio_compras_${today()}`)
}

// ─── Estoque ──────────────────────────────────────────────────────────────────
export function exportEstoqueExcel(data, totalizadores) {
  exportExcel(`relatorio_estoque_${today()}`, [
    {
      name: 'Estoque',
      headers: ['ID', 'Produto', 'Estoque', 'Custo Médio', 'Preço Venda', 'Margem %', 'Valor em Estoque', 'Status', 'Nível'],
      rows: [
        ...data.map((p) => [
          p.id, p.nome, p.estoque,
          money(p.custo_medio), money(p.preco_venda),
          pct(p.margem), money(p.valor_estoque),
          p.ativo ? 'Ativo' : 'Inativo', p.nivel,
        ]),
        [],
        ['', 'TOTAIS', '', '', '', '', money(totalizadores.valor_total_estoque), `${totalizadores.produtos_ativos} ativos`, ''],
      ],
    },
  ])
}

export function exportEstoquePDF(data, totalizadores, filtros = '') {
  const doc = createPDF('Relatório de Estoque', filtros)

  autoTable(doc, {
    startY: 22,
    head: [['ID', 'Produto', 'Qtd', 'Custo Médio', 'Pr. Venda', 'Margem', 'Valor Estoque', 'Status', 'Nível']],
    body: [
      ...data.map((p) => [
        p.id, p.nome, p.estoque,
        money(p.custo_medio), money(p.preco_venda),
        pct(p.margem), money(p.valor_estoque),
        p.ativo ? 'Ativo' : 'Inativo', p.nivel,
      ]),
      ['', 'TOTAIS', '', '', '', '', money(totalizadores.valor_total_estoque), `${totalizadores.produtos_ativos} ativos`, ''],
    ],
    styles: { fontSize: 7.5, cellPadding: 2 },
    headStyles: { fillColor: [245, 158, 11], textColor: 255, fontStyle: 'bold' },
    alternateRowStyles: { fillColor: [255, 251, 235] },
    columnStyles: { 3: { halign: 'right' }, 4: { halign: 'right' }, 6: { halign: 'right' } },
    didParseCell(data) {
      if (data.section === 'body' && data.row.index === data.table.body.length - 1) {
        data.cell.styles.fillColor = [254, 243, 199]
        data.cell.styles.fontStyle = 'bold'
      }
      if (data.section === 'body' && data.column.index === 8) {
        const val = data.cell.raw
        if (val === 'zerado') data.cell.styles.textColor = [220, 38, 38]
        else if (val === 'baixo') data.cell.styles.textColor = [234, 88, 12]
        else data.cell.styles.textColor = [5, 150, 105]
      }
    },
  })

  savePDF(doc, `relatorio_estoque_${today()}`)
}

// ─── Lucratividade ────────────────────────────────────────────────────────────
export function exportLucratividadeExcel(data, totalizadores) {
  exportExcel(`relatorio_lucratividade_${today()}`, [
    {
      name: 'Lucratividade',
      headers: ['ID', 'Produto', 'Qtd Vendida', 'Receita', 'Custo', 'Lucro', 'Margem %'],
      rows: [
        ...data.map((r) => [
          r.id, r.nome, r.total_vendido,
          money(r.receita), money(r.custo), money(r.lucro), pct(r.margem),
        ]),
        [],
        ['', 'TOTAIS', '', money(totalizadores.receita_total), money(totalizadores.custo_total), money(totalizadores.lucro_total), pct(totalizadores.margem_media)],
      ],
    },
  ])
}

export function exportLucratividadePDF(data, totalizadores, filtros = '') {
  const doc = createPDF('Relatório de Lucratividade', filtros)

  autoTable(doc, {
    startY: 22,
    head: [['#', 'Produto', 'Qtd Vendida', 'Receita', 'Custo', 'Lucro', 'Margem']],
    body: [
      ...data.map((r, i) => [
        i + 1, r.nome, r.total_vendido,
        money(r.receita), money(r.custo), money(r.lucro), pct(r.margem),
      ]),
      ['', 'TOTAIS', '', money(totalizadores.receita_total), money(totalizadores.custo_total), money(totalizadores.lucro_total), pct(totalizadores.margem_media)],
    ],
    styles: { fontSize: 8, cellPadding: 2 },
    headStyles: { fillColor: [139, 92, 246], textColor: 255, fontStyle: 'bold' },
    alternateRowStyles: { fillColor: [250, 245, 255] },
    columnStyles: { 3: { halign: 'right' }, 4: { halign: 'right' }, 5: { halign: 'right' }, 6: { halign: 'right' } },
    didParseCell(data) {
      if (data.section === 'body' && data.row.index === data.table.body.length - 1) {
        data.cell.styles.fillColor = [237, 233, 254]
        data.cell.styles.fontStyle = 'bold'
      }
    },
  })

  savePDF(doc, `relatorio_lucratividade_${today()}`)
}

function today() {
  return new Date().toISOString().slice(0, 10)
}
