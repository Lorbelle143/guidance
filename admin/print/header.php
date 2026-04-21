<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title><?php echo $printTitle ?? 'NBSC GCO Form'; ?></title>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Times New Roman',Times,serif;font-size:10pt;color:#000;background:#fff}
@page{size:A4;margin:12mm 14mm}
@media print{
  .no-print{display:none!important}
  body{margin:0}
}
.print-btn{position:fixed;top:12px;right:12px;z-index:999;display:flex;gap:8px}
.print-btn button{padding:8px 18px;border:none;border-radius:6px;font-size:13px;font-weight:600;cursor:pointer}
.btn-p{background:#2563eb;color:#fff}
.btn-c{background:#6b7280;color:#fff}

/* ── NBSC Header ── */
.nbsc-header{text-align:center;border-bottom:2px solid #000;padding-bottom:6px;margin-bottom:8px}
.nbsc-header .republic{font-size:8pt;margin-bottom:1px}
.nbsc-header .school-name{font-size:15pt;font-weight:700;letter-spacing:.5px;line-height:1.1}
.nbsc-header .address{font-size:8.5pt;margin:1px 0}
.nbsc-header .tagline{font-size:7.5pt;font-style:italic;color:#b8860b;margin-top:1px}
.nbsc-logo{width:60px;height:60px;object-fit:contain}
.header-row{display:flex;align-items:center;justify-content:center;gap:14px}
.header-text{text-align:center}

/* ── Doc code box ── */
.doc-code-box{border:1px solid #000;font-size:7.5pt;text-align:center;min-width:130px}
.doc-code-box .title{background:#000;color:#fff;padding:1px 4px;font-weight:700;font-size:7pt}
.doc-code-box .code{font-weight:700;font-size:9pt;padding:2px}
.doc-code-table{width:100%;border-collapse:collapse;font-size:7pt}
.doc-code-table td{border:1px solid #000;padding:1px 3px;text-align:center}

/* ── Form title ── */
.form-title{text-align:center;font-size:12pt;font-weight:700;text-decoration:underline;margin:8px 0 6px;text-transform:uppercase;letter-spacing:.5px}

/* ── Field rows ── */
table.form-tbl{width:100%;border-collapse:collapse;font-size:9pt}
table.form-tbl td,table.form-tbl th{border:1px solid #000;padding:2px 4px;vertical-align:top}
table.form-tbl th{background:#000;color:#fff;font-size:8pt;font-weight:700;text-align:left}
.field-label{font-size:7.5pt;font-weight:700;color:#333;display:block;margin-bottom:1px}
.field-val{font-size:9.5pt;border-bottom:1px solid #555;min-height:14px;padding-bottom:1px}
.field-val.bold{font-weight:700}
.section-hd{background:#000;color:#fff;font-weight:700;font-size:9pt;padding:3px 6px;margin:6px 0 0}
.check-row{display:flex;align-items:center;gap:4px;font-size:9pt}
.chk{width:11px;height:11px;border:1px solid #000;display:inline-flex;align-items:center;justify-content:center;font-size:8pt;flex-shrink:0}
.chk.checked::after{content:'✓';font-weight:700}
.sig-line{border-top:1px solid #000;margin-top:28px;padding-top:2px;text-align:center;font-size:8.5pt}
.sig-name{font-weight:700;font-size:9.5pt}

/* ── Footer ── */
.page-footer{margin-top:10px;border-top:1px solid #ccc;padding-top:4px;display:flex;align-items:center;justify-content:space-between}
.page-footer img{height:28px;object-fit:contain}
.page-footer .social{font-size:7.5pt;color:#333;display:flex;align-items:center;gap:4px}
</style>
</head>
<body>
<div class="print-btn no-print">
  <button class="btn-p" onclick="window.print()">🖨 Print</button>
  <button class="btn-c" onclick="window.close()">✕ Close</button>
</div>
