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

        .page {
            width: 100%;
            box-sizing: border-box;
        }

        .label-card {
            width: 50mm;
            height: 38mm;
            border: 1.2px solid #000;
            border-radius: 4mm;
            background: #fff;
            overflow: hidden;
            box-sizing: border-box;
            margin: 0 0 6mm 0;
            position: relative;
        }

        .barcode-left {
            position: absolute;
            left: 0;
            top: 0;
            width: 13mm;
            height: 37.6mm;
            border-right: 1px solid #000;
            box-sizing: border-box;
        }

        .barcode-code {
            position: absolute;
            left: -14mm;
            top: 16.3mm;
            width: 33mm;
            height: 5mm;
            text-align: center;
            font-size: 7.5px;
            font-weight: bold;
            letter-spacing: 0.5px;
            color: #000;
            white-space: nowrap;
            transform: rotate(-90deg);
            transform-origin: center center;
        }

        .barcode-img-wrapper {
            position: absolute;
            right: 0.8mm;
            top: 2.3mm;
            width: 7mm;
            height: 33mm;
            overflow: hidden;
        }

        .barcode-img-wrapper img {
            position: absolute;
            width: 33mm;
            height: 6.5mm;
            left: -13mm;
            top: 13.25mm;
            transform: rotate(-90deg);
            transform-origin: center center;
        }

        /* QR Code specific styles */
        .barcode-img-wrapper.qrcode {
            width: 100%;
            height: 100%;
            position: relative;
            right: 0;
            top: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1mm;
        }

        .barcode-img-wrapper.qrcode img {
            position: relative;
            width: 10mm !important;
            height: 10mm !important;
            left: 0 !important;
            top: 0 !important;
            transform: none !important;
            max-width: 100%;
            max-height: 100%;
        }

        .label-right {
            position: absolute;
            left: 13mm;
            top: 0;
            right: 0;
            height: 37.6mm;
            box-sizing: border-box;
            padding: 1mm 2mm 0.5mm 2mm;
        }

        .company-name {
            text-align: center;
            text-transform: uppercase;
            font-size: 9.5px;
            font-weight: bold;
            letter-spacing: 0.3px;
            color: #000;
            margin-top: 0.8mm;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .company-divider {
            border: 0;
            border-top: 0.8px solid #000;
            margin: 1mm 0 1.2mm 0;
            padding: 0;
            height: 0;
        }

        .details-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 7.2px;
            font-weight: bold;
            line-height: 1.2;
            color: #000;
        }

        .details-table td {
            padding: 0.25mm 0;
            vertical-align: middle;
        }

        .detail-key {
            width: 13mm;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .detail-separator {
            width: 2mm;
            text-align: center;
        }

        .detail-value {
            text-transform: uppercase;
            word-break: break-word;
        }

        .price-row td {
            vertical-align: top;
            padding-top: 0.4mm;
        }

        .price-value-container {
            line-height: 1;
            white-space: nowrap;
        }

        .price-text {
            font-size: 12px;
            font-weight: bold;
            display: inline-block;
            white-space: nowrap;
        }

        .tax-note {
            font-size: 5px;
            font-weight: bold;
            margin-top: 0.3mm;
            letter-spacing: 0.1px;
        }

        .no-barcode {
            font-size: 8px;
            color: #888;
            font-style: italic;
            text-align: center;
            padding: 10px 0;
        }
    </style>
</head>
<body>
    <div class="page">
        @if(count($barcodes) > 0)
            @foreach($barcodes as $bc)
                <div class="label-card">
                    <div class="barcode-left">
                        @if(!empty($bc['barcode']) && !empty($bc['barcode_image']))
                            @php
                                $isQrCode = isset($barcodeType) && $barcodeType === 'qrcode';
                            @endphp
                            @if(!$isQrCode)
                                <div class="barcode-code">{{ $bc['barcode'] }}</div>
                            @endif
                            <div class="barcode-img-wrapper {{ $isQrCode ? 'qrcode' : '' }}">
                                <img src="{{ $bc['barcode_image'] }}" alt="barcode">
                            </div>
                        @else
                            <div class="no-barcode">No barcode</div>
                        @endif
                    </div>

                    <div class="label-right">
                        <div class="company-name">
                            {{ strtoupper($settings->name ?? 'Company name') }}
                        </div>

                        <hr class="company-divider">

                        <div class="details">
                            <table class="details-table">
                                <tr>
                                    <td class="detail-key">ITEM</td>
                                    <td class="detail-separator">:</td>
                                    <td class="detail-value">{{ strtoupper($product->name ?? '-') }}</td>
                                </tr>
                                <tr>
                                    <td class="detail-key">CATAGORY</td>
                                    <td class="detail-separator">:</td>
                                    <td class="detail-value">{{ strtoupper($bc['category'] ?? '-') }}</td>
                                </tr>
                                @if(!empty($bc['color']) && $bc['color'] !== '-')
                                    <tr>
                                        <td class="detail-key">COLOR</td>
                                        <td class="detail-separator">:</td>
                                        <td class="detail-value">{{ strtoupper($bc['color']) }}</td>
                                    </tr>
                                @endif
                                @if(!empty($bc['size']) && $bc['size'] !== '-')
                                    <tr>
                                        <td class="detail-key">SIZE</td>
                                        <td class="detail-separator">:</td>
                                        <td class="detail-value">{{ strtoupper($bc['size']) }}</td>
                                    </tr>
                                @endif
                                <tr class="price-row">
                                    <td class="detail-key">MRP</td>
                                    <td class="detail-separator">:</td>
                                    <td class="detail-value">
                                        <div class="price-value-container">
                                            <div class="price-text">{{ $bc['price_text'] ?? '-' }}</div>
                                            <div class="tax-note">(INCL. OF ALL TAXES)</div>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="no-barcode">No barcode assigned to this product.</div>
        @endif
    </div>
</body>
</html>
