<style>
    body {
        font-family: DejaVu Sans, sans-serif;
        font-size: 12px;
        color: #333;
        margin: 0;
        padding: 0;
    }

    /* HEADER */
    .header {
        display: flex;
        justify-content: center;
        gap: 15px;
        align-items: center;
        margin-bottom: 5px;
        /* margin-top: -40px !important; */
        /* No extra gap on top */
        padding-top: 0;
        /* Remove any internal padding */
    }

    .logo img {
        height: 50px;
        max-width: 100px;
        object-fit: contain;
    }

    .company-info-header {
        text-align: center;
        line-height: 1.4;
        font-size: 10px !important;
    }

    hr {
        border: 1px solid #ccc;
        margin: 8px 0 15px;
    }

    .title {
        text-align: center;
        font-size: 22px;
        font-weight: bold;
        margin: 10px 0;
        color: #555;
    }

    .details-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
    }

    .details-table td {
        vertical-align: top;
        padding: 5px;
    }

    .inner-table {
        width: 100%;
        border-collapse: collapse;
    }

    .inner-table td {
        padding: 3px 5px;
        font-size: 13px;
    }

    .label {
        font-weight: bold;
        width: 40%;
        white-space: nowrap;
    }

    .value {
        width: 60%;
    }

    .details-table strong {
        color: #222;
    }

    .product-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
        font-size: 12px;
    }

    .product-table th {
        background: #f3f2f7;
        padding: 8px;
        text-align: center;
        border: 1px solid #ddd;
    }

    .product-table td {
        border: 1px solid #eee;
        padding: 8px;
        vertical-align: middle;
    }

    .summary-table {
        width: 300px;
        margin-left: auto;
        border-collapse: collapse;
        margin-top: 20px;
    }

    .summary-table td {
        padding: 6px 12px;
        border: 1px solid #e0e0e0;
    }

    .summary-table tr td:first-child {
        background-color: #f3f2f7;
        font-weight: bold;
    }

    .footer {
        text-align: center;
        margin-top: 60px;
    }

    .footer h2 {
        font-size: 18px;
        color: #555;
    }

    .footer p {
        font-size: 14px;
        color: #777;
    }
</style>

<div class="download_pdf">

    <!-- HEADER -->
    <div class="header">
        <div class="logo">
            @if (!empty($setting->logo) && file_exists(public_path('storage/' . $setting->logo)))
                <img src="{{ public_path('storage/' . $setting->logo) }}" alt="Company Logo">
            @endif


        </div>

        <div class="company-info-header text-center">
            <h3 class="mb-0" style="margin-bottom: 3px !important; margin-top: -50px !important; ">
                {{ $setting->name ?? '' }}
            </h3>
            <small>
                {{ $setting->address ?? '' }}
                <br>Customer care no: {{ $setting->phone ?? '' }} Email: {{ $setting->email ?? '' }}
            </small>
        </div>
    </div>
    <hr>
    <!-- TITLE -->
    <div class="title">INVOICE</div>

    <!-- CUSTOMER & ORDER DETAILS -->
    <table class="details-table">
        <tr>
            <!-- Customer Info -->
            <td width="50%">
                <strong>Customer Info</strong>
                <table class="inner-table">
                    <tr>
                        <td class="label">Name:</td>
                        <td class="value">{{ $customer['name'] ?? 'Walk-in Customer' }}</td>
                    </tr>
                    @if (!empty($customer['phone']))
                        <tr>
                            <td class="label">Phone:</td>
                            <td class="value">{{ $customer['phone'] }}</td>
                        </tr>
                    @endif
                    @if (!empty($customer['email']))
                        <tr>
                            <td class="label">Email:</td>
                            <td class="value">{{ $customer['email'] }}</td>
                        </tr>
                    @endif
                    @if (!empty($customer['address']))
                        <tr>
                            <td class="label">Address:</td>
                            <td class="value">{{ $customer['address'] }}</td>
                        </tr>
                    @endif
                </table>
            </td>

            <!-- Order Info -->
            <td width="50%">
                <strong>Order Info</strong>
                <table class="inner-table">
                    <tr>
                        <td class="label">Order No:</td>
                        <td class="value">{{ $sales->order_number ?? '' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Date:</td>
                        <td class="value">{{ date('d M Y, h:i a', strtotime($sales->created_at)) }}</td>
                    </tr>
                    <tr>
                        <td class="label">Payment:</td>
                        <td class="value">{{ ucfirst($sales->payment_status) }}
                            ({{ ucfirst($sales->payment_method) }})</td>
                    </tr>
                    <tr>
                        <td class="label">Warranty:</td>
                        <td class="value">{{ $sales->next_warranty_date ?? '-' }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- PRODUCTS -->
    <table class="product-table">
        <thead>
            <tr>
                <th style="width:50%;">Product / Service</th>
                <th style="width:15%;">Price</th>
                <th style="width:10%;">QTY</th>
                <th style="width:25%;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($orderItems as $item)
                <tr>
                    <td>
                        {{ $item->product->name ?? 'Product' }}
                    </td>
                    <td style="text-align:right;">
                        {{ $setting->currency_symbol }}{{ $item->price }}
                    </td>
                    <td style="text-align:center;">{{ $item->quantity }}</td>
                    <td style="text-align:right;">
                        {{ $setting->currency_symbol }}{{ $item->total_amount }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- SUMMARY -->
    <table class="summary-table">
        <tr>
            <td>Subtotal</td>
            <td style="text-align:right;">{{ $subtotal }}</td>
        </tr>
        <tr>
            <td>Discount</td>
            <td style="text-align:right;">{{ $sales->discount ?? 0 }}% ({{ $discountAmount }})</td>
        </tr>
        <tr>
            <td>After Discount</td>
            <td style="text-align:right;">{{ $afterDiscount }}</td>
        </tr>
        @if ($sales->gst_option == 'with')
            @foreach ($taxDetails as $tax)
                <tr>
                    <td>{{ $tax['name'] }} ({{ $tax['rate'] }}%)</td>
                    <td style="text-align:right;">{{ $tax['formatted_amount'] }}</td>
                </tr>
            @endforeach
        @endif
        <tr style="font-weight:bold;">
            <td>Total</td>
            <td style="text-align:right;">{{ $finalTotal }}</td>
        </tr>
    </table>

    <!-- FOOTER -->
    <div class="footer">
        <h2>Thank You for Your Business!</h2>
        <p>We appreciate your trust in us and look forward to serving you again.</p>
    </div>

</div>
