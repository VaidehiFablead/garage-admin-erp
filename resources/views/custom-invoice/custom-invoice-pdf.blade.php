<style>
    .invoice_pdf tr td {
        vertical-align: middle;
    }

    body {
        font-family: DejaVu Sans, sans-serif;
        font-size: 12px;
    }
</style>
<div class="download_pdf">
    <!-- Left: Logo + Company Name -->
    <table style="width: 100%; border-bottom: 1px solid #eee; margin-bottom: 20px; padding-bottom: 10px;">
        <tr>
            <!-- Left: Logo -->
            <td style="width: 25%; text-align: left; vertical-align: top;">
                <img src="{{ $setting->logo ? storage_path('app/public/' . $setting->logo) : public_path('/admin/assets/img/weblogo.jpeg') }}"
                    style="height: 60px; max-width: 120px; object-fit: contain;">
            </td>

            <!-- Center: Company Info -->
            <td style="width: 50%; text-align: center; vertical-align: middle;">
                <h2 style="margin: 0; font-weight: bold;">{{ $setting->name ?? 'Company Name' }}</h2>
                <p style="margin: 2px 0; font-size: 13px;">
                    {{ $setting->address ?? 'Company Address' }}
                </p>
                <p style="margin: 2px 0; font-size: 13px;">
                    Phone: {{ $setting->phone ?? '1234567890' }} | 
                    Email: {{ $setting->email ?? 'info@example.com' }}
                </p>
            </td>

            <!-- Right: Invoice Info -->
            <td style="width: 25%; text-align: right; vertical-align: middle;">
                <h1 style="color: #7367F0; font-size: 22px; margin: 0; font-weight: bold;">INVOICE</h1>
                <p style="margin: 5px 0; font-size: 13px; font-weight: bold;">#{{ $sales->invoice_number }}</p>
            </td>
        </tr>
    </table>

    <div class="invoice-box table-height"
        style="max-width: 1600px;width:100%;margin:15px auto;padding: 0;font-size: 14px;line-height: 24px;color: #555;">
        <table cellpadding="0" cellspacing="0" style="width: 100%;line-height: inherit;text-align: left;"
            class="invoice_pdf">
            <tbody id="product-details">
                <tr class="top">
                    <td colspan="6" style="padding: 5px;vertical-align: top;">
                        <table style="width: 100%;line-height: inherit;text-align: left;" class="product-list">
                            <tbody>
                                <tr>
                                    <!-- <td style="padding:5px;vertical-align:top;text-align:left;padding-bottom:20px">
                                        <font style="vertical-align: inherit;margin-bottom:25px;">
                                            <font
                                                style="vertical-align: inherit;font-size:14px;color:#7367F0;font-weight:600;line-height: 35px; ">
                                                Customer Info</font>
                                        </font><br>
                                        <font style="vertical-align: inherit;">

                                            <font
                                                style="vertical-align: inherit;font-size: 14px;color:#000;font-weight: 400;"
                                                class="customer-name">
                                                {{ isset($customer['name']) ? $customer['name'] : (isset($vendor['name']) ? $vendor['name'] : '') }}
                                            </font>
                                        </font><br>
                                        <font style="vertical-align: inherit;">
                                            <font
                                                style="vertical-align: inherit;font-size: 14px;color:#000;font-weight: 400;">
                                                {{ isset($customer['email']) ? $customer['email'] : (isset($vendor['email']) ? $vendor['email'] : '') }}
                                            </font>
                                        </font><br> 
                                        <font style="vertical-align: inherit;">
                                            <font
                                                style="vertical-align: inherit;font-size: 14px;color:#000;font-weight: 400;"
                                                class="customer-phone">
                                                {{ isset($customer['phone']) ? $customer['phone'] : (isset($vendor['phone']) ? $vendor['phone'] : '') }}
                                            </font>
                                        </font><br>

                                    </td> -->
                                    <td style="padding:5px; vertical-align:top; text-align:left; padding-bottom:20px">
                                        <font style="vertical-align: inherit; margin-bottom:25px;">
                                            <font style="vertical-align: inherit; font-size:14px; color:#7367F0; font-weight:600; line-height:35px;">
                                                {{ $vendor ? 'Vendor Info' : ($customer ? 'Customer Info' : 'Party Info') }}
                                            </font>
                                        </font><br>

                                        @php
                                        $name = $customer['name'] ?? ($vendor['name'] ?? null);
                                        $email = $customer['email'] ?? ($vendor['email'] ?? null);
                                        $phone = $customer['phone'] ?? ($vendor['phone'] ?? null);
                                        @endphp

                                        @if ($name)
                                        <font style="vertical-align: inherit; font-size: 14px; color:#000; font-weight: 400;">{{ $name }}</font><br>
                                        @endif

                                        @if ($email)
                                        <font style="vertical-align: inherit; font-size: 14px; color:#000; font-weight: 400;">{{ $email }}</font><br>
                                        @endif

                                        @if ($phone)
                                        <font style="vertical-align: inherit; font-size: 14px; color:#000; font-weight: 400;">{{ $phone }}</font><br>
                                        @endif
                                    </td>

                                    <td style="padding:5px; vertical-align:top; text-align:left; padding-bottom:20px">
                                        <font style="vertical-align: inherit; margin-bottom:25px;">
                                            <font style="vertical-align: inherit; font-size:14px; color:#7367F0; font-weight:600; line-height: 35px;">
                                                Company Info
                                            </font>
                                        </font><br>

                                        @if (!empty($setting->name))
                                        <font style="vertical-align: inherit;">
                                            <font style="vertical-align: inherit; font-size:14px; color:#000; font-weight:400;">
                                                {{ $setting->name }}
                                            </font>
                                        </font><br>
                                        @endif

                                        @if (!empty($setting->email))
                                        <font style="vertical-align: inherit;">
                                            <font style="vertical-align: inherit; font-size:14px; color:#000; font-weight:400;">
                                                {{ $setting->email }}
                                            </font>
                                        </font><br>
                                        @endif

                                        @if (!empty($setting->phone))
                                        <font style="vertical-align: inherit;">
                                            <font style="vertical-align: inherit; font-size:14px; color:#000; font-weight:400;">
                                                {{ $setting->phone }}
                                            </font>
                                        </font><br>
                                        @endif

                                        @if (!empty($setting->address))
                                        <font style="vertical-align: inherit;">
                                            <font style="vertical-align: inherit; font-size:14px; color:#000; font-weight:400;">
                                                {{ $setting->address }}
                                            </font>
                                        </font><br>
                                        @endif

                                        @if (!empty($setting->gst_num))
                                        <font style="vertical-align: inherit;">
                                            <font style="vertical-align: inherit; font-size:14px; color:#000; font-weight:400;">
                                                GST: {{ $setting->gst_num }}
                                            </font>
                                        </font><br>
                                        @endif
                                    </td>

                                    <td style="padding:5px;vertical-align:top;text-align:left;padding-bottom:20px">
                                        <font style="vertical-align: inherit;margin-bottom:25px;">
                                            <font
                                                style="vertical-align: inherit;font-size:14px;color:#7367F0;font-weight:600;line-height: 35px; ">
                                                Invoice Info</font>
                                        </font><br>
                                        <font style="vertical-align: inherit;">
                                            <font
                                                style="vertical-align: inherit;font-size: 14px;color:#000;font-weight: 400;">
                                                Order Number: {{ $sales->invoice_number ?? '' }}
                                            </font>
                                        </font><br>
                                        <font style="vertical-align: inherit;">
                                            <font
                                                style="vertical-align: inherit;font-size: 14px;color:#000;font-weight: 400;">
                                                Order Date: {{ date('d M Y', strtotime($sales->created_at)) }}
                                            </font>
                                        </font><br>
                                        <font style="vertical-align: inherit;">
                                            <font
                                                style="vertical-align: inherit;font-size: 14px;color:#000;font-weight: 400;">
                                                Order Status: {{ ucfirst($sales->status) }}
                                            </font>
                                        </font><br>
                                        <font style="vertical-align: inherit;">
                                            <font
                                                style="vertical-align: inherit;font-size: 14px;color:#000;font-weight: 400;">
                                                Payment Status: {{ ucfirst($paymentStatus) ?? 'Pending' }}
                                            </font>
                                        </font><br>

                                    </td>

                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>

                <tr class="heading" style="background: #F3F2F7;">
                    <td
                        style="padding: 10px; vertical-align: middle; font-weight: 600; color: #5E5873; font-size: 14px; width: 50%;">
                        Product Name
                    </td>
                    <td
                        style="padding: 10px; vertical-align: middle; font-weight: 600; color: #5E5873; font-size: 14px; width: 15%; text-align: right;">
                        Price
                    </td>
                    <td
                        style="padding: 10px; vertical-align: middle; font-weight: 600; color: #5E5873; font-size: 14px; width: 15%; text-align: center;">
                        QTY
                    </td>

                    <td
                        style="padding: 10px; vertical-align: middle; font-weight: 600; color: #5E5873; font-size: 14px; width: 20%; text-align: right;">
                        Subtotal
                    </td>
                </tr>

                @foreach($orderItems as $item)

                <tr class="details" style="border-bottom: 1px solid #E9ECEF;">
                    <td style="padding: 10px; vertical-align: middle;">
                        @php
                        $images = json_decode($item->product->images ?? '[]');
                        $firstImage = !empty($images) ? $images[0] : null;
                        $base64 = null;

                        if ($firstImage) {
                        $imagePath = storage_path('app/public/' . $firstImage);
                        } else {
                        $imagePath = public_path('admin/assets/img/product/product1.jpg');
                        }

                        if (file_exists($imagePath)) {
                        $type = pathinfo($imagePath, PATHINFO_EXTENSION);
                        $data = file_get_contents($imagePath);
                        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                        }
                        @endphp

                        <table style="border-collapse: collapse;">
                            <tr>
                                <td style="padding: 0; vertical-align: middle;">
                                    <img src="{{ $base64 }}" alt="img"
                                        style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px; margin-right: 5px;">
                                </td>
                                <td style="padding: 0 0 0 5px; vertical-align: middle; white-space: nowrap;">
                                    {{ $item->product->name ?? 'Product' }}
                                </td>
                            </tr>
                        </table>
                    </td>

                    <td style="padding: 10px; vertical-align: middle; text-align: right;">
                        @if($setting->currency_position === 'right')
                        {{ $item->price }}{{ $setting->currency_symbol }}
                        @else
                        {{ $setting->currency_symbol }}{{ $item->price }}
                        @endif
                    </td>
                    <td style="padding: 10px; vertical-align: middle; text-align: center;">
                        {{ $item->quantity }}
                    </td>


                    <td style="padding: 10px; vertical-align: middle; text-align: right;">
                        @if($setting->currency_position === 'right')
                        {{ $item->amount_total }}{{ $setting->currency_symbol }}
                        @else
                        {{ $setting->currency_symbol }}{{ $item->amount_total }}
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <table style="width: 100%; margin: 20px 0;">
        <tr>
            <td style="text-align: right;">
                <table style="width: 300px; margin-left: auto; border-collapse: collapse;">
                    <tr>

                        <td
                            style="padding: 12px 16px; text-align: left; background-color: #f3f2f7; border: 1px solid #e0e0e0;">
                            Subtotal</td>
                        <td style="padding: 12px 16px; text-align: right; border: 1px solid #e0e0e0;">
                            @if($setting->currency_position === 'right')
                            {{ $subtotal }}
                            @else

                            {{ $subtotal }}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td
                            style="padding: 12px 16px; text-align: left; background-color: #f3f2f7; border: 1px solid #e0e0e0;">
                            Discount</td>
                        <td style="padding: 12px 16px; text-align: right; border: 1px solid #e0e0e0;">
                            {{ $sales->discount ?? 0 }}%
                            (@if($setting->currency_position === 'right')
                            {{ $discountAmount }}
                            @else
                            {{ $discountAmount }}
                            @endif)
                        </td>
                    </tr>
                    <tr>
                        <td
                            style="padding: 12px 16px; text-align: left; background-color: #f3f2f7; border: 1px solid #e0e0e0;">
                            After Discount</td>
                        <td style="padding: 12px 16px; text-align: right; border: 1px solid #e0e0e0;">
                            @if($setting->currency_position === 'right')
                            {{ $afterDiscount }}
                            @else
                            {{ $afterDiscount }}
                            @endif
                        </td>
                    </tr>

                    <tr>
                        <td
                            style="padding: 12px 16px; text-align: left; background-color: #f3f2f7; border: 1px solid #e0e0e0;">
                            Shipping</td>
                        <td style="padding: 12px 16px; text-align: right; border: 1px solid #e0e0e0;">
                            @if($setting->currency_position === 'right')
                            {{ $shipping }}
                            @else
                            {{ $shipping }}
                            @endif
                        </td>
                    </tr>
                    @if(!empty($taxDetails))
                    @foreach($taxDetails as $tax)
                    <tr>
                        <td
                            style="padding: 12px 16px; text-align: left; background-color: #f3f2f7; border: 1px solid #e0e0e0;">
                            {{ $tax['name'] ?? '' }} ({{ $tax['rate'] ?? '' }}%)
                        </td>
                        <td style="padding: 12px 16px; text-align: right; border: 1px solid #e0e0e0;">
                            @if($setting->currency_position === 'right')
                            {{ $tax['amount'] ?? '' }}{{ $setting->currency_symbol }}
                            @else
                            {{ $setting->currency_symbol }}{{ $tax['amount'] ?? '' }}
                            @endif
                        </td>
                    </tr>
                    @endforeach
                    @endif
                    <tr style="font-weight: bold;">
                        <td
                            style="padding: 12px 16px; text-align: left; background-color: #f3f2f7; border: 1px solid #e0e0e0;">
                            Total</td>
                        <td style="padding: 12px 16px; text-align: right; border: 1px solid #e0e0e0;">
                            @if($setting->currency_position === 'right')
                            {{ $finalTotal }}
                            @else
                            {{ $finalTotal }}
                            @endif
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <div style="text-align: center; margin-top: 100px;">
        <h2 style="color: #7367F0; font-size: 24px; margin-bottom: 10px;">Thank You for Your Purchase!</h2>
        <p style="font-size: 16px; color: #555;">We appreciate your business and hope to serve you again soon.</p>
    </div>
</div>