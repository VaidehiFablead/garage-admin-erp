<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>All Product Barcodes</title>
    <style>
        @page {
            size: A4;
            margin: 10mm;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            margin: 0;
            padding: 0;
            background: #fff;
            width: 190mm;
            box-sizing: border-box;
        }

        /* ── Company header section ── */
        .page-header-block {
            width: 100%;
            box-sizing: border-box;
            border: none;
            padding: 4mm;
            margin-bottom: 4mm;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-table td {
            vertical-align: middle;
            padding: 2px 4px;
        }

        hr.divider {
            height: 2px;
            background: #ccc;
            border: none;
            margin: 5px 0 8px 0;
        }

        .section-title {
            text-align: center;
            text-transform: uppercase;
            font-size: 13px;
            font-weight: bold;
            color: #222;
        }

        .barcode-grid {
            width: 100%;
            border-collapse: separate;
            border-spacing: 4mm 4mm;
            table-layout: fixed;
        }

        .barcode-grid td {
            width: 50%;
            vertical-align: top;
        }

        .label-card {
            width: 100%;
            height: 100mm;
            border: 1.5px solid #000;
            border-radius: 6mm;
            background: #fff;
            overflow: hidden;
            box-sizing: border-box;
        }

        .label-header {
            background: #000;
            color: #fff;
            text-align: center;
            padding: 2.2mm 2mm;
            border-bottom: 1px solid #000;
        }

        .company-title {
            font-size: 15px;
            font-weight: bold;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .label-address {
            text-align: center;
            font-size: 9.5px;
            font-weight: bold;
            color: #000;
            padding: 1.5mm 2mm;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .info-container {
            padding: 0 10mm;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10.5px;
            font-weight: bold;
            line-height: 1.35;
            color: #000;
        }

        .info-table td {
            padding: 0.8mm 0;
            vertical-align: middle;
        }

        .info-table .key {
            width: 18mm;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .info-table .separator {
            width: 4mm;
            text-align: center;
        }

        .info-table .value {
            text-transform: uppercase;
            word-break: break-word;
        }

        .section-divider {
            border: 0;
            border-top: 1px solid #000;
            margin: 2mm 6mm;
            padding: 0;
            height: 0;
        }

        .mrp-box {
            width: 44mm;
            height: 17mm;
            background: #000;
            color: #fff;
            border-radius: 3mm;
            padding: 1.5mm 3mm;
            box-sizing: border-box;
            text-align: center;
        }

        .mrp-title {
            font-size: 8px;
            font-weight: bold;
            text-align: left;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .mrp-amount {
            font-size: 19px;
            font-weight: bold;
            line-height: 1.1;
            margin-top: -0.5mm;
            white-space: nowrap;
        }

        .mrp-taxes {
            font-size: 5.5px;
            font-weight: bold;
            letter-spacing: 0.2px;
            margin-top: 0.2mm;
        }

        /* PREMIUM QUALITY BADGE (PURE CSS) */
        .seal-badge {
            width: 22mm;
            height: 22mm;
            margin: 0 auto;
            position: relative;
        }

        .seal-outer {
            width: 20mm;
            height: 20mm;
            border: 1.5px dashed #000;
            border-radius: 50%;
            position: absolute;
            left: 1mm;
            top: 1mm;
            box-sizing: border-box;
        }

        .seal-inner {
            width: 17.5mm;
            height: 17.5mm;
            border: 1px solid #000;
            border-radius: 50%;
            position: absolute;
            left: 1.1mm;
            top: 1.1mm;
            box-sizing: border-box;
        }

        .seal-top {
            position: absolute;
            top: 2.2mm;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 6px;
            font-weight: bold;
            letter-spacing: 0.3px;
        }

        .seal-ribbon {
            position: absolute;
            top: 6.8mm;
            left: -2mm;
            right: -2mm;
            background: #000;
            color: #fff;
            font-size: 6.5px;
            font-weight: bold;
            text-align: center;
            padding: 0.5mm 0;
            z-index: 10;
        }

        .seal-bottom {
            position: absolute;
            bottom: 2.2mm;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 6px;
            font-weight: bold;
            letter-spacing: 0.3px;
        }

        .barcode-vertical-text {
            position: absolute;
            left: -6mm;
            top: 7mm;
            width: 20mm;
            height: 5mm;
            text-align: center;
            font-size: 7.5px;
            font-weight: bold;
            color: #000;
            white-space: nowrap;
            transform: rotate(-90deg);
            transform-origin: center center;
        }

        .barcode-bottom-val {
            text-align: center;
            font-size: 9px;
            font-weight: bold;
            letter-spacing: 0.5px;
            color: #000;
            margin-top: 1mm;
        }

        .no-barcode {
            text-align: center;
            color: #888;
            font-style: italic;
            padding: 14px 0;
        }
    </style>
</head>
<body>
    <!-- ── Company Header ── -->
    <div class="page-header-block">
        <table class="header-table">
            <tr>
                <td style="width: 120px;">
                    @if(!empty($settings->logo) && file_exists(storage_path('app/public/' . $settings->logo)))
                        @php
                            $logoPath = storage_path('app/public/' . $settings->logo);
                            $logoData = base64_encode(file_get_contents($logoPath));
                            $logoMime = mime_content_type($logoPath);
                        @endphp
                        <img src="data:{{ $logoMime }};base64,{{ $logoData }}" alt="Logo"
                             style="height:48px; width:auto;">
                    @endif
                </td>
                <td style="text-align:right; padding-left:10px;">
                    <div style="font-size:15px; font-weight:bold; text-transform:uppercase; color:#000;">
                        {{ $settings->name ?? '' }}
                    </div>
                    <div style="font-size:10px; color:#555; margin-top:3px; text-transform:uppercase;">
                        {{ $settings->address ?? '' }}<br>
                        @if(!empty($settings->phone))PHONE: {{ $settings->phone }}@endif
                        @if(!empty($settings->email)) | EMAIL: {{ $settings->email }}@endif
                    </div>
                </td>
            </tr>
        </table>

        <hr class="divider">
        <div class="section-title">All Product Barcodes</div>
    </div>

    <table class="barcode-grid">
        @foreach($products->chunk(2) as $row)
            <tr>
                @foreach($row as $product)
                    <td>
                        <div class="label-card">
                            <div class="label-header">
                                <div class="company-title">{{ strtoupper($settings->name ?? 'Company name') }}</div>
                            </div>
                            <div class="label-address">{{ strtoupper($settings->address ?? 'LOHARDAGA, JHARKHAND') }}</div>

                            <hr class="section-divider">

                            <div class="info-container">
                                <table class="info-table">
                                    <tr>
                                        <td class="key">ITEM</td>
                                        <td class="separator">:</td>
                                        <td class="value">{{ strtoupper(preg_replace('/\s*\(.*?\)$/', '', $product->name ?? '-')) }}</td>
                                    </tr>
                                    @if(!empty($product->color) && $product->color !== '-')
                                        <tr>
                                            <td class="key">COLOR</td>
                                            <td class="separator">:</td>
                                            <td class="value">{{ strtoupper($product->color) }}</td>
                                        </tr>
                                    @endif
                                    @if(!empty($product->size) && $product->size !== '-')
                                        <tr>
                                            <td class="key">SIZE</td>
                                            <td class="separator">:</td>
                                            <td class="value">{{ strtoupper($product->size) }}</td>
                                        </tr>
                                    @endif
                                </table>
                            </div>

                            <hr class="section-divider">

                            <table style="width: 100%; border-collapse: collapse; margin: 1mm 0;">
                                <tr>
                                    <td style="width: 58%; padding-left: 6mm; vertical-align: middle;">
                                        <div class="mrp-box">
                                            <div class="mrp-title">MRP</div>
                                            <div class="mrp-amount">{{ $product->price_text ?? '-' }}</div>
                                            <div class="mrp-taxes">(INCL. OF ALL TAXES)</div>
                                        </div>
                                    </td>
                                    <td style="width: 42%; padding-right: 6mm; text-align: center; vertical-align: middle;">
                                        <div class="seal-badge">
                                            <div class="seal-outer">
                                                <div class="seal-inner">
                                                    <span class="seal-top">PREMIUM</span>
                                                    <div class="seal-ribbon">BEST CHOICE</div>
                                                    <span class="seal-bottom">QUALITY</span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </table>

                            <hr class="section-divider">

                            @if(!empty($product->barcode) && isset($barcodeImages[$product->id]))
                                @php
                                    $isQrCode = isset($barcodeType) && $barcodeType === 'qrcode';
                                @endphp
                                <table style="width: 74mm; margin: 0 auto; border-collapse: collapse;">
                                    <tr>
                                        @if(!$isQrCode)
                                            <td style="width: 10mm; vertical-align: middle; position: relative;">
                                                <div class="barcode-vertical-text">{{ $product->barcode }}</div>
                                            </td>
                                        @endif
                                        <td style="vertical-align: {{ $isQrCode ? 'center' : 'top' }};">
                                            @if($isQrCode)
                                                <div style="height: 15mm; width: 15mm; margin: 0 auto; display: flex; align-items: center; justify-content: center;">
                                                    <img src="{{ $barcodeImages[$product->id] }}" alt="qrcode" style="width: 100%; height: 100%;">
                                                </div>
                                            @else
                                                <div style="height: 13.5mm; overflow: hidden; width: 64mm;">
                                                    <img src="{{ $barcodeImages[$product->id] }}" alt="barcode" style="width: 100%; height: 100%;">
                                                </div>
                                                <div class="barcode-bottom-val">{{ $product->sku ?? $product->id ?? '' }}</div>
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            @else
                                <div class="no-barcode">No barcode</div>
                            @endif
                        </div>
                    </td>
                @endforeach
                @for($i = $row->count(); $i < 2; $i++)
                    <td></td>
                @endfor
            </tr>
        @endforeach
    </table>
</body>
</html>
