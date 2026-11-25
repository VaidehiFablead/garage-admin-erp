<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Barcode - {{ $product->name }}</title>
    <style>
        @page {
            size: A4;
            margin: 10mm;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            margin: 0;
            padding: 0;
            background: #fff;
        }

        .label-card {
            width: 90mm;
            height: 100mm;
            border: 1.5px solid #000;
            border-radius: 6mm;
            background: #fff;
            overflow: hidden;
            box-sizing: border-box;
            margin: 0 auto 8mm;
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
            width: 45mm;
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
    @if(count($barcodes) > 0)
        @foreach($barcodes as $bc)
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
                        @if(!empty($bc['color']) && $bc['color'] !== '-')
                            <tr>
                                <td class="key">COLOR</td>
                                <td class="separator">:</td>
                                <td class="value">{{ strtoupper($bc['color']) }}</td>
                            </tr>
                        @endif
                        @if(!empty($bc['size']) && $bc['size'] !== '-')
                            <tr>
                                <td class="key">SIZE</td>
                                <td class="separator">:</td>
                                <td class="value">{{ strtoupper($bc['size']) }}</td>
                            </tr>
                        @endif
                    </table>
                </div>

                <hr class="section-divider">

                <table style="width: 100%; border-collapse: collapse; margin: 1mm 0;">
                    <tr>
                        <td style="width: 60%; padding-left: 6mm; vertical-align: middle;">
                            <div class="mrp-box">
                                <div class="mrp-title">MRP</div>
                                <div class="mrp-amount">{{ $bc['price_text'] ?? '-' }}</div>
                                <div class="mrp-taxes">(INCL. OF ALL TAXES)</div>
                            </div>
                        </td>
                        <td style="width: 40%; padding-right: 6mm; text-align: center; vertical-align: middle;">
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

                @if(!empty($bc['barcode']) && !empty($bc['barcode_image']))
                    @php
                        $isQrCode = isset($barcodeType) && $barcodeType === 'qrcode';
                    @endphp
                    <table style="width: 78mm; margin: 0 auto; border-collapse: collapse;">
                        <tr>
                            @if(!$isQrCode)
                                <td style="width: 10mm; vertical-align: middle; position: relative;">
                                    <div class="barcode-vertical-text">{{ $bc['barcode'] }}</div>
                                </td>
                            @endif
                            <td style="vertical-align: {{ $isQrCode ? 'center' : 'top' }};">
                                @if($isQrCode)
                                    <div style="height: 15mm; width: 15mm; margin: 0 auto; display: flex; align-items: center; justify-content: center;">
                                        <img src="{{ $bc['barcode_image'] }}" alt="qrcode" style="width: 100%; height: 100%;">
                                    </div>
                                @else
                                    <div style="height: 13.5mm; overflow: hidden; width: 68mm;">
                                        <img src="{{ $bc['barcode_image'] }}" alt="barcode" style="width: 100%; height: 100%;">
                                    </div>
                                    <div class="barcode-bottom-val">{{ $bc['sku'] ?? $product->id ?? '' }}</div>
                                @endif
                            </td>
                        </tr>
                    </table>
                @else
                    <div class="no-barcode">No barcode assigned to this product.</div>
                @endif
            </div>
        @endforeach
    @else
        <div class="no-barcode">No barcode assigned to this product.</div>
    @endif
</body>
</html>
