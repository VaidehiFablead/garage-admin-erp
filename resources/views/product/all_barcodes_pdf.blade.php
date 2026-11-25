<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>All Product Barcodes</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        @page {
            size: A4 portrait;
            margin: 8mm 7mm;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            background: #fff;
            color: #000;
        }

        /* ── Company Header Section ── */
        .page-header-block {
            width: 100%;
            padding: 1mm 1mm 2mm 1mm;
            margin-bottom: 1mm;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-table td {
            vertical-align: middle;
            padding: 0;
        }

        hr.divider {
            height: 1px;
            background: #bbb;
            border: none;
            margin: 4px 0 6px 0;
        }

        .section-title {
            text-align: center;
            text-transform: uppercase;
            font-size: 12px;
            font-weight: bold;
            letter-spacing: 0.5px;
            color: #111;
        }

        /* ── 3-Column Barcode Grid ── */
        .barcode-grid-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .barcode-grid-table td.grid-col {
            width: 33.333%;
            vertical-align: top;
            padding: 1mm 1.25mm;
        }

        .barcode-grid-table tr {
            height: 34mm;
            page-break-inside: avoid;
        }

        .barcode-grid-table tr.page-break-before {
            page-break-before: always;
        }

        /* ── Individual Barcode Card ── */
        .label-card {
            width: 100%;
            height: 32mm;
            border: 1px solid #222;
            border-radius: 8px;
            background: #fff;
            padding: 1.5mm 2mm;
            box-sizing: border-box;
            page-break-inside: avoid;
            overflow: hidden;
        }

        .card-company-header {
            text-align: center;
            font-size: 7.5px;
            font-weight: bold;
            text-transform: uppercase;
            border-bottom: 1px solid #222;
            padding-bottom: 1.5px;
            margin-bottom: 2.5px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            line-height: 1.1;
        }

        .card-body-table {
            width: 100%;
            border-collapse: collapse;
            height: 24mm;
        }

        .card-body-table td {
            padding: 0;
        }

        .card-code-col {
            width: 36%;
            vertical-align: middle;
            text-align: center;
        }

        .card-code-img {
            max-width: 21mm;
            max-height: 22mm;
            display: block;
            margin: 0 auto;
        }

        .card-code-img.barcode-1d {
            transform: rotate(-90deg);
            max-height: 21mm;
            max-width: 19mm;
        }

        .card-info-col {
            width: 64%;
            vertical-align: top;
            padding-left: 3px;
        }

        .details-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 6.8px;
            font-weight: bold;
            line-height: 1.25;
        }

        .details-table td {
            padding: 0.5px 0;
            vertical-align: top;
        }

        .details-table .key {
            width: 40%;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .details-table .val {
            width: 60%;
            text-transform: uppercase;
            word-break: break-word;
        }

        .mrp-val {
            font-size: 9px;
            font-weight: bold;
        }

        .taxes-subtext {
            font-size: 5px;
            font-weight: bold;
            margin-top: 1px;
            color: #222;
        }
    </style>
</head>
<body>

    <!-- ── Company Header ── -->
    <div class="page-header-block">
        <table class="header-table">
            <tr>
                <td style="width: 140px;">
                    @if(!empty($settings->logo) && file_exists(storage_path('app/public/' . $settings->logo)))
                        @php
                            $logoPath = storage_path('app/public/' . $settings->logo);
                            $logoData = base64_encode(file_get_contents($logoPath));
                            $logoMime = mime_content_type($logoPath);
                        @endphp
                        <img src="data:{{ $logoMime }};base64,{{ $logoData }}" alt="Logo"
                             style="max-height: 48px; max-width: 130px; object-fit: contain;">
                    @endif
                </td>
                <td style="text-align: right; padding-left: 10px;">
                    <div style="font-size: 14px; font-weight: bold; text-transform: uppercase; color: #000;">
                        {{ $settings->name ?? 'COMPANY NAME' }}
                    </div>
                    <div style="font-size: 8.5px; color: #444; margin-top: 2px; text-transform: uppercase; line-height: 1.3;">
                        {{ str_replace(["\r\n", "\r", "\n"], ', ', $settings->address ?? '') }}<br>
                        @if(!empty($settings->phone))PHONE: {{ $settings->phone }}@endif
                        @if(!empty($settings->email)) | EMAIL: {{ strtoupper($settings->email) }}@endif
                    </div>
                </td>
            </tr>
        </table>

        <hr class="divider">
        <div class="section-title">All Product Barcodes</div>
    </div>

    <!-- ── Barcode Labels Grid ── -->
    @php
        $currSym = html_entity_decode($settings->currency_symbol ?? '₹', ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $isQr = ($barcodeType ?? '') === 'qrcode' || ($barcodeType ?? '') === 'QRCODE';
    @endphp

    @if($products->count() > 0)
        <table class="barcode-grid-table">
            @foreach($products->chunk(15) as $pageRows)
                @foreach($pageRows->chunk(3) as $rowIndex => $row)
                <tr class="{{ $rowIndex === 0 && !$loop->first ? 'page-break-before' : '' }}">
                    @foreach($row as $bc)
                        <td class="grid-col">
                            <div class="label-card">
                                <div class="card-company-header">
                                    {{ $settings->name ?? 'COMPANY NAME' }}
                                </div>
                                <table class="card-body-table">
                                    <tr>
                                        <td class="card-code-col">
                                            @php
                                                $imgSrc = $barcodeImages[$bc->id] ?? ($bc->barcode_image ?? ($bc->img ?? null));
                                            @endphp
                                            @if(!empty($imgSrc))
                                                <img src="{{ $imgSrc }}" class="card-code-img {{ $isQr ? '' : 'barcode-1d' }}">
                                            @endif
                                        </td>
                                        <td class="card-info-col">
                                            <table class="details-table">
                                                <tr>
                                                    <td class="key">ITEM</td>
                                                    <td class="val">: {{ strtoupper($bc->name ?? $bc->display_name ?? 'N/A') }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="key">CATAGORY</td>
                                                    <td class="val">: {{ strtoupper($bc->category ?? $bc->category_name ?? ($bc->category->name ?? 'N/A')) }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="key" style="padding-top: 2px;">MRP</td>
                                                    <td class="val" style="padding-top: 2px;">
                                                        : <span class="mrp-val">
                                                            @if(isset($bc->mrp) && $bc->mrp !== null)
                                                                {{ $currSym }}{{ number_format((float)$bc->mrp, 2) }}
                                                            @elseif(isset($bc->price) && $bc->price !== null)
                                                                {{ $currSym }}{{ number_format((float)$bc->price, 2) }}
                                                            @else
                                                                {{ $currSym }}0.00
                                                            @endif
                                                        </span>
                                                        <div class="taxes-subtext">(INCL. OF ALL TAXES)</div>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </td>
                    @endforeach
                    @for($i = count($row); $i < 3; $i++)
                        <td class="grid-col" style="border: none;"></td>
                    @endfor
                </tr>
                @endforeach
            @endforeach
        </table>
    @else
        <div style="text-align: center; color: #888; margin-top: 20px;">No products found.</div>
    @endif

</body>
</html>
