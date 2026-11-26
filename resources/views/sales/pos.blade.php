@extends('layout.app')

@section('title', 'Add Sale')

@section('content')
<style>
    .disabled-product {
        opacity: 0.6;
        pointer-events: none;
        /* disables clicking inside */
    }

    .paymentmethod.active {
        /* background-color: #1b2850; */
        color: white;
        /* border-radius: 5px; */
    }

    .paymentmethod.active:hover {
        /* background-color: white; */
        color: white;
        /* Dark text for contrast */
        /* border: 1px solid #1b2850; */
        /* Optional: border to define shape */
    }

    .tabs_wrapper ul.tabs {
        display: flex;
        overflow-x: auto;
        gap: 10px;
        padding: 0;
        margin: 0;
        list-style: none;
        scrollbar-width: thin;
        width: auto;
        scrollbar-color: #ff9f43 #f1f1f1;
    }

    .tabs_wrapper ul.tabs::-webkit-scrollbar {
        display: none;
    }

    .tabs_wrapper ul.tabs li {
        flex: 0 0 auto;
        cursor: pointer;
        white-space: nowrap;
        width: auto;
        /* ðŸ‘ˆ Prevent tab name from wrapping */
        padding: 0;
    }

    .product-details {
        background: #fff;
        padding: 10px 16px;
        box-shadow: none;
        border: none !important;
        display: flex;
        flex-direction: column;
        align-items: center;
        transition: 0.3s ease;
        min-width: max-content;
        /* ðŸ‘ˆ Ensure box width fits content */
    }

    .product-details h6 {
        font-size: 14px;
        color: #000;
        margin: 0;
        text-align: center;
        white-space: nowrap;
        /* ðŸ‘ˆ Prevent text wrap */
        overflow: hidden;
        text-overflow: ellipsis;
        /* ðŸ‘ˆ Optional: show ... if too long */
        max-width: 100%;
        /* ðŸ‘ˆ Avoid overflow outside parent */
    }

    .tabs_wrapper ul.tabs li.active .product-details {
        background: transparent;
        /* border-bottom: 2px solid #ff9f43 !important; */
    }

    .tabs_wrapper ul.tabs li.active .product-details h6 {
        color: #ff9f43;
    }

    .pos-top-controls {
        display: flex;
        align-items: center;
        justify-content: flex-start;
    }
    .pos-gst-options.custom-gst-group {
        display: inline-flex;
        align-items: center;
        background-color: #f3f4f6;
        border-radius: 50px;
        padding: 3px;
        border: 1px solid #e5e7eb;
        gap: 0;
    }
    .custom-gst-btn {
        margin: 0;
        cursor: pointer;
    }
    .custom-gst-btn input {
        display: none;
    }
    .custom-gst-btn span {
        display: inline-block;
        padding: 6px 16px;
        border-radius: 50px;
        font-size: 13px;
        font-weight: 500;
        color: #6b7280;
        transition: all 0.2s;
    }
    .custom-gst-btn input:checked + span {
        background-color: #fff;
        color: #111827;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    .custom-pill-group {
        display: inline-flex;
        align-items: center;
    }
    .custom-pill-btn {
        margin: 0;
        cursor: pointer;
        flex-shrink: 0;
    }
    .custom-pill-btn input {
        display: none;
    }
    .custom-pill-btn span {
        display: inline-block;
        padding: 6px 16px;
        border-radius: 50px;
        font-size: 13px;
        font-weight: 500;
        color: #4b5563;
        border: 1px solid #e5e7eb;
        background-color: #fff;
        transition: all 0.2s;
        white-space: nowrap;
    }
    .custom-pill-btn input:checked + span {
        color: #f97316;
        border-color: #f97316;
        background-color: #fff;
    }
    .pos-action-pills-grid {
        display: grid !important;
        grid-template-columns: repeat(3, 1fr) !important;
        gap: 8px !important;
        margin-bottom: 8px !important;
    }
    .pos-action-pills-grid .pos-action-pill {
        width: 100% !important;
        white-space: nowrap !important;
        text-overflow: ellipsis !important;
        padding: 6px 6px !important;
        font-size: 12px !important;
        justify-content: center !important;
        border-radius: 6px !important;
        display: inline-flex;
        align-items: center !important;
        gap: 6px !important;
        background: #fff !important;
        border: 1px solid #d8d6de !important;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05) !important;
        color: #5e5873 !important;
        cursor: pointer !important;
        transition: all 0.2s ease !important;
    }
    .pos-action-pills-grid .pos-action-pill:hover {
        background: #f8f9fa !important;
        border-color: #ff9f43 !important;
        color: #ff9f43 !important;
    }
    .pos-action-pills-grid .pos-action-pill i {
        color: #ff9f43 !important;
        font-size: 11px !important;
    }
    .pos-action-pills-grid .pos-action-pill.active {
        background: #fff3e8 !important;
        border-color: #ff9f43 !important;
        color: #ff9f43 !important;
    }

    /* ===== CUSTOMER DETAILS & ORDER HEADER (Matching main-erp-product) ===== */
    .pos-back-btn {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        border: 1px solid #ff9f43;
        background: #ff9f43;
        color: #fff !important;
        border-radius: 6px;
        padding: 4px 12px;
        font-size: 13px;
        font-weight: 600;
        line-height: 1;
        height: 32px;
        text-decoration: none !important;
        transition: background 0.2s;
    }
    .pos-back-btn:hover {
        background: #e08930;
        color: #fff !important;
    }

    .pos-fullscreen-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border: 1.5px solid #e0e0e0;
        background: #fff;
        color: #4b5563;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.2s;
        padding: 0;
    }
    .pos-fullscreen-btn:hover {
        background: #f3f4f6;
        color: #111827;
    }

    .pos-order-number-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #fff;
        border: 1.5px solid #e0e0e0;
        border-radius: 6px;
        padding: 4px 12px;
        font-size: 13px;
        font-weight: 600;
        color: #1b2850;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.05);
        white-space: nowrap;
        height: 32px;
    }
    .pos-order-number-label {
        color: #888;
        font-weight: 500;
        font-size: 12px;
    }
    .pos-order-number-value {
        color: #ff9f43;
        font-weight: 700;
        font-size: 13px;
    }

    .customer-section-title {
        display: flex;
        align-items: center;
        margin-bottom: 8px;
        font-size: 14px;
        font-weight: 600;
        color: #1b2850;
    }

    .customer-details-box {
        border: 1.5px solid #ff9f43;
        border-radius: 8px;
        padding: 6px 10px 8px !important;
        background: #fff;
        margin-bottom: 8px !important;
    }

    .customer-details-box .pos-customer-row {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin: 0 !important;
        width: 100% !important;
    }

    .customer-details-box .pos-customer-field {
        flex: 1 1 0;
        min-width: 140px;
        padding: 0 !important;
        margin-bottom: 0 !important;
    }

    .customer-details-box label {
        display: flex;
        align-items: center;
        font-size: 11px;
        font-weight: 500;
        color: #5e5873;
        margin-bottom: 3px !important;
    }

    .customer-details-box .form-control {
        background-color: #f8f9fa !important;
        border: 1px solid #e0e0e0;
        border-radius: 6px;
        height: 34px !important;
        font-size: 13px;
        color: #333;
        padding: 4px 10px;
    }

    .customer-details-box .select2-container .select2-selection--single {
        background-color: #f8f9fa !important;
        border: 1px solid #e0e0e0;
        border-radius: 6px;
        height: 34px !important;
    }

    .customer-details-box .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 34px !important;
        padding-left: 10px;
        font-size: 13px;
    }

    .customer-details-box .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 32px !important;
    }

    .pos-order-row {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin: 0 0 8px 0 !important;
    }

    .pos-order-row .pos-customer-field {
        flex: 1 1 0;
        min-width: 110px;
        padding: 0 !important;
        margin-bottom: 0 !important;
    }

    .pos-order-row label {
        font-size: 11px;
        font-weight: 500;
        color: #5e5873;
        margin-bottom: 3px !important;
        display: block;
    }

    .pos-order-row .form-control {
        height: 34px !important;
        font-size: 13px;
        border-radius: 6px;
        padding: 4px 10px;
        background-color: #f8f9fa;
        border: 1px solid #e0e0e0;
    }

    .pos-order-row .select2-container .select2-selection--single {
        height: 34px !important;
        border-radius: 6px;
        background-color: #f8f9fa;
        border: 1px solid #e0e0e0;
    }

    .pos-order-row .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 34px !important;
        padding-left: 10px;
        font-size: 13px;
    }

    .pos-order-row .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 32px !important;
    }

    /* Dark Theme Support */
    :root[data-theme="dark"] .customer-details-box {
        background: #0f172a !important;
        border-color: #ff9f43 !important;
    }
    :root[data-theme="dark"] .customer-details-box label,
    :root[data-theme="dark"] .pos-order-row label {
        color: #cbd5e1 !important;
    }
    :root[data-theme="dark"] .customer-details-box .form-control,
    :root[data-theme="dark"] .pos-order-row .form-control {
        background-color: #1e293b !important;
        border-color: rgba(255, 255, 255, 0.14) !important;
        color: #f8fafc !important;
    }
    :root[data-theme="dark"] .pos-fullscreen-btn,
    :root[data-theme="dark"] .pos-order-number-badge {
        background: #0f172a !important;
        border-color: rgba(255, 255, 255, 0.14) !important;
        color: #f8fafc !important;
    }
    :root[data-theme="dark"] .customer-section-title {
        color: #f8fafc !important;
    }

    /* ── Payment Panel & Drag Handle (Matching main-erp-product) ── */
    .payment_panel {
        position: fixed;
        bottom: 0;
        background-color: white;
        z-index: 999;
        box-shadow: 0 -4px 16px rgba(0,0,0,0.08);
        border-radius: 12px 12px 0 0;
        box-sizing: border-box;
    }

    .page-wrapper .content {
        padding-bottom: 240px !important;
    }

    .payment-panel-handle {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 6px 12px 4px;
        cursor: grab;
        background: linear-gradient(to bottom, #f0f2f5, #e8eaf0);
        border-radius: 12px 12px 0 0;
        border-bottom: 1px solid #d8dce6;
        user-select: none;
        position: relative;
    }

    .payment-panel-handle:active {
        cursor: grabbing;
    }

    .handle-pill {
        width: 40px;
        height: 4px;
        background: #b0b8cc;
        border-radius: 99px;
        flex-shrink: 0;
    }

    .handle-hint {
        font-size: 10px;
        color: #8892a4;
        letter-spacing: 0.3px;
        white-space: nowrap;
    }

    .panel-min-btn {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        background: #e2e6ed;
        border: none;
        border-radius: 50%;
        width: 22px;
        height: 22px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        color: #555;
        transition: background 0.2s;
        padding: 0;
    }

    .panel-min-btn:hover {
        background: #cdd3dd;
    }

    #paymentPanelBody {
        overflow: hidden;
        transition: max-height 0.3s ease;
        padding: 10px 14px 14px;
    }

    #paymentPanelBody.panel-collapsed {
        max-height: 0 !important;
        padding-top: 0 !important;
        padding-bottom: 0 !important;
    }

    #paymentPanel .pos-pay-title {
        font-size: 13px;
        font-weight: 600;
        color: #6e6b7b;
        white-space: nowrap;
    }

    #paymentPanel .pos-pay-value-wrap {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        padding: 2px 8px 2px 7px;
        min-height: 24px;
    }

    #paymentPanel #posPaySummary {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 13px;
        font-weight: 700;
        color: #1b2850;
        letter-spacing: 0;
        white-space: nowrap;
    }

    #paymentPanel #posPaySummary::before {
        content: "";
        width: 8px;
        height: 8px;
        border-radius: 2px;
        background: #22c55e;
        flex-shrink: 0;
        display: inline-block;
    }

    #paymentPanel .handle-caret {
        color: #8892a4;
        font-size: 10px;
        line-height: 1;
        transition: transform 0.2s ease;
        display: inline-block;
    }

    #paymentPanel:not(.pos-pay-collapsed) .handle-caret {
        transform: rotate(180deg);
    }

    /* Desktop: NEVER collapse payment section, keep original handle hint */
    @media screen and (min-width: 992px) {
        #paymentPanel .pos-pay-title,
        #paymentPanel .pos-pay-value-wrap {
            display: none !important;
        }
        #paymentPanel .handle-hint {
            display: inline !important;
        }
        #paymentPanel.pos-pay-collapsed #paymentSection,
        #paymentPanel.pos-pay-collapsed #paymentPanelBody {
            display: block !important;
        }
    }

    /* Mobile / Responsive: collapsible payment bar with method pill */
    @media screen and (max-width: 991.98px) {
        #paymentPanel .handle-hint {
            display: none !important;
        }
        #paymentPanel #paymentPanelHandle {
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            padding: 8px 14px 6px !important;
            background: #fff !important;
            border-bottom: 1px solid #f1f2f6 !important;
            border-radius: 14px 14px 0 0 !important;
            cursor: pointer !important;
            gap: 10px !important;
        }
        #paymentPanel #paymentPanelHandle .handle-pill {
            width: 36px !important;
            height: 4px !important;
            background: #d1d5db !important;
            border-radius: 99px !important;
            flex-shrink: 0 !important;
        }
        #paymentPanel .pos-pay-title {
            display: inline-block !important;
            font-size: 13px !important;
            font-weight: 600 !important;
            color: #4b5563 !important;
            white-space: nowrap !important;
            margin-right: auto !important;
            margin-left: 6px !important;
        }
        #paymentPanel .pos-pay-value-wrap {
            display: inline-flex !important;
            align-items: center !important;
            gap: 6px !important;
            background: #f9fafb !important;
            border: 1px solid #e5e7eb !important;
            border-radius: 6px !important;
            padding: 4px 10px !important;
            min-height: 28px !important;
        }
        #paymentPanel #posPaySummary {
            display: inline-flex !important;
            align-items: center !important;
            gap: 6px !important;
            font-size: 13px !important;
            font-weight: 700 !important;
            color: #111827 !important;
            white-space: nowrap !important;
        }
        #paymentPanel #posPaySummary::before {
            content: "" !important;
            width: 8px !important;
            height: 8px !important;
            border-radius: 2px !important;
            background: #22c55e !important;
            flex-shrink: 0 !important;
            display: inline-block !important;
        }
        #paymentPanel .handle-caret {
            color: #6b7280 !important;
            font-size: 11px !important;
            line-height: 1 !important;
            transition: transform 0.2s ease !important;
            display: inline-block !important;
        }
        #paymentPanel:not(.pos-pay-collapsed) .handle-caret {
            transform: rotate(180deg) !important;
        }
        #paymentPanel.pos-pay-collapsed #paymentSection,
        #paymentPanel.pos-pay-collapsed #paymentPanelBody {
            display: none !important;
        }
        #paymentPanel {
            position: fixed !important;
            left: 0 !important;
            right: 0 !important;
            bottom: 50px !important;
            width: 100% !important;
            margin-left: 0 !important;
            border-radius: 14px 14px 0 0 !important;
            background-color: white !important;
            box-shadow: 0 -4px 16px rgba(0,0,0,0.12) !important;
            z-index: 1040 !important;
            max-height: calc(100vh - 100px) !important;
            overflow-y: auto !important;
        }
    }

    .setvaluecash ul {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        padding: 0;
        margin: 0;
        list-style: none;
    }
    .setvaluecash ul li {
        flex: 1 1 0;
        min-width: 60px;
    }
    .setvaluecash ul li a {
        border: 1px solid #e9ecef;
        color: #495057;
        font-size: 11px;
        font-weight: 600;
        min-height: 38px;
        border-radius: 6px;
        padding: 4px 6px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        transition: all 0.2s ease;
        background: #fff;
    }
    .setvaluecash ul li a.active {
        background-color: #1b2850 !important;
        color: #ffffff !important;
        border-color: #1b2850 !important;
    }
    .setvaluecash ul li a.active i {
        color: #ffffff !important;
    }
    .setvaluecash ul li a:hover {
        border-color: #1b2850;
    }

    .payment-detail-actions {
        display: flex;
        justify-content: flex-end;
        margin-top: 6px;
    }
    .payment-detail-cancel {
        border: 1px solid #ea5455;
        background: #fff;
        color: #ea5455;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        padding: 4px 12px;
        cursor: pointer;
    }
    .payment-detail-cancel:hover {
        background: #ea5455;
        color: #fff;
    }

    .body_space {
        /*margin-bottom: 2rem;*/
        padding: 9px !important;
    }

    .paymentmethod.active {
        background-color: #1b2850;
        color: #ffffff;
    }

    .paymentmethod.active svg {
        fill: #ffffff;
    }

    .productsetimgin {
        height: 100px;
        object-fit: contain;
        margin: 1rem auto 0;
        display: block;
    }

    .productsetbtn button {
        display: none;
    }

    .search_custom_product {
        width: 100%;
    }

    .payment_panel {
        z-index: 999;
    }

    .bill-generate {
        color: #fff;
        background: #1b2850;
        border-radius: 5px;
        margin: 0 0 15px;
        padding: 12px;
        cursor: pointer;
    }

    @media screen and (max-width: 768px) {
        .payment_panel {
            position: fixed;
            bottom: 50px;
            background-color: white;
            width: 80%;
            margin-left: -6px;
        }

        .productset {
            display: flex;
        }

        .productset .productsetimg {

            width: 30%;
        }

        .productsetimgin {
            object-fit: cover;
            height: 90px;
            margin: 0;
            max-width: 110px;
        }

        .productsetcontent {
            text-align: left !important;
        }



        .productsetbtn {
            position: absolute;
            right: 10px;
            top: 43px;
        }

        .productsetbtn button {
            display: block;
            width: 100%;
            height: 33px;
            background-color: #1b2850;
            color: white !important;
            border: none;
            border-radius: 5px;
        }

        .productsetbtn button:active {
            color: white;
        }

        .product-lists {
            flex-direction: row !important;
        }

        .body_space_two {
            margin-bottom: 4rem;
        }

        a.confirm-text.remove-item {
            /* font-size: 116px; */
            margin: 6px;
        }




    }
</style>
<style>
    .gst-header {
        display: flex;
        align-items: center;
        gap: 25px;
        padding: 12px 20px;
    }

    .gst-header .header-title {
        min-width: 140px;
        font-weight: bold;
        margin: 0;
        font-size: 18px;
        color: #444;
    }

    .custom-radio-label {
        display: flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        font-weight: 600;
        color: #333;
        user-select: none;
        position: relative;
        padding-left: 28px;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        transition: color 0.2s ease;
    }

    .custom-radio-label input[type="radio"] {
        appearance: none;
        -webkit-appearance: none;
        width: 18px;
        height: 18px;
        border: 2.5px solid #888;
        border-radius: 50%;
        position: absolute;
        left: 0;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        transition: all 0.25s ease;
    }

    .custom-radio-label input[type="radio"]:checked {
        border-color: #0056b3;
        background-color: #0056b3;
    }

    .custom-radio-label input[type="radio"]:checked::after {
        content: "";
        display: block;
        width: 8px;
        height: 8px;
        background: white;
        border-radius: 50%;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }

    .custom-radio-label:hover input[type="radio"] {
        border-color: #5a50cc;
    }

    .custom-radio-label:hover {
        color: #0056b3;
    }
</style>
<div class="content">

    <div class="row">

        <div class="col-lg-7 col-sm-12 tabs_wrapper" id="pos-left-panel">

            <div class="pos-top-controls mb-2 mb-md-3">
                <div class="pos-gst-options custom-gst-group">
                    <label class="custom-gst-btn" tabindex="0">
                        <input type="radio" name="gst_option" id="without_gst" value="without" checked />
                        <span>Without GST</span>
                    </label>
                    <label class="custom-gst-btn" tabindex="0">
                        <input type="radio" name="gst_option" id="with_gst" value="with" />
                        <span>With GST</span>
                    </label>
                </div>
            </div>

            <div class="scanner-search-wrap search_custom_product mb-2 mb-md-3">
                <div class="header-search d-flex align-items-center position-relative w-100" style="gap: 8px;">
                    <!-- Scanner Button -->
                    <button type="button" id="scanBarcodeBtn" class="btn"
                        data-bs-toggle="modal" data-bs-target="#barcodeScannerModal"
                        style="background: #f8f9fa; border: 1px solid #ced4da; border-radius: 6px; color: #495057; display: inline-flex; align-items: center; justify-content: center; width: 38px; min-width: 38px; height: 38px; flex-shrink: 0; padding: 0; cursor: pointer;"
                        title="Scan Barcode">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                            fill="currentColor" class="bi bi-qr-code-scan" viewBox="0 0 16 16">
                            <path d="M0 .5A.5.5 0 0 1 .5 0h3a.5.5 0 0 1 0 1H1v2.5a.5.5 0 0 1-1 0zm12 0a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 .5.5v3a.5.5 0 0 1-1 0V1h-2.5a.5.5 0 0 1-.5-.5M.5 12a.5.5 0 0 1 .5.5V15h2.5a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5v-3a.5.5 0 0 1 .5-.5m15 0a.5.5 0 0 1 .5.5v3a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1 0-1H15v-2.5a.5.5 0 0 1 .5-.5M4 4h1v1H4z" />
                            <path d="M7 2H2v5h5zM3 3h3v3H3zm2 8H4v1h1z" />
                            <path d="M7 9H2v5h5zm-4 1h3v3H3zm8-6h1v1h-1z" />
                            <path d="M9 2h5v5H9zm1 1v3h3V3zM8 8v2h1v1H8v1h2v-2h1v2h1v-1h2v-1h-3V8zm2 2H9V9h1zm4 2h-1v1h-2v1h3zm-4 2v-1H8v1z" />
                            <path d="M12 9h2V8h-2z" />
                        </svg>
                    </button>
                    <!-- Search Icon and Input -->
                    <div class="position-relative flex-grow-1 pos-search-input-box" style="min-width: 0;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#6c757d" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); pointer-events: none; z-index: 5;">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                        <input type="text" id="customerSearch1"
                            class="form-control" placeholder="Search..."
                            autocomplete="off" style="height: 38px; font-size: 14px; padding-left: 36px; border-radius: 6px; border: 1px solid #ced4da; width: 100%;">
                        <!-- Search Results -->
                        <div id="searchResults1"
                            class="list-group bg-white position-absolute rounded shadow mt-1"
                            style="z-index: 1050; max-height: 300px; overflow-y: auto; display: none; width: 100%; top: 100%; left: 0;">
                        </div>
                    </div>
                    <!-- + Add Product Button -->
                    <a href="{{ route('product.add') }}" class="btn btn-sm pos-action-btn"
                        style="background-color: #ff9f43; color: white; white-space: nowrap; border-radius: 6px; height: 38px; display: inline-flex; align-items: center; justify-content: center; padding: 0 16px; font-weight: 500;">
                        + Add Product
                    </a>
                </div>
            </div>

            <div class="d-flex align-items-center mb-2 mb-md-4">
                <div class="custom-pill-group mb-0" style="margin-right: 15px; flex-shrink: 0;">
                    <label class="custom-pill-btn" tabindex="0">
                        <input class="form-check-input" type="checkbox" id="mainBranchToggle">
                        <span>All Products</span>
                    </label>
                </div>

                <ul class="tabs border-0 mb-0 flex-grow-1" style="display: flex; flex-wrap: nowrap; overflow-x: auto; -webkit-overflow-scrolling: touch;">
                    @php
                    $hasProducts = false;
                    @endphp

                    @foreach($categories as $cat)
                    @php
                    $currentBranch = $branchIdToUse ?? (session('selectedSubAdminId') ?: 1);
                    $availableProducts = $cat->products()
                        ->where('isDeleted', 0)
                        ->where(function ($q) use ($currentBranch) {
                            if (!empty($currentBranch) && $currentBranch != 1) {
                                $q->where('branch_id', $currentBranch)
                                  ->orWhere('branch_id', 1);
                            } else {
                                $q->where('branch_id', 1)->orWhereNull('branch_id');
                            }
                        })
                        ->get();
                    if ($availableProducts->isEmpty()) {
                        $availableProducts = $cat->products()->where('isDeleted', 0)->get();
                    }
                    @endphp

                    @if($availableProducts && $availableProducts->count() > 0)
                    @php
                    $hasProducts = true;
                    @endphp
                    <li id="{{ $cat->id }}">
                        <div class="product-details">
                            <h6 style="text-transform: capitalize;">{{ $cat->name }}</h6>
                        </div>
                    </li>
                    @endif
                    @endforeach
                </ul>
            </div>

            @if(!$hasProducts)
            <div class="text-center my-4">
                <h6 class="text-muted mb-3">No data available</h6>
                <a href="{{ route('product.add') }}" class="btn btn-primary py-1">
                    <i class="fas fa-plus me-1"></i> Add Items
                </a>
            </div>
            @endif

            <div class="tabs_container">

            </div>
        </div>
        <div class="col-lg-5 col-sm-12" id="pos-right-panel">

            <div class="card card-order">
                <div class="card-body body_space" style="padding: 12px 14px 4px;">
                    <div class="customer-section-title">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <i class="fas fa-user-friends" style="color: #ff9f43;"></i> Customer details
                        </div>
                        <div class="customer-action-btns" style="display:flex; gap:6px; margin-left: 8px;">
                            <button type="button" id="openQuickAddCustomerBtn"
                                class="btn btn-outline-secondary btn-sm"
                                style="border-radius: 6px; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; background: #fff; border-color: #d8d6de; box-shadow: 0 2px 4px rgba(0,0,0,0.05);"
                                title="Add New Customer">
                                <i class="fas fa-plus" style="color: #ff9f43;"></i>
                            </button>
                            <button type="button" id="openEditCustomerBtn"
                                class="btn btn-outline-secondary btn-sm"
                                style="border-radius: 6px; width: 30px; height: 30px; display: none; align-items: center; justify-content: center; background: #fff; border-color: #d8d6de; box-shadow: 0 2px 4px rgba(0,0,0,0.05);"
                                title="Edit Selected Customer">
                                <i class="fas fa-pencil-alt" style="color: #ff9f43;"></i>
                            </button>
                        </div>
                        <div class="d-flex align-items-center gap-2 ms-auto">
                            {{-- Fullscreen Toggle --}}
                            <button type="button" class="pos-fullscreen-btn" id="posFullscreenToggle"
                                title="Expand panel">
                                <i class="fas fa-expand"></i>
                            </button>

                            {{-- Order Number Badge --}}
                            <div class="pos-order-number-badge" id="posOrderNumberBadge">
                                <span class="pos-order-number-label" id="posOrderNumberLabel">Order No:</span>
                                <span class="pos-order-number-value" id="posOrderNumberValue">{{ $nextSaleNo ?? 'ORD-01' }}</span>
                            </div>
                            <div class="page-btn">
                                <a href="{{ route('sales.list') }}" class="pos-back-btn">
                                    <i class="fa-solid fa-arrow-left"></i>
                                    Back
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- Customer Details Box --}}
                    <div class="customer-details-box">
                        <div class="row w-100 m-0 pos-customer-row">
                            <!-- Customer Name -->
                            <div class="col-md-6 col-12 pos-customer-field" id="customerSection">
                                <div class="select-split select-group w-100">
                                    <div class="select-group w-100">
                                        <label><i class="far fa-user-circle" style="color: #ff9f43; margin-right: 5px;"></i> Customer name</label>
                                        <select id="customer_name" name="customer_name" class="form-control select2">
                                            <option value="">Select Customer</option>
                                            @foreach ($customers as $username)
                                                <option value="{{ $username->id }}"
                                                    data-phone="{{ $username->phone ?? '' }}"
                                                    data-gst="{{ $username->gst_num ?? '' }}"
                                                    data-group="{{ $username->group_by_price ?? '' }}">
                                                    {{ $username->name }}{{ $username->phone ? ' - ' . $username->phone : '' }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <span class="error_customername text-danger"></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Customer Phone -->
                            <div class="col-md-6 col-12 pos-customer-field" id="customerPhoneSection">
                                <div class="select-split">
                                    <div class="select-group w-100">
                                        <label><i class="fas fa-phone-alt" style="color: #ff9f43; margin-right: 5px;"></i> Customer phone</label>
                                        <input type="tel" id="customer_phone" class="form-control"
                                            placeholder="Customer number" name="customer_phone">
                                        <span class="error_customerphone text-danger"></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Vendor Section (for vendor user_type) -->
                            <div class="col-md-6 col-12 pos-customer-field" id="vendorDropdown" style="display: none;">
                                <div class="select-split">
                                    <div class="select-group w-100">
                                        <label><i class="far fa-user-circle" style="color: #ff9f43; margin-right: 5px;"></i> Vendor Name</label>
                                        <select name="vendor_id" id="vendor_id" class="form-control select2">
                                            <option value="">Select Vendor</option>
                                            @foreach ($vendors as $vendor)
                                                <option value="{{ $vendor->id }}" data-phone="{{ $vendor->phone }}"
                                                    data-type="{{ $vendor->group_by_price }}">
                                                    {{ $vendor->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <span class="text-danger error-vendor_id"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 col-12 pos-customer-field" id="vendorPhoneSection" style="display: none;">
                                <div class="select-split">
                                    <div class="select-group w-100">
                                        <label><i class="fas fa-phone-alt" style="color: #ff9f43; margin-right: 5px;"></i> Vendor Phone</label>
                                        <input type="tel" id="vendor_phone" class="form-control"
                                            placeholder="Vendor number" name="vendor_phone">
                                        <span class="text-danger error-vendorphone"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Garage fields in Row 2 --}}
                    <div class="row w-100 m-0 pos-order-row">
                        @if(in_array(auth()->user()->role, ['admin', 'sub-admin']))
                        <div class="col-md-4 col-12 pos-customer-field" id="staffSection">
                            <div class="select-split">
                                <div class="select-group w-100">
                                    <label>Staff Name *</label>
                                    <select id="staff_name" name="staff_name" class="form-control select2">
                                        <option value="">Select Staff</option>
                                        @foreach ($staff as $username)
                                            <option value="{{ $username->id }}" data-phone="{{ $username->phone }}"
                                                data-group="{{ $username->group_by_price }}">
                                                {{ $username->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <span class="error_staff text-danger"></span>
                                </div>
                            </div>
                        </div>
                        @endif

                        @if(in_array(auth()->user()->role, ['admin', 'sub-admin']))
                        <div class="col-md-4 col-12 pos-customer-field">
                            <div class="select-split">
                                <div class="select-group w-100">
                                    <label>Model *</label>
                                    <select id="car_model" name="car_model" class="form-control select2">
                                        <option value="">Select Model</option>
                                        @foreach ($model as $username)
                                            <option value="{{ $username->id }}">{{ $username->model_name }}</option>
                                        @endforeach
                                    </select>
                                    <span class="error_car_model text-danger"></span>
                                </div>
                            </div>
                        </div>
                        @endif

                        @if(in_array(auth()->user()->role, ['admin', 'sub-admin']))
                        <div class="col-md-4 col-12 pos-customer-field">
                            <div class="select-split">
                                <div class="select-group w-100">
                                    <label>Vehicle No. *</label>
                                    <input type="text" id="vehicle_no" class="form-control"
                                        placeholder="Vehicle No." name="vehicle_no">
                                    <span class="error_vehicle_no text-danger"></span>
                                </div>
                            </div>
                        </div>
                        @endif

                        <div class="col-md-6" style="display: none;">
                            <div class="form-group">
                                <label>User Type</label>
                                <select name="user_type" id="user_type" class="form-control">
                                    <option value="customer">Customer</option>
                                    <option value="vendor">Vendor</option>
                                </select>
                                <span class="text-danger error-usertype"></span>
                            </div>
                        </div>
                    </div>
                    <div class="split-card">

                    </div>
                    <div class="card-body pt-0">
                        <div class="totalitem">
                            <h4>Total items : 4</h4>
                            <a href="javascript:void(0);" class="clear_items">Clear all</a>
                        </div>
                        <span class="error_product text-danger"></span>
                        <div class="product-table">
                        </div>
                    </div>
                    <div class="split-card">
                    </div>
                    <div class="card-body pt-0 pb-2 body_space_two">

                        <!-- Action Pills Grid (3 Columns) -->
                        <div class="col-lg-12 px-0">
                            <div class="pos-action-pills-grid">
                                <a href="javascript:void(0);" id="toggle_delivery_cost"
                                    class="btn btn-outline-secondary btn-sm pos-action-pill w-100"
                                    title="Delivery Cost">
                                    <span>Delivery Cost</span>
                                    <i class="fas fa-plus"></i>
                                </a>

                                <a href="javascript:void(0);" id="toggle_tds"
                                    class="btn btn-outline-secondary btn-sm pos-action-pill w-100"
                                    title="TDS">
                                    <span>TDS</span>
                                    <i class="fas fa-plus"></i>
                                </a>

                                <a href="javascript:void(0);" id="toggle_other_charges"
                                    class="btn btn-outline-secondary btn-sm pos-action-pill w-100"
                                    title="Add Other Charges">
                                    <span>Other Charges</span>
                                    <i class="fas fa-plus"></i>
                                </a>

                                <a href="javascript:void(0);" id="toggle_labour_items"
                                    class="btn btn-outline-secondary btn-sm pos-action-pill w-100"
                                    title="Add Labour Items">
                                    <span>Labour</span>
                                    <i class="fas fa-plus"></i>
                                </a>

                                <a href="javascript:void(0);" id="toggle_overall_discount"
                                    class="btn btn-outline-secondary btn-sm pos-action-pill w-100"
                                    title="Overall Discount">
                                    <span>Discount</span>
                                    <i class="fas fa-plus"></i>
                                </a>

                                <div class="pos-action-pill-wrapper w-100" style="display: flex; align-items: center;">
                                    <button type="button" id="openAddRemarksBtn"
                                        class="btn btn-outline-secondary btn-sm pos-action-pill w-100"
                                        title="Add Remarks">
                                        <span>Remarks</span>
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>

                                <a href="javascript:void(0);" id="toggle_exchange_charges"
                                    class="btn btn-outline-secondary btn-sm pos-action-pill w-100"
                                    title="Exchange">
                                    <span>Exchange</span>
                                    <i class="fas fa-plus"></i>
                                </a>
                            </div>
                        </div>

                        <!-- Delivery Cost Section -->
                        <div class="row mt-2" id="delivery_cost_section" style="display: none;">
                            <div class="col-lg-12">
                                <div class="select-split">
                                    <div class="select-group w-100">
                                        <label for="shipping" class="mb-1" style="font-size: 13px; font-weight: 500;">Delivery Cost</label>
                                        <input type="number" class="form-control" placeholder="Delivery Cost"
                                            name="shipping" id="shipping" min="0" step="0.01">
                                        <span class="error_shipping text-danger"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- TDS Section -->
                        <div class="row mt-2" id="tds_section" style="display: none;">
                            <div class="col-lg-6 col-6">
                                <div class="select-group w-100">
                                    <label for="tds_percentage" style="font-size: 12px; font-weight: 500;">TDS Percentage (%)</label>
                                    <input type="number" class="form-control" name="tds_percentage"
                                        id="tds_percentage" placeholder="0" min="0" max="100" step="0.01">
                                </div>
                            </div>
                            <div class="col-lg-6 col-6">
                                <div class="select-group w-100">
                                    <label for="tds_amount" style="font-size: 12px; font-weight: 500;">TDS Amount</label>
                                    <input type="text" class="form-control" name="tds_amount" id="tds_amount"
                                        min="0" value="0.00" readonly>
                                </div>
                            </div>
                        </div>

                        <!-- Other Charges Section -->
                        <div class="row mt-2" id="other_charges_section" style="display: none;">
                            <div class="col-lg-12">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label class="mb-0" style="font-size: 13px; font-weight: 500;">Other Charges</label>
                                    <button type="button" id="addOtherChargeBtn" class="btn btn-outline-secondary btn-sm" style="border-radius: 6px; width: 30px; height: 30px; display: inline-flex; align-items: center; justify-content: center; background: #fff; border-color: #d8d6de; box-shadow: 0 2px 4px rgba(0,0,0,0.05);" title="Add Other Charge">
                                        <i class="fas fa-plus" style="color: #ff9f43;"></i>
                                    </button>
                                </div>
                                <div id="otherChargesContainer" class="d-flex flex-column gap-2">
                                    <div class="other-charge-row d-flex align-items-center gap-2">
                                        <input type="text" class="form-control other-charge-name" placeholder="Charge Name">
                                        <input type="number" class="form-control other-charge-amount" placeholder="Amount" min="0" step="0.01">
                                        <button type="button" class="btn btn-outline-danger btn-sm removeOtherChargeBtn" style="border-radius: 6px; width: 38px; height: 38px; display: inline-flex; align-items: center; justify-content: center; background: #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.05);"><i class="fas fa-trash"></i></button>
                                    </div>
                                </div>
                                <input type="hidden" name="misc_charges" id="misc_charges" value="0">
                            </div>
                        </div>

                        <!-- Labour Charges Section -->
                        <div class="row mt-2" id="labour_charges_section" style="display: none;">
                            <div class="col-lg-12">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label class="mb-0" style="font-size: 13px; font-weight: 500;">Labour Charge</label>
                                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#labourItemModal" style="border-radius: 6px; font-size: 11px;">
                                        <i class="fas fa-plus text-primary me-1"></i> Add Labour
                                    </button>
                                </div>
                                <div id="labourChargesContainer" class="d-flex flex-column gap-1"></div>
                                <input type="hidden" name="labour_amount" id="labour_amount" value="0">
                            </div>
                        </div>

                        <!-- Overall Discount Section -->
                        <div class="row mt-2 overall-discount-container" id="overall_discount_section" style="display: none;">
                            <div class="col-lg-6 col-6">
                                <div class="select-group w-100">
                                    <label for="overall_discount_percentage" style="font-size: 12px; font-weight: 500;">Discount (%)</label>
                                    <input type="number" class="form-control" name="discount_percentage"
                                        id="overall_discount_percentage" placeholder="0" min="0" max="100" step="0.01">
                                </div>
                            </div>
                            <div class="col-lg-6 col-6">
                                <div class="select-group w-100">
                                    <label for="discount" style="font-size: 12px; font-weight: 500;">Discount Amount</label>
                                    <input type="number" class="form-control" name="discount"
                                        id="discount" placeholder="0.00" min="0" step="0.01">
                                </div>
                            </div>
                        </div>

                        <!-- Exchange Charges Section -->
                        <div class="mt-2" id="exchange_charges_wrapper" style="display: none;">
                            <div class="card p-2 mb-0" style="background: #fff8f0; border: 1px dashed #ff9f43; border-radius: 8px;">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <strong style="color: #ff9f43; font-size: 13px;"><i class="fas fa-recycle me-1"></i> Exchange Details</strong>
                                </div>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <input type="text" class="form-control form-control-sm" id="pos_exchange_material" placeholder="Item Name">
                                    </div>
                                    <div class="col-6">
                                        <input type="number" class="form-control form-control-sm" id="pos_exchange_amount" name="exchange_amount" placeholder="Value (₹)" min="0" step="0.01">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Hidden remarks textarea --}}
                        <textarea name="remark" id="remark" style="display:none;"></textarea>
                        <textarea name="remarks" id="remarks" style="display:none;"></textarea>
                        <textarea name="terms_condition" id="terms_condition" style="display:none;">{{ $setting->terms_condition_eng ?? '' }}</textarea>

                        <!-- Totals Summary List -->
                        <div class="setvalue">
                            <ul>
                                <li>
                                    <h5>Total (Product)</h5>
                                    <h6 style="color: green;">
                                        @if($currency_position === 'right')
                                        <span class="subtotal-value">0.00</span>{{ $currency_symbol }}
                                        @else
                                        {{ $currency_symbol }}<span class="subtotal-value">0.00</span>
                                        @endif
                                    </h6>
                                    <input type="hidden" name="subtotal" value="0.00" class="tax-hidden">
                                </li>

                                <li class="gst-summary-row" style="display: none;">
                                    <h5>Total GST Amount</h5>
                                    <h6 class="gst-total-amount">
                                        @if($currency_position === 'right')
                                        <span>0.00</span>{{ $currency_symbol }}
                                        @else
                                        {{ $currency_symbol }}<span>0.00</span>
                                        @endif
                                    </h6>
                                </li>

                                <li class="discount-summary-row" style="display: none;">
                                    <h5>Discount Amount</h5>
                                    <h6 class="discount-amount">
                                        @if($currency_position === 'right')
                                        <span>0.00</span>{{ $currency_symbol }}
                                        @else
                                        {{ $currency_symbol }}<span>0.00</span>
                                        @endif
                                    </h6>
                                    <input type="hidden" name="discount_amount" value="0.00" class="tax-hidden">
                                </li>

                                <li class="shipping-summary-row" style="display: none;">
                                    <h5>Delivery Cost</h5>
                                    <h6 class="shipping-cost-summary">
                                        @if($currency_position === 'right')
                                        <span>0.00</span>{{ $currency_symbol }}
                                        @else
                                        {{ $currency_symbol }}<span>0.00</span>
                                        @endif
                                    </h6>
                                </li>

                                <li class="other-charges-summary-row" style="display: none;">
                                    <h5>Other Charges</h5>
                                    <h6>
                                        <span id="other-charges-cost-display">0.00</span>
                                    </h6>
                                </li>

                                <li class="labour-summary-row" style="display: none;">
                                    <h5>Labour Charge</h5>
                                    <h6 class="labour-total-amount">
                                        @if($currency_position === 'right')
                                        <span>0.00</span>{{ $currency_symbol }}
                                        @else
                                        {{ $currency_symbol }}<span>0.00</span>
                                        @endif
                                    </h6>
                                </li>

                                <li class="tds-summary-row" style="display: none;">
                                    <h5>TDS (<span class="tds-percentage-summary">0.00</span>%)</h5>
                                    <h6 class="tds-amount-summary">
                                        @if($currency_position === 'right')
                                        <span>0.00</span>{{ $currency_symbol }}
                                        @else
                                        {{ $currency_symbol }}<span>0.00</span>
                                        @endif
                                    </h6>
                                </li>

                                <li class="exchange-summary-row" style="display: none;">
                                    <h5>Exchange Value</h5>
                                    <h6 class="exchange-amount-summary" style="color: #ff9f43;">
                                        @if($currency_position === 'right')
                                        - <span>0.00</span>{{ $currency_symbol }}
                                        @else
                                        - {{ $currency_symbol }}<span>0.00</span>
                                        @endif
                                    </h6>
                                </li>

                                @foreach($taxRates as $tax)
                                <li class="taxList" style="display: none;">
                                    <h5>{{ $tax->tax_name }} ({{ $tax->tax_rate }}%) Tax</h5>
                                    <h6 class="tax-value" data-rate="{{ $tax->tax_rate }}"
                                        data-symbol="{{ $currency_symbol }}" data-position="{{ $currency_position }}">
                                        @if($currency_position === 'right')
                                        <span>0.00</span>{{ $currency_symbol }}
                                        @else
                                        {{ $currency_symbol }}<span>0.00</span>
                                        @endif
                                    </h6>
                                </li>
                                @endforeach

                                <li class="total-value">
                                    <h5>Total</h5>
                                    <h6>
                                        @if($currency_position === 'right')
                                        <span class="total-amount">0.00</span>{{ $currency_symbol }}
                                        @else
                                        {{ $currency_symbol }}<span class="total-amount">0.00</span>
                                        @endif
                                    </h6>
                                    <input type="hidden" name="total" value="0.00" class="tax-hidden">
                                    <input type="hidden" name="grand_total" id="grand_total_form" value="0.00">
                                    <input type="hidden" name="price_after_discount" value="0.00" class="tax-hidden">
                                </li>

                                <li class="error-message-container">
                                    <span class="error_total" style="color:red; display:none;"></span>
                                </li>
                            </ul>
                        </div>

                        <!-- Modern Payment Panel (Collapsible with Handle) -->
                        <div class="payment_panel pos-pay-collapsed" id="paymentPanel">
                            <!-- Drag handle -->
                            <div id="paymentPanelHandle" class="payment-panel-handle" title="Show or hide payment options">
                                <span class="handle-pill"></span>
                                <span class="handle-hint">⬆ drag to reveal products ⬇</span>
                                <span class="pos-pay-title">Payment Method</span>
                                <span class="pos-pay-value-wrap">
                                    <span id="posPaySummary">Cash</span>
                                    <span class="handle-caret"><i class="fas fa-chevron-down"></i></span>
                                </span>
                                <button type="button" id="paymentPanelMinBtn" class="panel-min-btn d-none" title="Minimise / Restore">
                                    <svg id="panelMinIcon" xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                                        <path d="M7.646 4.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1-.708.708L8 5.707l-5.646 5.647a.5.5 0 0 1-.708-.708z" />
                                    </svg>
                                </button>
                            </div>

                            <div id="paymentPanelBody">
                                <div class="setvaluecash mt-1" id="paymentSection">
                                    <ul>
                                        <li id="payLaterOption">
                                            <a href="javascript:void(0);" class="paymentmethod">
                                                <input type="radio" name="payment_method" value="pending" hidden>
                                                <i class="fas fa-history" style="font-size: 16px; display: block; margin-bottom: 4px;"></i>
                                                Pay Later
                                            </a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="paymentmethod active">
                                                <input type="radio" name="payment_method" value="cash" hidden checked>
                                                <i class="fas fa-money-bill-wave" style="font-size: 16px; display: block; margin-bottom: 4px;"></i>
                                                Cash
                                            </a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="paymentmethod">
                                                <input type="radio" name="payment_method" value="debit card" hidden>
                                                <i class="fas fa-credit-card" style="font-size: 16px; display: block; margin-bottom: 4px;"></i>
                                                Debit
                                            </a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="paymentmethod">
                                                <input type="radio" name="payment_method" value="cash+online" hidden>
                                                <i class="fas fa-money-check-alt" style="font-size: 16px; display: block; margin-bottom: 4px;"></i>
                                                Cash+Online
                                            </a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="paymentmethod">
                                                <input type="radio" name="payment_method" value="scan" hidden>
                                                <i class="fas fa-qrcode" style="font-size: 16px; display: block; margin-bottom: 4px;"></i>
                                                Scan
                                            </a>
                                        </li>
                                        {{-- 
                                        <li id="emiPaymentOption">
                                            <a href="javascript:void(0);" class="paymentmethod">
                                                <input type="radio" name="payment_method" value="emi" hidden>
                                                <i class="fas fa-calendar-alt" style="font-size: 16px; display: block; margin-bottom: 4px;"></i>
                                                EMI
                                            </a>
                                        </li>
                                        --}}
                                    </ul>

                                    <input type="hidden" name="paid_by" id="paid_by" value="Cash">
                                    <input type="hidden" name="cash_type" id="cash_type" value="cash_Fully">
                                    <input type="hidden" name="online_type" id="online_type" value="Fully">
                                    <input type="hidden" name="cash_online_type" id="cash_online_type" value="cash_online_Fully">

                                    <!-- Compact Date & Warranty Date row -->
                                    <div class="row g-2 mt-2">
                                        <div class="col-md-6 col-6">
                                            <div class="form-group mb-1">
                                                <label for="payment_date" style="font-size: 12px; font-weight: 500;">Payment Date</label>
                                                <input type="date" name="payment_date" id="payment_date" class="form-control form-control-sm" value="{{ date('Y-m-d') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-6">
                                            <div class="form-group mb-1">
                                                <label for="warranty_date" style="font-size: 12px; font-weight: 500;">Warranty Date</label>
                                                <input type="date" name="warranty_date" id="warranty_date" class="form-control form-control-sm" value="{{ date('Y-m-d', strtotime('+3 years')) }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div id="paymentDetailActions" class="payment-detail-actions mt-2" style="display:none;">
                                        <button type="button" id="cancelPaymentDetails" class="payment-detail-cancel me-2">
                                            Cancel
                                        </button>
                                    </div>

                                    <!-- Paid Type (Fully / Partially) -->
                                    <div id="posPaidTypeBox" style="margin-top:10px;">
                                        <div class="form-group mb-2" id="posPaidTypeDropdownGroup">
                                            <label for="posPaidType" style="font-size: 12px; font-weight: 500;">Paid Type</label>
                                            <select id="posPaidType" class="form-control">
                                                <option value="fully" selected>Fully</option>
                                                <option value="partially">Partially</option>
                                            </select>
                                            <span class="error_paidtype text-danger"></span>
                                        </div>
                                        <div id="posPaidAmountFields" style="display:none;">
                                            <div class="row g-2">
                                                <div class="col-md-6 col-6">
                                                    <div class="form-group mb-2">
                                                        <label for="posPaidAmount" style="font-size: 12px; font-weight: 500;">Enter Amount</label>
                                                        <input type="number" class="form-control" id="posPaidAmount" name="partial_amount" placeholder="0.00" min="0" step="0.01">
                                                        <input type="hidden" name="cash_partial_amount" id="cash_partial_amount" value="">
                                                        <span class="error_paidamount text-danger"></span>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-6">
                                                    <div class="form-group">
                                                        <label for="posPendingAmount" style="font-size: 12px; font-weight: 500;">Pending Amount</label>
                                                        <input type="number" class="form-control" id="posPendingAmount" name="remaining_amount" placeholder="0.00" readonly>
                                                        <input type="hidden" id="cash_remaining_amount" value="0.00">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Cash + Online Fields -->
                                    <div id="cashOnlineBox" style="display:none; margin-top:10px;">
                                        <div class="row g-2">
                                            <div class="col-md-6 col-6">
                                                <div class="form-group">
                                                    <label style="font-size: 12px; font-weight: 500;">Cash Amount</label>
                                                    <input type="number" class="form-control" id="posCashOnlineCashAmount" name="cash_online_cash_amount" placeholder="Enter Cash Amount" min="0" step="0.01">
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-6">
                                                <div class="form-group">
                                                    <label style="font-size: 12px; font-weight: 500;">Online Amount</label>
                                                    <input type="number" class="form-control" id="posCashOnlineOnlineAmount" name="cash_online_upi_amount" placeholder="Enter Online Amount" value="0" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- 
                                    <!-- EMI Fields -->
                                    <div id="emiBox" style="display:none; margin-top:10px;">
                                        <div class="row g-2">
                                            <div class="col-md-6 col-6">
                                                <div class="form-group">
                                                    <label style="font-size: 12px; font-weight: 500;">Down Payment (Optional)</label>
                                                    <input type="number" class="form-control" id="emiDownPayment" placeholder="₹ Amount" min="0" step="0.01">
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-6">
                                                <div class="form-group">
                                                    <label style="font-size: 12px; font-weight: 500;">Loan Amount</label>
                                                    <input type="number" class="form-control" id="emiLoanAmount" name="emi_amount" placeholder="Auto Calculate" min="0" step="0.01" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-6">
                                                <div class="form-group">
                                                    <label style="font-size: 12px; font-weight: 500;">EMI Duration (Months)</label>
                                                    <select class="form-control" id="emiTenure" name="emi_duration">
                                                        <option value="">Select Tenure</option>
                                                        <option value="3">3 Months</option>
                                                        <option value="6">6 Months</option>
                                                        <option value="9">9 Months</option>
                                                        <option value="12">12 Months</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-6">
                                                <div class="form-group">
                                                    <label style="font-size: 12px; font-weight: 500;">Monthly Installment</label>
                                                    <input type="number" class="form-control" id="emiMonthlyAmount" name="calculated_emi" placeholder="Auto Calculate" min="0" step="0.01" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    --}}

                                    <span class="error_peymentmethod"></span>
                                </div>

                                <input type="hidden" id="bill_generate" name="bill_generate" value="bill_generate">
                                <div class="btn-totallabel bill-generate" tabindex="0" style="cursor: pointer; background: #1b2850; border-radius: 8px; padding: 12px 18px; display: flex; align-items: center; justify-content: space-between; color: #fff; margin-top: 14px;">
                                    <h6 style="margin: 0; color: #fff; font-size: 14px; font-weight: 600;">Total Amount : ₹0.00</h6>
                                    <h5 style="margin: 0; color: #fff; font-size: 14px; font-weight: 600; display: inline-flex; align-items: center; gap: 8px;">
                                        Generate Bill
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-arrow-right" viewBox="0 0 16 16" style="margin-bottom: 0.1rem;">
                                            <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8" />
                                        </svg>
                                    </h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Remarks & T&C Modal -->
    <div class="modal fade" id="remarksModal" tabindex="-1" aria-labelledby="remarksModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="remarksModalLabel">T&C and Remarks</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">x</button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="termsConditionModalTextarea" style="font-weight: 500; color: #5e5873;">
                            Terms & Conditions
                        </label>
                        <textarea id="termsConditionModalTextarea" class="form-control" rows="4" placeholder="Enter terms & conditions...">{{ $setting->terms_condition_eng ?? '' }}</textarea>
                    </div>
                    <div class="form-group">
                        <label for="remarksModalTextarea" style="font-weight: 500; color: #5e5873;">
                            Remarks <span class="text-muted" style="font-size:12px;">(Optional)</span>
                        </label>
                        <textarea id="remarksModalTextarea" class="form-control" rows="3" placeholder="Enter remarks..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn text-white" id="saveRemarksBtn" style="background-color: #ff9f43;">Save</button>
                    <button type="button" class="btn btn-secondary btn-cancel" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Labour Item Modal -->
    <div class="modal fade" id="labourItemModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="labourItemForm">
                    <div class="modal-header">
                        <h5 class="modal-title" id="labourItemModalLabel">Add Labour Item</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">x</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label>Labour Name <span class="text-danger">*</span></label>
                            <input type="text" name="item_name" id="labour_item_name" class="form-control" placeholder="Enter labour name" required>
                            <span class="text-danger" id="labour_item_name_error"></span>
                        </div>
                        <div class="form-group mb-3">
                            <label>Labour Price <span class="text-danger">*</span></label>
                            <input type="number" name="price" id="labour_item_price" class="form-control" placeholder="Enter price" step="0.01" min="0" required>
                            <span class="text-danger" id="labour_item_price_error"></span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Barcode Scanner Modal -->
    <div class="modal fade" id="barcodeScannerModal" tabindex="-1" aria-labelledby="barcodeScannerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="barcodeScannerModalLabel">Scan Barcode</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">x</button>
                </div>
                <div class="modal-body">
                    <div id="qr-reader" style="width:100%; min-height:300px;"></div>
                    <div id="scan-message" class="text-center mt-2 small text-muted">Initializing camera...</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn text-white" style="background: #1b2850; padding: 8px 24px;" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== QUICK ADD CUSTOMER MODAL ===== --}}
    <div class="modal fade" id="quickAddCustomerModal" tabindex="-1" aria-labelledby="quickAddCustomerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form id="quickAddCustomerForm" enctype="multipart/form-data">
                    <input type="hidden" id="qac_edit_id" value="">
                    <div class="modal-header">
                        <h5 class="modal-title" id="quickAddCustomerModalLabel">Add New Customer</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">x</button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-3 col-sm-6 col-6 mb-3">
                                <label>Customer Code</label>
                                <input type="text" name="qac_customer_code" id="qac_customer_code" class="form-control" maxlength="50" placeholder="Customer Code">
                                <div class="text-danger small qac-error" id="qac_error_customer_code"></div>
                            </div>
                            <div class="col-lg-3 col-sm-6 col-6 mb-3">
                                <label>Customer Name <span class="text-danger">*</span></label>
                                <input type="text" name="qac_customer_name" id="qac_customer_name" class="form-control" maxlength="80" placeholder="Customer Name">
                                <div class="text-danger small qac-error" id="qac_error_customer_name"></div>
                            </div>
                            <div class="col-lg-3 col-sm-6 col-6 mb-3">
                                <label>Company Name</label>
                                <input type="text" name="qac_company_name" id="qac_company_name" class="form-control" maxlength="80" placeholder="Company Name">
                                <div class="text-danger small qac-error" id="qac_error_company_name"></div>
                            </div>
                            <div class="col-lg-3 col-sm-6 col-6 mb-3">
                                <label>Phone <span class="text-danger">*</span></label>
                                <input type="text" name="qac_phone" id="qac_phone" class="form-control" maxlength="10" placeholder="10-digit phone">
                                <div class="text-danger small qac-error" id="qac_error_phone"></div>
                            </div>
                            <div class="col-lg-3 col-sm-6 col-6 mb-3">
                                <label>Alternate Phone</label>
                                <input type="text" name="qac_alternate_phone" id="qac_alternate_phone" class="form-control" maxlength="10" placeholder="10-digit phone (optional)">
                                <div class="text-danger small qac-error" id="qac_error_alternate_phone"></div>
                            </div>
                            <div class="col-lg-3 col-sm-6 col-6 mb-3">
                                <label>Email</label>
                                <input type="email" name="qac_email" id="qac_email" class="form-control" placeholder="Email">
                                <div class="text-danger small qac-error" id="qac_error_email"></div>
                            </div>
                            <div class="col-lg-3 col-sm-6 col-6 mb-3">
                                <label>GST Number</label>
                                <div style="position:relative;">
                                    <input type="text" name="qac_gst_number" id="qac_gst_number" class="form-control" maxlength="15" placeholder="GST Number (auto-fill)" autocomplete="off">
                                    <span id="qac-gst-loader" style="position:absolute;right:10px;top:50%;transform:translateY(-50%);display:none;color:#ff9f43;"><i class="fas fa-spinner fa-spin"></i></span>
                                </div>
                                <div class="text-danger small qac-error" id="qac_error_gst_number"></div>
                                <div id="qac-gst-msg" style="font-size:12px;margin-top:3px;min-height:16px;"></div>
                            </div>
                            <div class="col-lg-3 col-sm-6 col-6 mb-3">
                                <label>PAN Number</label>
                                <input type="text" name="qac_pan_number" id="qac_pan_number" class="form-control" maxlength="10" placeholder="PAN Number">
                                <div class="text-danger small qac-error" id="qac_error_pan_number"></div>
                            </div>
                            <div class="col-lg-3 col-sm-6 col-6 mb-3">
                                <label>State Code</label>
                                <input type="text" name="qac_state_code" id="qac_state_code" class="form-control" placeholder="e.g. 24">
                                <div class="text-danger small qac-error" id="qac_error_state_code"></div>
                            </div>
                            <div class="col-lg-3 col-sm-6 col-6 mb-3">
                                <label>State Name</label>
                                <input type="text" name="qac_state_name" id="qac_state_name" class="form-control" placeholder="Auto-filled from state code" readonly>
                            </div>
                            <div class="col-lg-3 col-sm-6 col-6 mb-3">
                                <label>Country</label>
                                <input type="text" name="qac_country" id="qac_country" class="form-control" placeholder="Country">
                            </div>
                            <div class="col-lg-3 col-sm-6 col-6 mb-3">
                                <label>City</label>
                                <input type="text" name="qac_city" id="qac_city" class="form-control" placeholder="City">
                            </div>
                            <div class="col-lg-6 col-sm-6 col-12 mb-3">
                                <label>Address</label>
                                <textarea name="qac_address" id="qac_address" class="form-control" rows="2" placeholder="Address"></textarea>
                            </div>
                            <div class="col-lg-6 col-sm-6 col-12 mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label class="mb-0">Delivery Address</label>
                                    <div class="form-check mb-0">
                                        <input class="form-check-input" type="checkbox" id="qac_use_same_address">
                                        <label class="form-check-label small mb-0" style="text-transform: none; font-weight: normal;" for="qac_use_same_address">Use Address</label>
                                    </div>
                                </div>
                                <textarea name="qac_delivery_address" id="qac_delivery_address" class="form-control" rows="2" placeholder="Delivery Address"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn text-white" id="qacSaveBtn" style="background-color:#ff9f43;">
                            <span class="spinner-border spinner-border-sm d-none" id="qacBtnSpinner" role="status" aria-hidden="true"></span>
                            <span id="qacSaveBtnText">Save Customer</span>
                        </button>
                        <button type="button" class="btn btn-secondary btn-cancel" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- ===== END QUICK ADD CUSTOMER MODAL ===== --}}

    @endsection
    @push('js')

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.4/html5-qrcode.min.js"></script>
    <script>
        // ==================== BARCODE SCANNER ====================
        window.selectedItems = window.selectedItems || new Map();
        var selectedItems = window.selectedItems;
        let html5QrCode = null;
        let isProcessingBarcode = false;

        function triggerCartUpdate() {
            if (typeof updateTotalItems === 'function') {
                updateTotalItems();
            } else {
                if (typeof renderSelectedItems === 'function') renderSelectedItems();
                if (typeof calculateTotals === 'function') calculateTotals();
                $(".totalitem h4").text("Total items : " + selectedItems.size);
            }
        }

        function playBeep() {
            try {
                const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                function beep(time, freq) {
                    const osc = audioCtx.createOscillator();
                    const gain = audioCtx.createGain();
                    osc.connect(gain);
                    gain.connect(audioCtx.destination);
                    osc.type = "square";
                    osc.frequency.setValueAtTime(freq, audioCtx.currentTime + time);
                    gain.gain.setValueAtTime(1, audioCtx.currentTime + time);
                    osc.start(audioCtx.currentTime + time);
                    osc.stop(audioCtx.currentTime + time + 0.2);
                }
                beep(0, 1200);
                beep(0.25, 1500);
            } catch (e) {
                console.log("Audio beep not supported or blocked");
            }
        }

        async function onScanSuccess(decodedText) {
            if (isProcessingBarcode) return;
            isProcessingBarcode = true;

            decodedText = (decodedText || '').trim();
            console.log("Scan success:", decodedText);

            playBeep();

            document.activeElement && document.activeElement.blur();
            $('#scan-message').text('Processing...').css('color', 'orange');

            try {
                const product = await fetchProductByBarcode(decodedText);
                if (product) {
                    try {
                        addProductToCart(product);
                        $('#scan-message').text('Added: ' + (product.name || decodedText)).css('color', 'green');
                    } catch (cartError) {
                        $('#scan-message').text('Error adding: ' + cartError.message).css('color', 'red');
                    }
                } else {
                    $('#scan-message').text('Product not found: ' + decodedText).css('color', 'red');
                }
            } catch (error) {
                console.error("Scan processing error:", error);
                $('#scan-message').text('Error fetching product').css('color', 'red');
            } finally {
                setTimeout(() => {
                    isProcessingBarcode = false;
                    $('#scan-message').text('Point camera at barcode').css('color', 'green');
                }, 1500);
            }
        }

        function onScanError(errorMessage) {
            // silent scan debug
        }

        function stopScanner() {
            if (!html5QrCode) return;
            if (html5QrCode.isScanning) {
                html5QrCode.stop()
                    .then(() => { html5QrCode = null; })
                    .catch(err => {
                        console.error("Stop error:", err);
                        html5QrCode = null;
                    });
            } else {
                html5QrCode = null;
            }
            $('#scan-message').text('');
        }

        function waitForLibrary(callback, retries = 20) {
            if (typeof Html5Qrcode !== "undefined") {
                callback();
            } else if (retries > 0) {
                setTimeout(() => waitForLibrary(callback, retries - 1), 200);
            } else {
                $('#scan-message').text('Scanner library failed to load. Please refresh.').css('color', 'red');
                showManualBarcodeInput();
            }
        }

        function startScanner() {
            waitForLibrary(function() {
                if (html5QrCode) {
                    let stopPromise = html5QrCode.isScanning ? html5QrCode.stop() : Promise.resolve();
                    stopPromise.then(() => {
                        html5QrCode = null;
                        initScanner();
                    }).catch(() => {
                        html5QrCode = null;
                        initScanner();
                    });
                } else {
                    initScanner();
                }
            });
        }

        function initScanner() {
            try {
                html5QrCode = new Html5Qrcode("qr-reader");
            } catch (e) {
                console.error("Failed to create Html5Qrcode:", e);
                showManualBarcodeInput();
                return;
            }

            const formats = (typeof Html5QrcodeSupportedFormats !== 'undefined') ? [
                Html5QrcodeSupportedFormats.CODE_128,
                Html5QrcodeSupportedFormats.CODE_39,
                Html5QrcodeSupportedFormats.EAN_13,
                Html5QrcodeSupportedFormats.EAN_8,
                Html5QrcodeSupportedFormats.UPC_A,
                Html5QrcodeSupportedFormats.UPC_E,
                Html5QrcodeSupportedFormats.QR_CODE
            ] : undefined;

            const config = {
                fps: 10,
                qrbox: { width: 250, height: 250 }
            };
            if (formats) {
                config.formatsToSupport = formats;
            }

            $('#scan-message').text('Starting camera...').css('color', '');

            html5QrCode.start(
                { facingMode: "environment" },
                config,
                onScanSuccess,
                onScanError
            ).then(() => {
                $('#scan-message').text('Point camera at barcode').css('color', 'green');
            }).catch(err => {
                console.warn("Rear camera failed, trying fallback...", err);
                Html5Qrcode.getCameras().then(devices => {
                    if (devices && devices.length) {
                        let cameraId = devices.find(device =>
                            device.label.toLowerCase().includes('back') ||
                            device.label.toLowerCase().includes('rear')
                        )?.id || devices[0].id;

                        return html5QrCode.start(cameraId, config, onScanSuccess, onScanError);
                    } else {
                        throw new Error("No camera found");
                    }
                }).then(() => {
                    $('#scan-message').text('Point camera at barcode').css('color', 'green');
                }).catch(fallbackErr => {
                    console.error("Camera fallback failed:", fallbackErr);
                    html5QrCode = null;
                    showManualBarcodeInput();
                });
            });
        }

        function showManualBarcodeInput() {
            $('#scan-message').text('').hide();
            $('#qr-reader').html(`
                <div style="text-align:center; padding: 30px 20px;">
                    <div style="font-size: 48px; margin-bottom: 10px;margin-top: 60px;"></div>
                    <p style="color:#666; font-size:14px; margin-bottom:16px;">
                        Scanner stays open. Scan more products, then click Close.<br>
                        Camera access denied or unsupported.<br>
                        Enter barcode manually below:
                    </p>
                    <div style="display:flex; gap:8px; justify-content:center;">
                        <input type="text" id="manualBarcodeInput" class="form-control"
                            placeholder="Enter barcode / product code"
                            style="max-width:260px; font-size:14px;">
                        <button type="button" class="btn text-white" id="manualBarcodeSubmit" style="background-color: #ff9f43; border-color: #ff9f43;">Search</button>
                    </div>
                    <div id="manualBarcodeError" class="text-danger" style="font-size:14px; margin-top:8px;"></div>
                </div>
            `);

            setTimeout(() => { $('#manualBarcodeInput').focus(); }, 150);

            $('#qr-reader').off('click', '#manualBarcodeSubmit').on('click', '#manualBarcodeSubmit', function() {
                handleManualBarcode();
            });

            $('#qr-reader').off('keydown', '#manualBarcodeInput').on('keydown', '#manualBarcodeInput', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    handleManualBarcode();
                }
            });
        }

        async function handleManualBarcode() {
            let barcode = $('#manualBarcodeInput').val().trim();

            if (!barcode) {
                $('#manualBarcodeError').text('Please enter a barcode.');
                return;
            }

            $('#manualBarcodeSubmit').prop('disabled', true).text('Searching...');
            $('#manualBarcodeError').text('').removeClass('text-success').addClass('text-danger');

            try {
                const product = await fetchProductByBarcode(barcode);

                document.activeElement && document.activeElement.blur();
                
                if (product) {
                    try {
                        addProductToCart(product);
                        playBeep();
                        $('#manualBarcodeInput').val('').focus();
                        $('#manualBarcodeError').removeClass('text-danger').addClass('text-success').text('Added: ' + product.name);
                        setTimeout(() => { $('#manualBarcodeError').text(''); }, 2500);
                    } catch (cartError) {
                        console.error('Error in addProductToCart:', cartError);
                        $('#manualBarcodeError').removeClass('text-success').addClass('text-danger').text('Error adding product: ' + cartError.message);
                        $('#manualBarcodeInput').focus();
                    }
                } else {
                    $('#manualBarcodeError').removeClass('text-success').addClass('text-danger').text('Product not found: ' + barcode);
                    $('#manualBarcodeInput').focus();
                }
            } catch (error) {
                console.error('Error fetching barcode:', error);
                $('#manualBarcodeError').removeClass('text-success').addClass('text-danger').text('Fetch error: ' + (error.message || error));
                $('#manualBarcodeInput').focus();
            } finally {
                $('#manualBarcodeSubmit').prop('disabled', false).text('Search');
            }
        }

        function fetchProductByBarcode(barcode) {
            return new Promise((resolve, reject) => {
                let authToken = localStorage.getItem("authToken");
                $.ajax({
                    url: '/api/product-by-barcode/' + encodeURIComponent(barcode),
                    type: 'GET',
                    headers: {
                        "Authorization": "Bearer " + authToken,
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        resolve(response.status && response.product ? response.product : null);
                    },
                    error: function(xhr) {
                        if (xhr.status === 404) {
                            resolve(null);
                        } else {
                            reject(xhr.responseJSON?.message || 'Network error');
                        }
                    }
                });
            });
        }

        function addProductToCart(product) {
            if (!product) return;
            const rawId = String(product.id || '').trim();
            const numericId = parseInt(rawId, 10) || 0;
            const barcodeVal = (product.barcode || '').trim();
            const stringId = String(numericId);

            const productName = product.name || 'Unknown';
            const rawPrice = parseFloat(product.price) || 0;

            let basePath = '{{ rtrim(env('ImagePath'), '/') }}'.replace(/\/+$/, '');
            let productImage = `${basePath}/admin/assets/img/product/noimage.png`;
            if (product.image) {
                try {
                    let cleanImage = String(product.image).replace(/\\/g, '').replace(/^"(.*)"$/, '$1');
                    let rawImg = '';
                    if (cleanImage.startsWith('[')) {
                        let imagesArray = JSON.parse(cleanImage);
                        if (Array.isArray(imagesArray) && imagesArray.length > 0) {
                            rawImg = imagesArray[0];
                        }
                    } else {
                        rawImg = cleanImage;
                    }
                    if (rawImg) {
                        let cleanImg = String(rawImg).replace(/^\/+/, '').replace(/^storage\/+/, '');
                        productImage = (cleanImg.startsWith('http://') || cleanImg.startsWith('https://'))
                            ? cleanImg
                            : `${basePath}/storage/${cleanImg}`;
                    }
                } catch (e) {
                    console.warn('Image parse error, using default');
                }
            }

            const stock = parseInt(product.quantity || product.stock) || 100;
            const categoryId = product.category_id || 0;

            if (selectedItems.has(stringId)) {
                let item = selectedItems.get(stringId);
                let nextQty = (parseInt(item.quantity) || 1) + 1;
                if (nextQty <= item.stock) {
                    item.quantity = nextQty;
                    selectedItems.set(stringId, item);
                    triggerCartUpdate();
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Stock Limit Reached',
                        text: 'Cannot exceed available stock (' + item.stock + ')',
                        confirmButtonColor: '#ff9f43',
                        confirmButtonText: 'OK'
                    });
                }
            } else {
                selectedItems.set(stringId, {
                    id: stringId,
                    productId: numericId,
                    categoryId: categoryId,
                    name: productName,
                    price: rawPrice.toFixed(2),
                    originalPrice: rawPrice.toFixed(2),
                    image: productImage,
                    code: barcodeVal || "PT001",
                    quantity: 1,
                    stock: stock
                });
                triggerCartUpdate();
            }
        }

        // Modal triggers for Barcode Scanner
        $(document).on('click', '#scanBarcodeBtn', function(e) {
            e.preventDefault();
            const modalEl = document.getElementById('barcodeScannerModal');
            if (modalEl) {
                if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    bootstrap.Modal.getOrCreateInstance(modalEl).show();
                } else {
                    $('#barcodeScannerModal').appendTo("body").modal('show');
                }
            }
        });

        $(document).on('shown.bs.modal', '#barcodeScannerModal', function() {
            setTimeout(startScanner, 300);
        });

        $(document).on('hidden.bs.modal', '#barcodeScannerModal', function() {
            stopScanner();
            $('#qr-reader').html('').css('min-height', '300px');
            $('#scan-message').text('Initializing camera...').show().css('color', '');
        });
    </script>
    <script>
        // let selectedItems = new Map();
        document.querySelectorAll('.paymentmethod').forEach(el => {
            el.addEventListener('click', function() {
                document.querySelectorAll('input[name="payment_method"]').forEach(radio => radio.checked = false);
                this.querySelector('input[type="radio"]').checked = true;
                document.querySelectorAll('.paymentmethod').forEach(item => item.classList.remove('active'));
                this.classList.add('active');
            });
        });

        let groupPrices = [];
        let selectedItems = new Map();



        // Fetch group prices when customer/vendor changes
        $(document).on('change', '#customer_name, #vendor_name', function() {
            let authToken = localStorage.getItem("authToken");
            let selectedOption = $(this).find(":selected");
            let name = selectedOption.data('group');
            let userType = $("#user_type").val() || "";

            if (name) {
                $.ajax({
                    url: '/api/getproductpricebygroup',
                    type: 'GET',
                    data: {
                        name: name,
                        userType: userType
                    },
                    headers: {
                        "Authorization": "Bearer " + authToken,
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.status) {
                            groupPrices = response.data; // save group prices

                            // Update selected items
                            selectedItems.forEach((item, key) => {
                                const groupProduct = groupPrices.find(p => p.product_id == String(item.productId));
                                // Use group price if available, otherwise keep original price
                                item.price = groupProduct ? parseFloat(groupProduct.price) : parseFloat(item.price || 0);
                            });

                            // Recalculate totals and update UI
                            calculateTotals();
                            renderSelectedItems();
                        }
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                    }
                });
            } else {
                // If no customer selected, fallback: use each item's original price
                selectedItems.forEach((item, key) => {
                    item.price = parseFloat(item.originalPrice || 0);
                });

                // Recalculate totals and update UI
                calculateTotals();
                renderSelectedItems();
            }
        });


        function addOrUpdateItem(item) {
            // Find latest price from groupPrices
            let product = groupPrices.find(p => p.product_id == item.product_id);
            if (product) {
                item.price = parseFloat(product.price); // ensure numeric
            }

            selectedItems.set(item.product_id, item);
            updateTotalItems(); // recalc totals
            renderSelectedItems(); // update UI3
            printSelectedItems();
        }

        function printSelectedItems() {
            const arr = Array.from(selectedItems, ([id, item]) => ({
                productId: id,
                ...item
            }));
            console.log("Selected Items:", arr);
        }



        function refreshPosPaymentUi() {
            let activeRadio = $("input[name='payment_method']:checked");
            let method = activeRadio.val() || 'cash';
            let grandTotal = parseFloat($("input[name='total']").val()) || 0;

            // Update header summary text
            let summaryLabel = 'Cash';
            if (method === 'pending') summaryLabel = 'Pay Later';
            else if (method === 'debit card') summaryLabel = 'Debit';
            else if (method === 'cash+online') summaryLabel = 'Cash+Online';
            else if (method === 'scan') summaryLabel = 'Scan';
            else if (method === 'emi') summaryLabel = 'EMI';
            $("#posPaySummary").text(summaryLabel);

            // Sync hidden paid_by
            let paidByMap = {
                'pending': 'Pending',
                'cash': 'Cash',
                'debit card': 'Online',
                'cash+online': 'Cash_online',
                'scan': 'Online',
                'emi': 'EMI'
            };
            $("#paid_by").val(paidByMap[method] || 'Cash');

            if (method === 'pending') {
                $("#paymentDetailActions").hide();
                $("#posPaidTypeBox, #cashOnlineBox, #emiBox").hide();
            } else if (method === 'emi') {
                $("#paymentDetailActions").show();
                $("#posPaidTypeBox, #cashOnlineBox").hide();
                $("#emiBox").show();

                let downPayment = parseFloat($("#emiDownPayment").val()) || 0;
                let loanAmount = Math.max(0, grandTotal - downPayment);
                $("#emiLoanAmount").val(loanAmount.toFixed(2));
                let tenure = parseInt($("#emiTenure").val()) || 0;
                let monthlyEmi = tenure > 0 ? (loanAmount / tenure) : 0;
                $("#emiMonthlyAmount").val(monthlyEmi.toFixed(2));
            } else if (method === 'cash+online') {
                $("#paymentDetailActions").show();
                $("#posPaidTypeBox, #emiBox").hide();
                $("#cashOnlineBox").show();

                let cashPart = parseFloat($("#posCashOnlineCashAmount").val()) || 0;
                let onlinePart = Math.max(0, grandTotal - cashPart);
                $("#posCashOnlineOnlineAmount").val(onlinePart.toFixed(2));
            } else {
                // Cash, Debit, Scan
                $("#paymentDetailActions").show();
                $("#cashOnlineBox, #emiBox").hide();
                $("#posPaidTypeBox").show();

                let paidType = $("#posPaidType").val();
                if (paidType === 'partially') {
                    $("#posPaidAmountFields").show();
                    let paid = parseFloat($("#posPaidAmount").val()) || 0;
                    let pending = Math.max(0, grandTotal - paid);
                    $("#posPendingAmount").val(pending.toFixed(2));
                    $("#cash_partial_amount").val(paid.toFixed(2));
                    $("#cash_remaining_amount").val(pending.toFixed(2));
                } else {
                    $("#posPaidAmountFields").hide();
                    $("#posPaidAmount").val(grandTotal.toFixed(2));
                    $("#posPendingAmount").val("0.00");
                    $("#cash_partial_amount").val(grandTotal.toFixed(2));
                    $("#cash_remaining_amount").val("0.00");
                }
            }
        }

        function calculateTotals() {
            const firstTaxElement = $(".tax-value").first();
            const currencySymbol = firstTaxElement.data("symbol") || '{{ $currency_symbol }}';
            const currencyPosition = firstTaxElement.data("position") || '{{ $currency_position }}';

            let subtotal = 0;

            selectedItems.forEach(item => {
                let groupProduct = groupPrices.find(gp => gp.product_id == String(item.productId) && gp.id == item.id);
                let price = groupProduct ? parseFloat(groupProduct.price) : parseFloat(item.price || 0);
                let quantity = parseInt(item.quantity) || 0;
                subtotal += price * quantity;
            });

            $(".subtotal-value").html(formatCurrency(subtotal.toFixed(2), currencySymbol, currencyPosition));
            $("input[name='subtotal']").val(subtotal.toFixed(2));

            let totalTax = 0;
            let gstOption = $("input[name='gst_option']:checked").val();

            // Overall Discount calculation
            let discountAmount = 0;
            let discountPerc = parseFloat($("#overall_discount_percentage").val()) || 0;
            let discountFixed = parseFloat($("#discount").val()) || 0;
            if (discountPerc > 0 && subtotal > 0) {
                discountAmount = subtotal * (discountPerc / 100);
            } else if (discountFixed > 0) {
                discountAmount = discountFixed;
            }
            if (discountAmount > subtotal) discountAmount = subtotal;

            let priceAfterDiscount = Math.max(0, subtotal - discountAmount);

            // Taxes calculation
            if (gstOption === "with" && priceAfterDiscount > 0) {
                $(".taxList").each(function() {
                    const taxRate = parseFloat($(this).find(".tax-value").data("rate")) / 100;
                    const taxAmount = priceAfterDiscount * taxRate;
                    const taxEl = $(this).find(".tax-value");
                    const symbol = taxEl.data("symbol") || currencySymbol;
                    const position = taxEl.data("position") || currencyPosition;
                    taxEl.html(formatCurrency(taxAmount.toFixed(2), symbol, position));
                    $(this).show();
                    totalTax += taxAmount;
                });
            } else {
                $(".taxList").hide().find(".tax-value").html(formatCurrency("0.00", currencySymbol, currencyPosition));
                totalTax = 0;
            }

            // Delivery cost
            let shipping = parseFloat($("#shipping").val()) || 0;

            // Other charges
            let otherChargesTotal = 0;
            $(".other-charge-amount").each(function() {
                otherChargesTotal += parseFloat($(this).val()) || 0;
            });
            $("#misc_charges").val(otherChargesTotal.toFixed(2));

            // Labour charge
            let labourTotal = parseFloat($("#labour_amount").val()) || 0;

            // TDS
            let tdsPercentage = parseFloat($("#tds_percentage").val()) || 0;
            let tdsAmount = 0;
            if (tdsPercentage > 0 && priceAfterDiscount > 0) {
                tdsAmount = priceAfterDiscount * (tdsPercentage / 100);
                $("#tds_amount").val(tdsAmount.toFixed(2));
            } else {
                tdsAmount = parseFloat($("#tds_amount").val()) || 0;
            }

            // Exchange
            let exchangeAmount = parseFloat($("#pos_exchange_amount").val()) || 0;

            // Grand Total
            let total = 0;
            if (subtotal > 0 || shipping > 0 || otherChargesTotal > 0 || labourTotal > 0) {
                total = priceAfterDiscount + totalTax + shipping + otherChargesTotal + labourTotal - tdsAmount - exchangeAmount;
                if (total < 0) total = 0;
            }

            // Update UI Summary rows
            if (totalTax > 0) {
                $(".gst-summary-row").show();
                $(".gst-total-amount").html(formatCurrency(totalTax.toFixed(2), currencySymbol, currencyPosition));
            } else {
                $(".gst-summary-row").hide();
            }

            if (discountAmount > 0) {
                $(".discount-summary-row").show();
                $(".discount-amount").html(formatCurrency(discountAmount.toFixed(2), currencySymbol, currencyPosition));
                $("input[name='discount_amount']").val(discountAmount.toFixed(2));
            } else {
                $(".discount-summary-row").hide();
                $("input[name='discount_amount']").val("0.00");
            }

            if (shipping > 0) {
                $(".shipping-summary-row").show();
                $(".shipping-cost-summary").html(formatCurrency(shipping.toFixed(2), currencySymbol, currencyPosition));
            } else {
                $(".shipping-summary-row").hide();
            }

            if (otherChargesTotal > 0) {
                $(".other-charges-summary-row").show();
                $("#other-charges-cost-display").html(formatCurrency(otherChargesTotal.toFixed(2), currencySymbol, currencyPosition));
            } else {
                $(".other-charges-summary-row").hide();
            }

            if (labourTotal > 0) {
                $(".labour-summary-row").show();
                $(".labour-total-amount").html(formatCurrency(labourTotal.toFixed(2), currencySymbol, currencyPosition));
            } else {
                $(".labour-summary-row").hide();
            }

            if (tdsAmount > 0) {
                $(".tds-summary-row").show();
                $(".tds-percentage-summary").text(tdsPercentage.toFixed(2));
                $(".tds-amount-summary").html(formatCurrency(tdsAmount.toFixed(2), currencySymbol, currencyPosition));
            } else {
                $(".tds-summary-row").hide();
            }

            if (exchangeAmount > 0) {
                $(".exchange-summary-row").show();
                $(".exchange-amount-summary span").text(exchangeAmount.toFixed(2));
            } else {
                $(".exchange-summary-row").hide();
            }

            $(".total-value h6").html(formatCurrency(total.toFixed(2), currencySymbol, currencyPosition));
            $(".btn-totallabel h6").html('Total Amount : ' + formatCurrency(total.toFixed(2), currencySymbol, currencyPosition));
            $("input[name='total']").val(total.toFixed(2));
            $("input[name='grand_total']").val(total.toFixed(2));
            $("input[name='price_after_discount']").val(priceAfterDiscount.toFixed(2));

            refreshPosPaymentUi();
        }
        function renderSelectedItems() {
            let $productTable = $(".product-table");

            let productHtml = "";


            selectedItems.forEach(item => {
                productHtml += `
                                <ul class="product-lists">
                                    <li>
                                        <div class="productimg">
                                            <div class="productimgs">
                                                <img src="${item.image}" alt="${item.name}">
                                            </div>
                                            <div class="productcontet">
                                                <h4 style="text-transform: capitalize;">${item.name}
                                                    <a href="javascript:void(0);" class="ms-2 edit-item" data-id="${item.id}">
                                                        <img src="{{ env('ImagePath') . '/admin/assets/img/icons/edit-5.svg' }}" alt="edit">
                                                    </a>
                                                </h4>
                                                <div class="increment-decrement">
                                                    <div class="input-groups">
                                                        <input type="button" value="-" class="button-minus dec button" data-id="${item.id}">
                                                        <input type="text" name="quantity" value="${item.quantity}" class="quantity-field" data-id="${item.id}" data-stock="${item.stock}">
                                                        <input type="button" value="+" class="button-plus inc button" data-id="${item.id}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="price">${item.price}</li>
                                    <li>
                                        <a class="confirm-text remove-item" href="javascript:void(0);" data-id="${item.id}">
                                            <img src="{{ env('ImagePath') . '/admin/assets/img/icons/delete-2.svg' }}" alt="delete">
                                        </a>
                                    </li>
                                </ul>`;
            });

            $productTable.html(productHtml);
        }

        function formatCurrency(amount, symbol, position) {
            return position === 'right' ? amount + symbol : symbol + amount;
        }

        $(document).ready(function() {
            selectedItems = new Map();
            var authToken = localStorage.getItem("authToken");
            const selectedSubAdminId = localStorage.getItem("selectedSubAdminId");

            @if(auth()->user() && auth()->user()->role === 'admin')
                const currentSessionSubAdminId = "{{ session('selectedSubAdminId') }}";
                if (selectedSubAdminId && selectedSubAdminId !== "1" && selectedSubAdminId !== currentSessionSubAdminId) {
                    $.post('/set-subadmin-session', {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        subAdminId: selectedSubAdminId
                    }, function() {
                        window.location.reload();
                    });
                    return;
                }
            @endif
            // Initialize tax rows as hidden on page load
            $(".taxList").hide();

            function formatCurrency(amount, symbol, position) {
                return position === 'right' ? amount + symbol : symbol + amount;
            }

            $(document).on("change", "#discount_group", function() {
                calculateTotals();
            });

            $(document).on("change", "input[name='gst_option']", function() {
                calculateTotals();
            });


            function updateItemPrice(productId) {
                // Find the product in your groupPrices
                let product = groupPrices.find(p => p.product_id == productId);

                console.log("Updating price for product ID:", productId, "Found:", product);


                if (product) {
                    // Update the price in selectedItems
                    let item = selectedItems.find(i => i.product_id == productId);
                    if (item) {
                        item.price = product.price; // set correct price
                    }
                }

                calculateTotals(); // recalc totals
            }









            window.updateTotalItems = function() {
                $(".totalitem h4").text("Total items : " + selectedItems.size);
                renderSelectedItems();
                calculateTotals();
            }



            $(document).on("click", ".productset", function() {
                var $product = $(this);
                var productId = $product.data("id");
                var productName = $product.find("h4").text().trim();
                var productPrice = $product.find(".productsetcontent h6").text().trim();
                var categoryId = $product.find(".productsetcontent h1").text().trim();
                var productImage = $product.find("img").attr("src");
                var productCode = "PT001";
                var $checkIcon = $product.find(".check-product i");
                var productStock = parseInt($product.data("stock")) || 0;

                // Override price if group price exists
                let groupProduct = groupPrices.find(p => p.product_id == String(productId));

                // console.log("Clicked productId:", productId);
                // console.log("Searching in groupPrices:", groupPrices);
                // console.log("Matched groupProduct:", groupProduct);



                if (groupProduct) {
                    productPrice = parseFloat(groupProduct.price);
                } else {
                    productPrice = parseFloat(productPrice.replace(/[^0-9.]/g, '')) || 0; // remove any symbols and parse
                }

                var uniqueId = productId;
                selectedItems.set(uniqueId, {
                    id: uniqueId,
                    productId: productId,
                    categoryId: categoryId,
                    name: productName,
                    price: productPrice,
                    originalPrice: productPrice,
                    image: productImage,
                    code: productCode,
                    quantity: 1,
                    stock: productStock
                });

                $checkIcon.addClass("selected");
                updateTotalItems();
            });


            function updateTotalItems() {
                $(".totalitem h4").text("Total items : " + selectedItems.size);
                renderSelectedItems();
                calculateTotals();
            }
            window.updateTotalItems = updateTotalItems;

            // Format currency helper
            function formatCurrency(amount, symbol, position) {
                return position === 'right' ? amount + symbol : symbol + amount;
            }

            $(document).on("click", ".button-plus", function() {
                var itemId = $(this).data("id");
                if (selectedItems.has(itemId)) {
                    var item = selectedItems.get(itemId);
                    if (item.quantity < item.stock) {
                        item.quantity++;
                        selectedItems.set(itemId, item);
                        updateTotalItems();
                    } else {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Stock Limit Reached',
                            text: 'Cannot exceed available stock (' + item.stock + ')',
                            confirmButtonColor: '#ff9f43',
                            confirmButtonText: 'OK'
                        });
                    }
                }
            });

            $(document).on("change", ".quantity-field", function() {
                var itemId = $(this).data("id");
                var newQty = parseInt($(this).val());

                if (selectedItems.has(itemId)) {
                    var item = selectedItems.get(itemId);

                    if (newQty > item.stock) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Stock Quantity Exceeded',
                            text: 'Only ' + item.stock + ' quantity are available.',
                            confirmButtonColor: '#3085d6',
                            confirmButtonText: 'OK'
                        });
                        newQty = item.stock;
                    } else if (newQty < 1 || isNaN(newQty)) {
                        newQty = 1;
                    }

                    item.quantity = newQty;
                    selectedItems.set(itemId, item);
                    updateTotalItems();
                    $(this).val(newQty); // update input box with valid value
                }
            });

            $(document).on("click", ".button-minus", function() {
                var itemId = $(this).data("id");
                if (selectedItems.has(itemId) && selectedItems.get(itemId).quantity > 1) {
                    selectedItems.get(itemId).quantity--;
                    updateTotalItems();
                }
            });

            $(document).on("click", ".remove-item", function() {
                var itemId = $(this).data("id");
                var item = selectedItems.get(itemId);
                if (item) {
                    $(".productset[data-id='" + item.productId + "'] .check-product i").removeClass("selected");
                    selectedItems.delete(itemId);
                    updateTotalItems();
                }
            });

            $(".clear_items").click(function() {
                selectedItems.clear();
                $(".check-product i").removeClass("selected");
                updateTotalItems();
            });

            $("#discount").on("input", function() {
                const discountValue = parseFloat($(this).val());

                if ($(this).val() === "") {
                    // If empty, treat as 0 and remove error
                    $(".error_discount").text("");
                    $("#discount").val(""); // keep it blank for UX
                    calculateTotals(); // recalculate with 0 discount
                    return;
                }

                if (discountValue < 0) {
                    $(".error_discount").text("Discount must be greater 0").css("color", "red");
                    $(this).val("");
                } else if (isNaN(discountValue)) {
                    $(".error_discount").text("");
                } else {
                    $(".error_discount").text("Discount Applied").css("color", "green");
                    calculateTotals();
                }
            });

            $(".btn-totallabel").click(function(event) {

                event.preventDefault();
                var $btn = $(this); // cache the button
                var originalText = $btn.html();


                // Reset errors
                $(".error_customername, .error_customerphone, .error_peymentmethod, .error_discount").text("");
                $(".total-value .error_total").remove();


                let userType = $("#user_type").val() || "";

                if (userType === "customer") {
                    let customerId = $("#customer_name").val();
                    let customerName = $("#customer_name option:selected").text().trim();
                    phone = $("#customer_phone").val().trim();

                    // send id if exists, else name
                    name_or_id = customerId ? customerId : customerName;


                } else if (userType === "vendor") {
                    let vendorId = $("#vendor_id").val();
                    let vendorName = $("#vendor_id option:selected").text().trim();
                    phone = $("#vendor_phone").val().trim();

                    // send id if exists, else name
                    name_or_id = vendorId ? vendorId : vendorName;
                }

                let selectedPayment = $("input[name='payment_method']:checked").val();
                let subtotal = parseFloat($("input[name='subtotal']").val()) || 0;
                let total = parseFloat($("input[name='total']").val()) || 0;
                let discount = parseFloat($("#discount").val()) || 0;
                let grandTotal = parseFloat($("#grand_total_form").val()) || 0;
                let paidBy = $("#paid_by").val() || "";
                let cashType = $("#cash_type").val() || "";
                let onlineType = $("#online_type").val() || "";
                let cashPartial = $("#cash_partial_amount").val() || "";
                let partialAmount = $("#partial_amount").val() || "";
                let cashOnlineType = $("#cash_online_type").val() || "";
                let cashOnlineCash = $("#cash_online_cash_amount").val() || "";
                let cashOnlinePartialCash = $("#cash_online_partial_cash").val() || "";
                let cashOnlinePartialUpi = $("#cash_online_partial_upi").val() || "";
                let emiDuration = $("#emi_duration").val() || "";
                let remark = $("#remark").val() || "";
                let discount_group = $("#discount_group").val() || "";
                let gstOption = $('input[name="gst_option"]:checked').val() || "without";


                let isValid = true;

                // Reset errors
                $(
                    "#paid_by, #cash_type, #cash_partial_amount, #online_type, #partial_amount, #cash_online_type, #cash_online_cash_amount, #cash_online_partial_cash, #cash_online_partial_upi"
                ).removeClass("is-invalid");
                $(
                    ".paid_by_error, .cash_type_error, .cash_partial_error, .online_type_error, .partial_amount_error, .cash_online_type_error, .cash_online_cash_error, .cash_online_partial_cash_error, .cash_online_partial_upi_error"
                ).remove();

                $("#emi_duration").removeClass("is-invalid");
                $(".emi_duration_error").remove();

                $(".error_customername").text('');
                $(".error_customerphone").text('');


                // Reset previous validation
                $("#customer_name, #vendor_id, #customer_phone, #vendor_phone, #user_type").removeClass("is-invalid");
                $(".error_customername, .error-vendor_id, .error_customerphone, .error-vendorphone, .error-usertype").text("");

                if (userType === "customer") {
                    // Get values
                    name = $("#customer_name option:selected").text().trim();
                    phone = $("#customer_phone").val().trim();

                    // Validate Customer Name
                    if (!$("#customer_name").val()) {
                        isValid = false;
                        $("#customer_name").addClass("is-invalid");
                        $(".error_customername").text("Please select a customer");
                    }

                    // Validate Customer Phone
                    if (!phone) {
                        isValid = false;
                        $("#customer_phone").addClass("is-invalid");
                        $(".error_customerphone").text("Please enter customer phone");
                    } else if (!/^[0-9]{10}$/.test(phone)) {
                        isValid = false;
                        $("#customer_phone").addClass("is-invalid");
                        $(".error_customerphone").text("Enter a valid 10-digit phone number");
                    }

                } else if (userType === "vendor") {
                    // Get values
                    name = $("#vendor_id option:selected").text().trim();
                    phone = $("#vendor_phone").val().trim();

                    // Validate Vendor Name
                    if (!$("#vendor_id").val()) {
                        isValid = false;
                        $("#vendor_id").addClass("is-invalid");
                        $(".error-vendor_id").text("Please select a vendor");
                    }

                    // Validate Vendor Phone
                    if (!phone) {
                        isValid = false;
                        $("#vendor_phone").addClass("is-invalid");
                        $(".error-vendorphone").text("Please enter vendor phone");
                    } else if (!/^[0-9]{10}$/.test(phone)) {
                        isValid = false;
                        $("#vendor_phone").addClass("is-invalid");
                        $(".error-vendorphone").text("Enter a valid 10-digit phone number");
                    }

                } else {
                    isValid = false;
                    $("#user_type").addClass("is-invalid");
                    $(".error-usertype").text("Please select a user type (Customer or Vendor)");
                }



                $(".error_product").text('');

                // Check if at least one product exists
                let totalProducts = $(".product-table").children().length;
                if (totalProducts === 0) {
                    isValid = false;
                    $(".error_product").text("Please select at least one product.");
                }

                // ✅ Validate Paid By
                if (!paidBy) {
                    isValid = false;
                    $("#paid_by").focus().addClass("is-invalid")
                        .after('<span class="text-danger paid_by_error">Please select method</span>');
                }

                // ✅ Validate Cash
                if (paidBy === "Cash") {
                    if (cashType === "cash_fully") {
                        if (parseFloat(cashPartial) !== grandTotal) {
                            isValid = false;
                            $("#cash_partial_amount")
                                .addClass("is-invalid")
                                .closest(".form-group")
                                .append('<span class="text-danger cash_partial_error">Amount must equal total</span>');
                            $("#cash_partial_amount").focus();
                        }
                    } else if (cashType === "cash_partially") {
                        if (!cashPartial || cashPartial.trim() === "" || parseFloat(cashPartial) <= 0 || parseFloat(cashPartial) > grandTotal) {
                            isValid = false;
                            $("#cash_partial_amount")
                                .addClass("is-invalid")
                                .closest(".form-group")
                                .append('<span class="text-danger cash_partial_error">Please enter a valid partial amount</span>');
                            $("#cash_partial_amount").focus();
                        }
                    }
                }


                // ✅ Validate Online
                // if (paidBy === "Online") {
                //     if (!onlineType) {
                //         isValid = false;
                //         $("#online_type")
                //             .addClass("is-invalid")
                //             .closest(".form-group")
                //             .append('<span class="text-danger online_type_error">Select type</span>');
                //         $("#online_type").focus();
                //     } else if (onlineType === "Fully") {
                //         if (parseFloat(partialAmount) !== grandTotal) {
                //             isValid = false;
                //             $("#partial_amount")
                //                 .addClass("is-invalid")
                //                 .closest(".form-group")
                //                 .append('<span class="text-danger partial_amount_error">Amount must equal total</span>');
                //             $("#partial_amount").focus();
                //         }
                //     } else if (onlineType === "Partially") {
                //         if (!partialAmount || parseFloat(partialAmount) <= 0 || parseFloat(partialAmount) > grandTotal) {
                //             isValid = false;
                //             $("#partial_amount")
                //                 .addClass("is-invalid")
                //                 .closest(".form-group")
                //                 .append('<span class="text-danger partial_amount_error">Enter valid partial amount</span>');
                //             $("#partial_amount").focus();
                //         }
                //     }
                // }


                // ✅ Validate Cash + Online
                if ($("#cash_online_type_container").is(":visible")) {
                    if (!cashOnlineType) {
                        isValid = false;
                        $("#cash_online_type")
                            .addClass("is-invalid")
                            .closest(".form-group")
                            .append('<span class="text-danger cash_online_type_error">Select type</span>');
                        $("#cash_online_type").focus();
                    } else if (cashOnlineType === "cash_online_Fully") {
                        if (parseFloat(cashOnlineCash) !== grandTotal) {
                            isValid = false;
                            $("#cash_online_cash_amount")
                                .addClass("is-invalid")
                                .closest(".form-group")
                                .append('<span class="text-danger cash_online_cash_error">Amount must equal total</span>');
                            $("#cash_online_cash_amount").focus();
                        }
                    } else if (cashOnlineType === "cash_online_Partially") {
                        let totalSplit = (parseFloat(cashOnlinePartialCash) || 0) + (parseFloat(cashOnlinePartialUpi) || 0);

                        if (totalSplit <= 0 || totalSplit > grandTotal) {
                            isValid = false;
                            $("#cash_online_partial_cash").addClass("is-invalid");
                            $("#cash_online_partial_upi").addClass("is-invalid");
                            $("#cash_online_partial_upi").closest(".form-group")
                                .append('<span class="text-danger cash_online_partial_upi_error">Split must be > 0 and ≤ total</span>');
                        }
                    }
                }

                // ✅ EMI validation
                if ($("#emi_container").is(":visible")) {
                    if (!emiDuration || parseInt(emiDuration) < 1 || parseInt(emiDuration) > 12) {
                        $("#emi_duration")
                            .addClass("is-invalid")
                            .closest(".form-group")
                            .append('<span class="text-danger emi_duration_error">1-12 months only</span>');
                        $("#emi_duration").focus();
                        return false;
                    }
                }

                if (!isValid) return false;

                // Show loading text and disable the button
                $btn.html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Placing Order...').prop('disabled', true);


                if (!selectedPayment) {
                    $(".error_peymentmethod").text("Please select a payment method").css("color", "red");
                    isValid = false;
                }



                // if (subtotal <= 0) {
                //     $(".total-value").append("<span class='error_total' style='color:red; display:block;'>Please select at least one product</span>");
                //     isValid = false;
                // }
                if (subtotal <= 0) {
                    $(".error_total")
                        .text("Please select at least one product")
                        .show();
                    isValid = false;
                } else {
                    $(".error_total").hide().text("");
                }





                // if (!isValid) return;
                // if (!isValid) {
                //     $btn.html(originalText).prop('disabled', false); // <-- important line
                //     return;
                // }




                // Prepare order items data
                let orderItems = [];
                selectedItems.forEach(function(item) {


                    let price = typeof item.price === "string" ?
                        parseFloat(item.price.replace(/[^0-9.-]+/g, "")) :
                        parseFloat(item.price) || 0;
                    orderItems.push({
                        product_id: item.productId,
                        quantity: item.quantity,
                        categoryId: item.categoryId,
                        price: price,
                        total: price * item.quantity
                    });
                });



                // Prepare tax data
                let taxes = [];
                $(".taxList").each(function() {

                    const taxName = $(this).find("h5").text().replace(" Tax", "");
                    const taxAmount = parseFloat($(this).find("h6").text().replace(/[^0-9.]/g, ""));


                    const taxRate = parseFloat($(this).find(".tax-value").data("rate"));
                    taxes.push({
                        tax_name: taxName,
                        rate: taxRate,
                        amount: taxAmount
                    });
                });



                const selectedSubAdminId = localStorage.getItem("selectedSubAdminId");

                const remaining_amount = parseFloat($("#cash_online_remaining").val()) ||
                    parseFloat($("#cash_remaining_amount").val()) ||
                    parseFloat($("#remaining_amount").val()) || 0;


                let type; 

                if (cashType === "cash_partially" || onlineType === "Partially" || cashOnlineType === "cash_online_partially") {
                    type = "partially";
                } else if (cashType === "cash_Fully" || onlineType === "Fully" || cashOnlineType === "cash_online_Fully") {
                    type = "fully";
                } else {
                    type = "emi";
                }

                let orderData = {
                    user_id: name_or_id,
                    user_phone: phone,
                    payment_method: selectedPayment,
                    cash_type: $("#cash_type").val() || "",
                    car_model: $("#car_model").val() || "",
                    vehicle_no: $("#vehicle_no").val() || "",
                    warranty_date: $("#warranty_date").val() || "",
                    payment_date: $("#payment_date").val() || "",
                    staff_name: $("#staff_name").val() || "",
                    cash_partial_amount: $("#cash_partial_amount").val() || "",
                    online_type: $("#online_type").val() || "",
                    partial_amount: $("#partial_amount").val() || "",
                    cash_online_type: $("#cash_online_type").val() || "",
                    cash_online_cash_amount: $("#cash_online_cash_amount").val() || "",
                    cash_online_upi_amount: $("#cash_online_upi_amount").val() || "",
                    cash_online_partial_cash: $("#cash_online_partial_cash").val() || "",
                    cash_online_partial_upi: $("#cash_online_partial_upi").val() || "",
                    emi_amount: $("#emi_amount").val() || "",
                    emi_duration: $("#emi_duration").val() || "",
                    calculated_emi: $("#calculated_emi").val() || "",
                    method: paidBy,
                    type: type,
                    subtotal: subtotal,
                    discount: discount,
                    discount_amount: subtotal * (discount / 100),
                    remaining_amount: remaining_amount,
                    tax: taxes,
                    total: total,
                    items: orderItems,
                    selectedSubAdminId: selectedSubAdminId,
                    remark: remark,
                    shipping: parseFloat($('#shipping').val()) || 0,
                    misc_charges: parseFloat($('#misc_charges').val()) || 0,
                    labour_amount: parseFloat($('#labour_amount').val()) || 0,
                    gst_option: gstOption,
                    userType: userType

                };



                // Submit order via AJAX
                $.ajax({
                    url: "/api/order_sale",
                    type: "POST",
                    dataType: "json",
                    headers: {
                        "Authorization": "Bearer " + authToken,
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
                    },
                    data: JSON.stringify(orderData),
                    success: function(response) {
                        // Re-enable and reset the button
                        $btn.html(originalText).prop('disabled', false);
                        if (response.status) {
                            orderId = response.order_id;
                            // alert("Order placed successfully!");
                            // location.reload();
                            Swal.fire({
                                title: "Success",
                                text: response.message,
                                icon: "success",
                                confirmButtonText: "OK",
                                confirmButtonColor: "#ff9f43" // Set custom button color
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.open("/sales/invoice/pdf/" + orderId, "_blank");
                                    window.location.href = "/sales-invoice/" + orderId;
                                }
                            });
                            // Reset form
                            selectedItems.clear();
                            $(".check-product i").removeClass("selected");
                            $("input[name='customer_name'], input[name='customer_phone']").val("");
                            $("input[name='payment_method']").prop("checked", false);
                            $(".paymentmethod").removeClass("active");
                            $("#discount").val("");
                            updateTotalItems();
                        } else {
                            alert("Error: " + response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        // Re-enable and reset the button
                        $btn.html(originalText).prop('disabled', false);

                        // SweetAlert2 error popup
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Error placing order. Please try again.',
                            confirmButtonColor: '#3085d6',
                            confirmButtonText: 'OK'
                        });
                    }

                });
            });

            $(".bill-generate").click(function(event) {
                event.preventDefault();
                var $btn = $(this); // cache the button
                var originalText = $btn.html();


                // Reset errors
                $(".error_customername, .error_customerphone, .error_peymentmethod, .error_discount").text("");
                $(".total-value .error_total").remove();


                let userType = $("#user_type").val() || "";

                if (userType === "customer") {
                    let customerId = $("#customer_name").val();
                    let customerName = $("#customer_name option:selected").text().trim();
                    phone = $("#customer_phone").val().trim();

                    // send id if exists, else name
                    name_or_id = customerId ? customerId : customerName;


                } else if (userType === "vendor") {
                    let vendorId = $("#vendor_id").val();
                    let vendorName = $("#vendor_id option:selected").text().trim();
                    phone = $("#vendor_phone").val().trim();

                    // send id if exists, else name
                    name_or_id = vendorId ? vendorId : vendorName;
                }

                let selectedPayment = $("input[name='payment_method']:checked").val();
                let subtotal = parseFloat($("input[name='subtotal']").val()) || 0;
                let total = parseFloat($("input[name='total']").val()) || 0;
                let discount = parseFloat($("#discount").val()) || 0;
                let grandTotal = parseFloat($("#grand_total_form").val()) || 0;
                let paidBy = $("#paid_by").val() || "";
                let cashType = $("#cash_type").val() || "";
                let onlineType = $("#online_type").val() || "";
                let cashPartial = $("#cash_partial_amount").val() || "";
                let partialAmount = $("#partial_amount").val() || "";
                let cashOnlineType = $("#cash_online_type").val() || "";
                let cashOnlineCash = $("#cash_online_cash_amount").val() || "";
                let cashOnlinePartialCash = $("#cash_online_partial_cash").val() || "";
                let cashOnlinePartialUpi = $("#cash_online_partial_upi").val() || "";
                let emiDuration = $("#emi_duration").val() || "";
                let remark = $("#remark").val() || "";
                let discount_group = $("#discount_group").val() || "";
                let gstOption = $('input[name="gst_option"]:checked').val() || "without";

                let bill_generate = $("#bill_generate").val() || "";




                let isValid = true;

                // Reset errors
                $(
                    "#paid_by, #cash_type, #cash_partial_amount, #online_type, #partial_amount, #cash_online_type, #cash_online_cash_amount, #cash_online_partial_cash, #cash_online_partial_upi"
                ).removeClass("is-invalid");
                $(
                    ".paid_by_error, .cash_type_error, .cash_partial_error, .online_type_error, .partial_amount_error, .cash_online_type_error, .cash_online_cash_error, .cash_online_partial_cash_error, .cash_online_partial_upi_error"
                ).remove();

                $("#emi_duration").removeClass("is-invalid");
                $(".emi_duration_error").remove();

                $(".error_customername").text('');
                $(".error_customerphone").text('');


                // Reset previous validation
                $("#customer_name, #vendor_id, #customer_phone, #vendor_phone, #user_type").removeClass("is-invalid");
                $(".error_customername, .error-vendor_id, .error_customerphone, .error-vendorphone, .error-usertype").text("");

                if (userType === "customer") {
                    // Get values
                    name = $("#customer_name option:selected").text().trim();
                    phone = $("#customer_phone").val().trim();

                    // Validate Customer Name
                    if (!$("#customer_name").val()) {
                        isValid = false;
                        $("#customer_name").addClass("is-invalid");
                        $(".error_customername").text("Please select a customer");
                    }

                    // Validate Customer Phone
                    if (!phone) {
                        isValid = false;
                        $("#customer_phone").addClass("is-invalid");
                        $(".error_customerphone").text("Please enter customer phone");
                    } else if (!/^[0-9]{10}$/.test(phone)) {
                        isValid = false;
                        $("#customer_phone").addClass("is-invalid");
                        $(".error_customerphone").text("Enter a valid 10-digit phone number");
                    }

                } else if (userType === "vendor") {
                    // Get values
                    name = $("#vendor_id option:selected").text().trim();
                    phone = $("#vendor_phone").val().trim();

                    // Validate Vendor Name
                    if (!$("#vendor_id").val()) {
                        isValid = false;
                        $("#vendor_id").addClass("is-invalid");
                        $(".error-vendor_id").text("Please select a vendor");
                    }

                    // Validate Vendor Phone
                    if (!phone) {
                        isValid = false;
                        $("#vendor_phone").addClass("is-invalid");
                        $(".error-vendorphone").text("Please enter vendor phone");
                    } else if (!/^[0-9]{10}$/.test(phone)) {
                        isValid = false;
                        $("#vendor_phone").addClass("is-invalid");
                        $(".error-vendorphone").text("Enter a valid 10-digit phone number");
                    }

                } else {
                    isValid = false;
                    $("#user_type").addClass("is-invalid");
                    $(".error-usertype").text("Please select a user type (Customer or Vendor)");
                }



                $(".error_product").text('');

                // Check if at least one product exists
                let totalProducts = $(".product-table").children().length;
                if (totalProducts === 0) {
                    isValid = false;
                    $(".error_product").text("Please select at least one product.");
                }

                // ✅ Validate Paid By
                if (!paidBy) {
                    isValid = false;
                    $("#paid_by").focus().addClass("is-invalid")
                        .after('<span class="text-danger paid_by_error">Please select method</span>');
                }

                // ✅ Validate Cash
                // if (paidBy === "Cash") {
                //     if (cashType === "cash_fully") {
                //         if (parseFloat(cashPartial) !== grandTotal) {
                //             isValid = false;
                //             $("#cash_partial_amount")
                //                 .addClass("is-invalid")
                //                 .closest(".form-group")
                //                 .append('<span class="text-danger cash_partial_error">Amount must equal total</span>');
                //             $("#cash_partial_amount").focus();
                //         }
                //     } else if (cashType === "cash_partially") {
                //         if (!cashPartial || cashPartial.trim() === "" || parseFloat(cashPartial) <= 0 || parseFloat(cashPartial) > grandTotal) {
                //             isValid = false;
                //             $("#cash_partial_amount")
                //                 .addClass("is-invalid")
                //                 .closest(".form-group")
                //                 .append('<span class="text-danger cash_partial_error">Please enter a valid partial amount</span>');
                //             $("#cash_partial_amount").focus();
                //         }
                //     }
                // }


                // ✅ Validate Online
                // if (paidBy === "Online") {
                //     if (!onlineType) {
                //         isValid = false;
                //         $("#online_type")
                //             .addClass("is-invalid")
                //             .closest(".form-group")
                //             .append('<span class="text-danger online_type_error">Select type</span>');
                //         $("#online_type").focus();
                //     } else if (onlineType === "Fully") {
                //         if (parseFloat(partialAmount) !== grandTotal) {
                //             isValid = false;
                //             $("#partial_amount")
                //                 .addClass("is-invalid")
                //                 .closest(".form-group")
                //                 .append('<span class="text-danger partial_amount_error">Amount must equal total</span>');
                //             $("#partial_amount").focus();
                //         }
                //     } else if (onlineType === "Partially") {
                //         if (!partialAmount || parseFloat(partialAmount) <= 0 || parseFloat(partialAmount) > grandTotal) {
                //             isValid = false;
                //             $("#partial_amount")
                //                 .addClass("is-invalid")
                //                 .closest(".form-group")
                //                 .append('<span class="text-danger partial_amount_error">Enter valid partial amount</span>');
                //             $("#partial_amount").focus();
                //         }
                //     }
                // }


                // ✅ Validate Cash + Online
                // if ($("#cash_online_type_container").is(":visible")) {
                //     if (!cashOnlineType) {
                //         isValid = false;
                //         $("#cash_online_type")
                //             .addClass("is-invalid")
                //             .closest(".form-group")
                //             .append('<span class="text-danger cash_online_type_error">Select type</span>');
                //         $("#cash_online_type").focus();
                //     } else if (cashOnlineType === "cash_online_Fully") {
                //         if (parseFloat(cashOnlineCash) !== grandTotal) {
                //             isValid = false;
                //             $("#cash_online_cash_amount")
                //                 .addClass("is-invalid")
                //                 .closest(".form-group")
                //                 .append('<span class="text-danger cash_online_cash_error">Amount must equal total</span>');
                //             $("#cash_online_cash_amount").focus();
                //         }
                //     } else if (cashOnlineType === "cash_online_Partially") {
                //         let totalSplit = (parseFloat(cashOnlinePartialCash) || 0) + (parseFloat(cashOnlinePartialUpi) || 0);

                //         if (totalSplit <= 0 || totalSplit > grandTotal) {
                //             isValid = false;
                //             $("#cash_online_partial_cash").addClass("is-invalid");
                //             $("#cash_online_partial_upi").addClass("is-invalid");
                //             $("#cash_online_partial_upi").closest(".form-group")
                //                 .append('<span class="text-danger cash_online_partial_upi_error">Split must be > 0 and ≤ total</span>');
                //         }
                //     }
                // }

                // ✅ EMI validation
                // if ($("#emi_container").is(":visible")) {
                //     if (!emiDuration || parseInt(emiDuration) < 1 || parseInt(emiDuration) > 12) {
                //         $("#emi_duration")
                //             .addClass("is-invalid")
                //             .closest(".form-group")
                //             .append('<span class="text-danger emi_duration_error">1-12 months only</span>');
                //         $("#emi_duration").focus();
                //         return false;
                //     }
                // }

                if (!isValid) return false;

                // Show loading text and disable the button
                $btn.html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Placing Order...').prop('disabled', true);


                if (!selectedPayment) {
                    $(".error_peymentmethod").text("Please select a payment method").css("color", "red");
                    isValid = false;
                }



                // if (subtotal <= 0) {
                //     $(".total-value").append("<span class='error_total' style='color:red; display:block;'>Please select at least one product</span>");
                //     isValid = false;
                // }
                if (subtotal <= 0) {
                    $(".error_total")
                        .text("Please select at least one product")
                        .show();
                    isValid = false;
                } else {
                    $(".error_total").hide().text("");
                }





                // if (!isValid) return;
                // if (!isValid) {
                //     $btn.html(originalText).prop('disabled', false); // <-- important line
                //     return;
                // }




                // Prepare order items data
                let orderItems = [];
                selectedItems.forEach(function(item) {


                    let price = typeof item.price === "string" ?
                        parseFloat(item.price.replace(/[^0-9.-]+/g, "")) :
                        parseFloat(item.price) || 0;
                    orderItems.push({
                        product_id: item.productId,
                        quantity: item.quantity,
                        categoryId: item.categoryId,
                        price: price,
                        total: price * item.quantity
                    });
                });



                // Prepare tax data
                let taxes = [];
                $(".taxList").each(function() {

                    const taxName = $(this).find("h5").text().replace(" Tax", "");
                    const taxAmount = parseFloat($(this).find("h6").text().replace(/[^0-9.]/g, ""));


                    const taxRate = parseFloat($(this).find(".tax-value").data("rate"));
                    taxes.push({
                        tax_name: taxName,
                        rate: taxRate,
                        amount: taxAmount
                    });
                });



                const selectedSubAdminId = localStorage.getItem("selectedSubAdminId");

                const remaining_amount = parseFloat($("#cash_online_remaining").val()) ||
                    parseFloat($("#cash_remaining_amount").val()) ||
                    parseFloat($("#remaining_amount").val()) || 0;

                    

                let type; // declare once

                if (cashType === "cash_partially" || onlineType === "Partially" || cashOnlineType === "cash_online_partially") {
                    type = "partially";
                } else if (cashType === "cash_Fully" || onlineType === "Fully" || cashOnlineType === "cash_online_Fully") {
                    type = "fully";
                } else {
                    type = "emi";
                }

                let orderData = {
                    user_id: name_or_id,
                    user_phone: phone,
                    payment_method: selectedPayment,
                    car_model: $("#car_model").val() || "",
                    vehicle_no: $("#vehicle_no").val() || "",
                    cash_type: $("#cash_type").val() || "",
                    warranty_date: $("#warranty_date").val() || "",
                    payment_date: $("#payment_date").val() || "",
                    staff_name: $("#staff_name").val() || "",
                    cash_partial_amount: $("#cash_partial_amount").val() || "",
                    online_type: $("#online_type").val() || "",
                    partial_amount: $("#partial_amount").val() || "",
                    cash_online_type: $("#cash_online_type").val() || "",
                    cash_online_cash_amount: $("#cash_online_cash_amount").val() || "",
                    cash_online_upi_amount: $("#cash_online_upi_amount").val() || "",
                    cash_online_partial_cash: $("#cash_online_partial_cash").val() || "",
                    cash_online_partial_upi: $("#cash_online_partial_upi").val() || "",
                    emi_amount: $("#emi_amount").val() || "",
                    emi_duration: $("#emi_duration").val() || "",
                    calculated_emi: $("#calculated_emi").val() || "",
                    method: paidBy,
                    type: type,
                    subtotal: subtotal,
                    discount: discount,
                    discount_amount: subtotal * (discount / 100),
                    remaining_amount: remaining_amount,
                    tax: taxes,
                    total: total,
                    items: orderItems,
                    selectedSubAdminId: selectedSubAdminId,
                    remark: remark,
                    shipping: parseFloat($('#shipping').val()) || 0,
                    misc_charges: parseFloat($('#misc_charges').val()) || 0,
                    labour_amount: parseFloat($('#labour_amount').val()) || 0,
                    gst_option: gstOption,
                    userType: userType,
                    bill_generate: bill_generate

                };


                // Submit order via AJAX
                $.ajax({
                    url: "/api/order_sale",
                    type: "POST",
                    dataType: "json",
                    headers: {
                        "Authorization": "Bearer " + authToken,
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
                    },
                    data: JSON.stringify(orderData),
                    success: function(response) {
                        // Re-enable and reset the button
                        $btn.html(originalText).prop('disabled', false);
                        if (response.status) {
                            orderId = response.order_id;
                            // alert("Order placed successfully!");
                            // location.reload();
                            Swal.fire({
                                title: "Success",
                                text: response.message,
                                icon: "success",
                                confirmButtonText: "OK",
                                confirmButtonColor: "#ff9f43" // Set custom button color
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.open("/sales/invoice/pdf/" + orderId, "_blank");
                                    window.location.href = "/sales-invoice/" + orderId;
                                }
                            });
                            // Reset form
                            selectedItems.clear();
                            $(".check-product i").removeClass("selected");
                            $("input[name='customer_name'], input[name='customer_phone']").val("");
                            $("input[name='payment_method']").prop("checked", false);
                            $(".paymentmethod").removeClass("active");
                            $("#discount").val("");
                            updateTotalItems();
                        } else {
                            alert("Error: " + response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        // Re-enable and reset the button
                        $btn.html(originalText).prop('disabled', false);

                        // SweetAlert2 error popup
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Error placing order. Please try again.',
                            confirmButtonColor: '#3085d6',
                            confirmButtonText: 'OK'
                        });
                    }

                });
            });

            if (typeof $.fn.owlCarousel === 'function' && $(".owl-carousel").length) {
                try {
                    $(".owl-carousel").owlCarousel({
                        items: 5,
                        loop: false,
                        nav: true,
                        dots: false,
                        margin: 10
                    });
                } catch (e) {
                    console.warn("owlCarousel init error:", e);
                }
            }

            // Handle category click (delegated)
            $(document).on('click', '.tabs li', function() {
                var categoryId = $(this).attr('id');
                if (!categoryId) return;

                // Remove active class from all tabs and tab contents
                $(".tabs li").removeClass('active');
                $(".tab_content").removeClass('active');

                // Add active class to clicked tab
                $(this).addClass('active');

                // Find the corresponding tab content
                var tabContentSelector = '.tab_content[data-tab="' + categoryId + '"]';
                var $tabContent = $(tabContentSelector);

                if ($tabContent.length) {
                    $tabContent.addClass('active');

                    // Check if content already loaded
                    if ($tabContent.find('.productset').length === 0) {
                        loadProductsByCategory(categoryId);
                    }
                } else {
                    // Create new tab content if not found
                    $('.tabs_container').append('<div class="tab_content active" data-tab="' + categoryId + '"></div>');
                    loadProductsByCategory(categoryId);
                }
            });

            // Function to load products by category
            function loadProductsByCategory(categoryId) {
                const rawSubAdminId = localStorage.getItem("selectedSubAdminId");
                let selectedSubAdminId = (rawSubAdminId && rawSubAdminId !== 'null' && rawSubAdminId !== 'undefined' && rawSubAdminId !== '1') ? rawSubAdminId : '';
                let url = `/api/getProductsByCategory/${categoryId}`;

                if (selectedSubAdminId) {
                    url += `?sub_admin_id=${encodeURIComponent(selectedSubAdminId)}`;
                }

                var token = localStorage.getItem("authToken");
                var ajaxHeaders = {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                };
                if (token && token !== "null" && token !== "undefined") {
                    ajaxHeaders["Authorization"] = "Bearer " + token;
                }

                $.ajax({
                    url: url,
                    type: "GET",
                    dataType: "json",
                    headers: ajaxHeaders,
                    success: function(response) {
                        const currencySymbol = response.currency_symbol || '₹';
                        const currencyPosition = response.currency_position || 'left';

                        if (response.status && response.data) {
                            renderProducts(response.data, categoryId, currencySymbol, currencyPosition);
                        } else {
                            renderProducts([], categoryId, currencySymbol, currencyPosition);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Failed to load products for category:", categoryId, error);
                        renderProducts([], categoryId);
                    }
                });
            }

            // Function to render products
            function renderProducts(products, categoryId, currencySymbol, currencyPosition) {

                var productsHtml = '';

                if (!products || products.length === 0) {
                    productsHtml = '<div class="col-12"><p class="text-muted p-3">No products found in this category</p></div>';
                } else {
                    products.forEach(function(product) {
                        // Default image
                        let productImage = "{{ env('ImagePath') }}/admin/assets/img/product/noimage.png";

                        // Handle different image formats
                        if (product.image) {
                            try {
                                // Clean the image string
                                let cleanImageString = product.image.replace(/\\/g, '');
                                cleanImageString = cleanImageString.replace(/^"(.*)"$/, '$1');

                                let imageBasePath = ('{{ rtrim(env('ImagePath', ''), '/') }}').replace(/\/+$/, '');

                                if (cleanImageString.startsWith('[')) {
                                    let imagesArray = JSON.parse(cleanImageString);
                                    if (Array.isArray(imagesArray) && imagesArray.length > 0) {
                                        let cleanImg = String(imagesArray[0]).replace(/^\/+/, '');
                                        productImage = imageBasePath ? `${imageBasePath}/storage/${cleanImg}` : `/storage/${cleanImg}`;
                                    }
                                } else {
                                    let cleanImg = String(cleanImageString).replace(/^\/+/, '');
                                    productImage = imageBasePath ? `${imageBasePath}/storage/${cleanImg}` : `/storage/${cleanImg}`;
                                }
                            } catch (e) {

                            }
                        }

                        // Format price
                        let price = parseFloat(product.price || 0).toFixed(2);
                        let displayPrice = currencyPosition === 'right' ? `${price}${currencySymbol}` : `${currencySymbol}${price}`;

                        // Check if product is out of stock
                        let outOfStockBadge = '';
                        let disabledClass = '';
                        let addButtonDisabled = '';

                        if (Number(product.quantity) === 0) {
                            outOfStockBadge = `<div style="
                                                        position: absolute;
                                                        top: 10px;
                                                        left: 10px;
                                                        background: rgba(255,0,0,0.85);
                                                        color: white;
                                                        font-weight: 700;
                                                        padding: 2px 8px;
                                                        border-radius: 4px;
                                                        font-size: 12px;
                                                        z-index: 10;
                                                        ">Out of Stock</div>`;

                            disabledClass = 'disabled-product'; // optional styling class
                            addButtonDisabled = 'disabled'; // disable button
                        }

                        productsHtml += `
                                                <div class="col-lg-3 col-sm-6 d-flex position-relative">
                                                    <div class="productset flex-fill text-center ${disabledClass}" data-id="${product.id}" data-stock="${product.quantity}" style="position: relative;">
                                                        ${outOfStockBadge}
                                                        <div class="productsetimg">
                                                            <img src="${productImage}" alt="${product.name}" class="productsetimgin">
                                                            <h6>Qty: ${product.quantity || '0'}</h6>
                                                        </div>
                                                        <div class="productsetcontent" style="padding: 0.5rem;">
                                                            <h4 style="font-size: 14px; font-weight: 600; margin: 0.5rem 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                                ${product.name}
                                                            </h4>
                                                            <h1 class="d-none">${product.categoryId || 'Uncategorized'}</h5>
                                                            <h5 style="text-transform: capitalize;">${product.category_name || 'Uncategorized'}</h5>
                                                            <h6>${displayPrice}</h6>        
                                                        </div>
                                                        <div class="productsetbtn">
                                                            <button class="btn btn-added" ${addButtonDisabled}>+ Add</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            `;
                    });
                }

                // Update the tab content
                var $tabContent = $('.tab_content[data-tab="' + categoryId + '"]');
                if ($tabContent.length) {
                    $tabContent.html('<div class="row">' + productsHtml + '</div>');
                } else {
                    $('.tabs_container').append('<div class="tab_content active" data-tab="' + categoryId + '"><div class="row">' + productsHtml + '</div></div>');
                }
            }

            // Load products for the first category by default
            var firstCategory = $(".tabs li:first");
            if (firstCategory.length && firstCategory.attr('id')) {
                var firstCategoryId = firstCategory.attr('id');
                firstCategory.addClass('active');
                var firstTabContent = $('.tab_content[data-tab="' + firstCategoryId + '"]');

                if (firstTabContent.length) {
                    firstTabContent.addClass('active');
                } else {
                    $('.tabs_container').append('<div class="tab_content active" data-tab="' + firstCategoryId + '"></div>');
                }

                // Load data for first category
                loadProductsByCategory(firstCategoryId);
            } else {
                $('#mainBranchToggle').prop('checked', true);
                if ($('.tab_content[data-tab="all"]').length === 0) {
                    $('.tabs_container').append('<div class="tab_content active" data-tab="all"></div>');
                }
                loadProductsByCategory('all');
            }


            // Initialize with empty state
            updateTotalItems();
        });
        $(".select2").select2({
            tags: true,
        });
        // $(document).ready(function() {
        $('#customer_name').on('change', function() {
            var phone = $(this).find(':selected').data('phone');
            $('#customer_phone').val(phone || '');
        });
        // });
        $(document).ready(function() {
            const $searchInput = $('#customerSearch1');
            const $resultBox = $('#searchResults1');
            const selectedSubAdminId = localStorage.getItem("selectedSubAdminId");

            $searchInput.on('input', function() {
                const query = $(this).val().trim();

                if (query.length < 1) {
                    $resultBox.hide();
                    return;
                }

                $.ajax({
                    url: `/search-users`,
                    method: 'GET',
                    data: {
                        query: query,
                        selectedSubAdminId: selectedSubAdminId
                    },
                    dataType: 'json',
                    success: function(data) {
                        $resultBox.empty();

                        let hasResults = false;

                        // Users section
                        // (add user search results here if needed)

                        // Products section
                        if (data.products && data.products.length > 0) {
                            hasResults = true;
                            $resultBox.append(`<div class="list-group-item fw-bold mt-2">Products</div>`);
                            data.products.forEach(product => {
                                $resultBox.append(`
                                                        <div class="list-group-item d-flex align-items-center justify-content-between">
                                                            <div class="d-flex align-items-center">
                                                                <img src="${product.image}" alt="Product Image" class="rounded me-2" style="width:30px; height:30px; object-fit: cover;">
                                                                <div>
                                                                    <strong>${product.name ?? 'N/A'}</strong><br>
                                                                    <small>Price: ${product.price ?? 'N/A'}</small>
                                                                </div>
                                                            </div>
                                                            <button class="btn btn-sm btn-primary add-product" 
                                                                data-id="${product.id}" 
                                                                data-name="${product.name}" 
                                                                data-price="${product.price}" 
                                                                data-image="${product.image}" 
                                                                data-stock="${product.stock ?? 10}" 
                                                                data-category="${product.category_id ?? 0}">+ Add</button>
                                                        </div>
                                                    `);
                            });
                        }

                        // Orders section
                        // (add orders section if needed)

                        if (!hasResults) {
                            $resultBox.html('<div class="list-group-item">No results found</div>');
                        }

                        $resultBox.show();
                    },
                    error: function(xhr, status, error) {

                        $resultBox.hide();
                    }
                });
            });

            $(document).on("click", ".add-product", function() {
                const $btn = $(this);
                const productId = $btn.data("id");
                const productName = $btn.data("name");
                let productPrice = $btn.data("price"); // raw price (may be string)
                const productImage = $btn.data("image");
                const productStock = parseInt($btn.data("stock")) || 0;
                const categoryId = $btn.data("category");

                // 🔹 Ensure price is numeric (remove currency symbols / text)
                productPrice = parseFloat(productPrice?.toString().replace(/[^0-9.]/g, "")) || 0;

                // 🔹 Override price if group price exists
                const groupProduct = groupPrices.find(p => p.product_id == String(productId));
                if (groupProduct) {
                    productPrice = parseFloat(groupProduct.price) || 0;
                }

                // Check if product is already selected
                if (!selectedItems.has(productId)) {
                    selectedItems.set(productId, {
                        id: productId,
                        productId: productId,
                        categoryId: categoryId,
                        name: productName,
                        price: productPrice,
                        originalPrice: productPrice, // keep original safe copy
                        image: productImage,
                        code: "PT001", // Default code
                        quantity: 1,
                        stock: productStock
                    });

                    // Disable button & update label
                    $btn.prop("disabled", true).text("Added");

                    // Update UI & totals
                    updateTotalItems();
                } else {
                    Swal.fire({
                        icon: 'info',
                        title: 'Already Added',
                        text: 'This product is already in the list!',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'OK'
                    });
                }
            });

            // --- POS Action Pills Handlers ---
            $(document).on('click', '#toggle_delivery_cost', function(e) {
                e.preventDefault();
                let $sec = $("#delivery_cost_section");
                let $icon = $(this).find('i');
                if ($sec.is(':visible')) {
                    $sec.hide();
                    $icon.removeClass('fa-minus').addClass('fa-plus');
                    $("#shipping").val('');
                } else {
                    $sec.show();
                    $icon.removeClass('fa-plus').addClass('fa-minus');
                    $("#shipping").focus();
                }
                calculateTotals();
            });

            $(document).on('click', '#toggle_tds', function(e) {
                e.preventDefault();
                let $sec = $("#tds_section");
                let $icon = $(this).find('i');
                if ($sec.is(':visible')) {
                    $sec.hide();
                    $icon.removeClass('fa-minus').addClass('fa-plus');
                    $("#tds_percentage, #tds_amount").val('');
                } else {
                    $sec.show();
                    $icon.removeClass('fa-plus').addClass('fa-minus');
                    $("#tds_percentage").focus();
                }
                calculateTotals();
            });

            $(document).on('click', '#toggle_other_charges', function(e) {
                e.preventDefault();
                let $sec = $("#other_charges_section");
                let $icon = $(this).find('i');
                if ($sec.is(':visible')) {
                    $sec.hide();
                    $icon.removeClass('fa-minus').addClass('fa-plus');
                } else {
                    $sec.show();
                    $icon.removeClass('fa-plus').addClass('fa-minus');
                }
                calculateTotals();
            });

            $(document).on('click', '#addOtherChargeBtn', function() {
                let rowHtml = `
                    <div class="other-charge-row d-flex align-items-center gap-2">
                        <input type="text" class="form-control other-charge-name" placeholder="Charge Name">
                        <input type="number" class="form-control other-charge-amount" placeholder="Amount" min="0" step="0.01">
                        <button type="button" class="btn btn-outline-danger btn-sm removeOtherChargeBtn" style="border-radius: 6px; width: 38px; height: 38px; display: inline-flex; align-items: center; justify-content: center; background: #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.05);"><i class="fas fa-trash"></i></button>
                    </div>`;
                $("#otherChargesContainer").append(rowHtml);
            });

            $(document).on('click', '.removeOtherChargeBtn', function() {
                $(this).closest('.other-charge-row').remove();
                calculateTotals();
            });

            $(document).on('input', '.other-charge-amount', function() {
                calculateTotals();
            });

            $(document).on('click', '#toggle_labour_items', function(e) {
                e.preventDefault();
                let $sec = $("#labour_charges_section");
                let $icon = $(this).find('i');
                if ($sec.is(':visible')) {
                    $sec.hide();
                    $icon.removeClass('fa-minus').addClass('fa-plus');
                } else {
                    $sec.show();
                    $icon.removeClass('fa-plus').addClass('fa-minus');
                }
                calculateTotals();
            });

            $(document).on('submit', '#labourItemForm', function(e) {
                e.preventDefault();
                let name = $("#labour_item_name").val().trim();
                let price = parseFloat($("#labour_item_price").val()) || 0;
                if (!name || price <= 0) return;

                let rowHtml = `
                    <div class="labour-item-row d-flex justify-content-between align-items-center p-1 border rounded mb-1" data-price="${price}">
                        <span style="font-size: 12px;">${name} - ₹${price.toFixed(2)}</span>
                        <button type="button" class="btn btn-sm btn-link text-danger p-0 remove-labour-row" style="font-size: 13px;"><i class="fas fa-times"></i></button>
                    </div>`;
                $("#labourChargesContainer").append(rowHtml);
                $("#labourItemModal").modal('hide');
                $("#labour_item_name, #labour_item_price").val('');
                $("#labour_charges_section").show();
                $("#toggle_labour_items i").removeClass('fa-plus').addClass('fa-minus');

                let currentLabour = 0;
                $(".labour-item-row").each(function() {
                    currentLabour += parseFloat($(this).data('price')) || 0;
                });
                $("#labour_amount").val(currentLabour.toFixed(2));
                calculateTotals();
            });

            $(document).on('click', '.remove-labour-row', function() {
                $(this).closest('.labour-item-row').remove();
                let currentLabour = 0;
                $(".labour-item-row").each(function() {
                    currentLabour += parseFloat($(this).data('price')) || 0;
                });
                $("#labour_amount").val(currentLabour.toFixed(2));
                calculateTotals();
            });

            $(document).on('click', '#toggle_overall_discount', function(e) {
                e.preventDefault();
                let $sec = $("#overall_discount_section");
                let $icon = $(this).find('i');
                if ($sec.is(':visible')) {
                    $sec.hide();
                    $icon.removeClass('fa-minus').addClass('fa-plus');
                    $("#overall_discount_percentage, #discount").val('');
                } else {
                    $sec.show();
                    $icon.removeClass('fa-plus').addClass('fa-minus');
                    $("#discount").focus();
                }
                calculateTotals();
            });

            $(document).on('click', '#openAddRemarksBtn', function(e) {
                e.preventDefault();
                $("#remarksModal").modal('show');
            });

            $(document).on('click', '#saveRemarksBtn', function() {
                let terms = $("#termsConditionModalTextarea").val();
                let remark = $("#remarksModalTextarea").val();
                $("#terms_condition").val(terms);
                $("#remark, #remarks").val(remark);
                $("#remarksModal").modal('hide');
            });

            $(document).on('click', '#toggle_exchange_charges', function(e) {
                e.preventDefault();
                let $sec = $("#exchange_charges_wrapper");
                let $icon = $(this).find('i');
                if ($sec.is(':visible')) {
                    $sec.hide();
                    $icon.removeClass('fa-minus').addClass('fa-plus');
                    $("#pos_exchange_material, #pos_exchange_amount").val('');
                } else {
                    $sec.show();
                    $icon.removeClass('fa-plus').addClass('fa-minus');
                    $("#pos_exchange_material").focus();
                }
                calculateTotals();
            });

            $(document).on('input', '#shipping, #tds_percentage, #discount, #pos_exchange_amount', function() {
                calculateTotals();
            });

            $(document).on('input', '#overall_discount_percentage', function() {
                let perc = parseFloat($(this).val()) || 0;
                let subtotal = parseFloat($("input[name='subtotal']").val()) || 0;
                if (perc > 0 && subtotal > 0) {
                    let discAmt = subtotal * (perc / 100);
                    $("#discount").val(discAmt.toFixed(2));
                } else {
                    $("#discount").val('');
                }
                calculateTotals();
            });

            // Payment Methods Selection
            $(document).on('click', '.paymentmethod', function(e) {
                e.preventDefault();
                $('.paymentmethod').removeClass('active');
                $(this).addClass('active');
                $(this).find('input[type="radio"]').prop('checked', true);
                refreshPosPaymentUi();
            });

            $(document).on('change', '#posPaidType', function() {
                refreshPosPaymentUi();
            });

            $(document).on('input', '#posPaidAmount', function() {
                refreshPosPaymentUi();
            });

            $(document).on('input', '#posCashOnlineCashAmount', function() {
                refreshPosPaymentUi();
            });

            $(document).on('input change', '#emiDownPayment, #emiTenure', function() {
                refreshPosPaymentUi();
            });

            $(document).on('click', '#cancelPaymentDetails', function() {
                $('.paymentmethod').removeClass('active');
                $('.paymentmethod input[value="cash"]').closest('.paymentmethod').addClass('active');
                $('.paymentmethod input[value="cash"]').prop('checked', true);
                $("#posPaidType").val('fully');
                $("#posPaidAmount, #posPendingAmount, #posCashOnlineCashAmount, #posCashOnlineOnlineAmount, #emiDownPayment, #emiLoanAmount, #emiMonthlyAmount").val('');
                refreshPosPaymentUi();
            });

            // All Products Checkbox / Category sync
            $(document).on('change', '#mainBranchToggle', function() {
                if ($(this).is(':checked')) {
                    $('.tabs li').removeClass('active');
                    $('.tabs_container .tab_content').removeClass('active');
                    if ($('.tab_content[data-tab="all"]').length === 0) {
                        $('.tabs_container').append('<div class="tab_content active" data-tab="all"></div>');
                    } else {
                        $('.tab_content[data-tab="all"]').addClass('active');
                    }
                    loadProductsByCategory('all');
                } else {
                    const firstCategory = $('.tabs li:first');
                    if (firstCategory.length) {
                        firstCategory.addClass('active');
                        const catId = firstCategory.attr('id');
                        $('.tabs_container .tab_content').removeClass('active');
                        if ($(`.tab_content[data-tab="${catId}"]`).length === 0) {
                            $('.tabs_container').append(`<div class="tab_content active" data-tab="${catId}"></div>`);
                        } else {
                            $(`.tab_content[data-tab="${catId}"]`).addClass('active');
                        }
                        loadProductsByCategory(catId);
                    }
                }
            });

            $(document).on('click', '.tabs li', function() {
                $('#mainBranchToggle').prop('checked', false);
            });

            // Optional: Hide dropdown on outside click
            $(document).on('click', function(e) {
                if (!$(e.target).closest($searchInput).length && !$(e.target).closest($resultBox).length) {
                    $resultBox.hide();
                }
            });
        });
    </script>
    <script>
        $(document).ready(function() {

            $('#full_payment_block').hide();

            // Master controller: Paid By selection
            $(document).ready(function() {
                const total = parseFloat($("input[name='total']").val()) || 0;

                // ✅ Show Cash container & set default
                $('#cash_type_container').show();
                $('#cash_type').val('cash_Fully'); // default 'Fully'

                // Set Paid By dropdown to Cash
                $('#paid_by').val('Cash');

                // Show full payment block & set total
                $('#full_payment_block').show();
                $('#grand_total_form').val(total.toFixed(2));

                // Hidden input
                $('input[name="type"]').remove();
                $('<input>', {
                    type: 'hidden',
                    name: 'type',
                    id: 'payment_type_hidden'
                }).appendTo('#jobCardForm');

                // Existing change handler
                $('#paid_by').on('change', function() {
                    const paidBy = $(this).val();

                    // Hide all sections
                    $('#online_type_container, #cash_type_container, #cash_fully_fields, #cash_partially_fields, #emi_container, #partial_remaining_container, #full_payment_block, #cash_online_type_container, #cash_online_fully_fields, #cash_online_partial_fields').hide();

                    const total = parseFloat($("input[name='total']").val()) || 0;

                    if (paidBy === 'Cash') {
                        $('#cash_type_container').show();
                        $('#cash_type').val('cash_Fully'); // default again on change

                        $('#full_payment_block').show();
                        $('#grand_total_form').val(total.toFixed(2));

                        $('input[name="type"]').remove();
                        $('<input>', {
                            type: 'hidden',
                            name: 'type',
                            id: 'payment_type_hidden'
                        }).appendTo('#jobCardForm');

                    } else if (paidBy === 'Online') {
                        $('#online_type_container').show();
                        $('#online_type').trigger('change');

                        $('#full_payment_block').hide();
                        $('#grand_total_form').val(total.toFixed(2));

                        $('input[name="type"]').remove();
                        $('<input>', {
                            type: 'hidden',
                            name: 'type',
                            id: 'payment_type_hidden'
                        }).appendTo('#jobCardForm');

                    } else if (paidBy === 'Cash_online') {
                        $('#cash_online_type_container').show();
                        $('#cash_online_type').trigger('change');

                        $('#full_payment_block').hide();
                        $('#grand_total_form').val(total.toFixed(2));

                        $('input[name="type"]').remove();
                        $('<input>', {
                            type: 'hidden',
                            name: 'type',
                            id: 'payment_type_hidden'
                        }).appendTo('#jobCardForm');

                    } else if (paidBy === 'EMI') {
                        $('#emi_container').show();
                        $('#full_payment_block').hide();

                        $('#emi_amount').val(total.toFixed(2));
                        calculateMonthlyEMI();

                        $('input[name="type"]').remove();
                    }
                });
            });



            // Cash type change logic
            $('#cash_type').on('change', function() {
                const type = $(this).val();
                $('#cash_fully_fields').hide();
                $('#cash_partially_fields').hide();
                $('#partial_remaining_container').hide();
                $('#full_payment_block').hide();

                if (type === 'cash_Fully') {
                    $('#cash_fully_fields').hide();
                    $('#full_payment_block').show();

                    // ✅ Set "This Payment" to grand total immediately
                    const grandTotal = parseFloat($('#grand_total_form').val()) || 0;
                    $('#grand_total_form').val(grandTotal.toFixed(2));
                    $('#payment_type_hidden').val('cash_Fully');
                } else if (type === 'cash_partially') {
                    $('#full_payment_block').hide();
                    $('#cash_partially_fields').show();
                    $('#payment_type_hidden').val('cash_partially');
                }
            });

            // Online type logic
            $('#online_type').on('change', function() {
                const type = $(this).val();
                $('#partial_remaining_container').hide();
                $('#full_payment_block').hide();

                if (type === 'Fully') {
                    $('#full_payment_block').show();
                    $('#payment_type_hidden').val('Fully');
                } else if (type === 'Partially') {
                    $('#partial_remaining_container').show();
                    $('#payment_type_hidden').val('Partially');
                }
            });

            $('#cash_online_type').on('change', function() {
                const type = $(this).val();
                $('#cash_online_fully_fields, #cash_online_partial_fields').hide();

                if (type === 'cash_online_Fully') {
                    $('#cash_online_fully_fields').show();
                    const grandTotal = parseFloat($('#grand_total_form').val()) || 0;
                    $('#cash_online_cash_amount').val(grandTotal.toFixed(2));
                    $('#cash_online_upi_amount').val('0.00');
                    $('#full_payment_block').hide();
                    $('#payment_type_hidden').val('cash_online_Fully');

                } else if (type === 'cash_online_Partially') {
                    $('#cash_online_partial_fields').show();
                    $('#full_payment_block').hide();
                    $('#payment_type_hidden').val('cash_online_Partially');
                }
            });
        });
    </script>
    <script>
        $(document).ready(function() {

            // ✅ Cash partially calculation
            $(document).on('input', '#cash_partial_amount', function() {
                const total = parseFloat($("input[name='total']").val()) || 0;
                let partial = parseFloat($(this).val()) || 0;

                // ❌ Negative value allow mat karo
                if (partial < 0) {
                    partial = 0;
                    $(this).val(" ");
                }

                if (partial > total) {
                    $(this).val(total.toFixed(2));
                    $('#cash_remaining_amount').val(" ");
                } else {
                    const remaining = total - partial;
                    $('#cash_remaining_amount').val(remaining.toFixed(2));
                }
            });


            // ✅ Online partially calculation
            $(document).on('input', '#partial_amount', function() {
                const total = parseFloat($("input[name='total']").val()) || 0;
                let partial = parseFloat($(this).val()) || 0;

                // ❌ Negative value allow mat kar
                if (partial < 0) {
                    partial = 0;
                    $(this).val("");
                }

                if (partial > total) {
                    $(this).val(total.toFixed(2));
                    $('#remaining_amount').val("");
                } else {
                    const remaining = total - partial;
                    $('#remaining_amount').val(remaining.toFixed(2));
                }
            });


            function calcCashOnlineFully() {
                const total = parseFloat($("input[name='total']").val()) || 0;

                let cash = parseFloat($("#cash_online_cash_amount").val()) || 0;

                // ❌ Ab turant overwrite nahi karunga while typing
                // ✅ Sirf agar cash > total tabhi adjust karunga
                if (cash > total) {
                    cash = total;
                    $("#cash_online_cash_amount").val(cash.toFixed(2));
                } else if (cash < 0 || isNaN(cash)) {
                    cash = 0;
                    $("#cash_online_cash_amount").val("");
                }

                // Online = total - cash
                const online = total - cash;
                $("#cash_online_upi_amount").val(online.toFixed(2));
                $("#cash_online_upi_amount_hidden").val(online.toFixed(2));
            }

            // Trigger on typing + blur
            $(document).on('input change blur', '#cash_online_cash_amount, input[name="total"]', calcCashOnlineFully);

            function calcEMI() {
                let amount = parseFloat($("#emi_amount").val()) || 0;
                let duration = parseInt($("#emi_duration").val()) || 0;

                let monthly = 0;

                // ✅ Prevent negative amount
                if (amount < 0) {
                    amount = 0;
                    $("#emi_amount").val(""); // reset field
                }

                // ✅ Ensure duration is between 1 and 12
                if (duration < 1 || duration > 12) {
                    duration = 0;
                    $("#emi_duration").val(""); // reset invalid value
                }

                // ✅ Calculate EMI only if amount > 0 and duration valid
                if (amount > 0 && duration > 0) {
                    monthly = amount / duration;
                }

                // ✅ Prevent negative EMI
                if (monthly < 0) {
                    monthly = 0;
                }

                $("#calculated_emi").val(monthly.toFixed(2));
            }


            // 🔗 Trigger calc on amount or duration change
            $(document).on('input change', '#emi_amount, #emi_duration', calcEMI);





            // ✅ Cash + Online — PARTIAL calculation
            $(document).on('input', '#cash_online_partial_cash, #cash_online_partial_upi', function() {
                const total = parseFloat($("input[name='total']").val()) || 0;

                let cash = parseFloat($("#cash_online_partial_cash").val()) || 0;
                let upi = parseFloat($("#cash_online_partial_upi").val()) || 0;

                // ❌ Prevent negative values
                if (cash < 0) {
                    cash = 0;
                    $("#cash_online_partial_cash").val(" ");
                }
                if (upi < 0) {
                    upi = 0;
                    $("#cash_online_partial_upi").val(" ");
                }

                let combined = cash + upi;

                if (combined > total) {
                    // If combined > total → reset latest field
                    if ($(this).attr("id") === "cash_online_partial_cash") {
                        cash = total - upi;
                        $("#cash_online_partial_cash").val(cash >= 0 ? cash.toFixed(2) : "");
                    } else {
                        upi = total - cash;
                        $("#cash_online_partial_upi").val(upi >= 0 ? upi.toFixed(2) : "");
                    }
                    combined = total;
                }

                const remaining = total - combined;
                $("#cash_online_remaining").val(remaining.toFixed(2));
            });


        });
    </script>
    <script>
        $(document).ready(function() {

            $("#user_type").change(function() {
                const type = $(this).val();
                if (type === "customer") {
                    $("#customerSection, #customerPhoneSection").show();
                    $("#vendorDropdown, #vendorPhoneSection").hide();
                } else if (type === "vendor") {
                    $("#vendorDropdown, #vendorPhoneSection").show();
                    $("#customerSection, #customerPhoneSection").hide();
                } else {
                    $("#customerSection, #customerPhoneSection, #vendorDropdown, #vendorPhoneSection").hide();
                }
            });
            $("#user_type").val("customer").trigger("change");

            function toggleCustomerButtons() {
                var val = $('#customer_name').val();
                if (val && !isNaN(val)) {
                    $('#openQuickAddCustomerBtn').hide();
                    $('#openEditCustomerBtn').css('display', 'inline-flex');
                } else {
                    $('#openQuickAddCustomerBtn').css('display', 'inline-flex');
                    $('#openEditCustomerBtn').hide();
                }
            }
            toggleCustomerButtons();

            // Populate customer phone
            $("#customer_name").change(function() {
                const phone = $(this).find(':selected').data('phone') || '';
                $("#customer_phone").val(phone);

                if (phone === "") {
                    $(".error_customerphone").text("");
                } else if (!/^\d+$/.test(phone)) {
                    $(".error_customerphone").text("Enter a valid number (digits only)").css("color", "red");
                } else {
                    $(".error_customerphone").text("");
                }
                toggleCustomerButtons();
            });

            // Populate vendor phone
            $("#vendor_id").change(function() {
                const phone = $(this).find(':selected').data('phone') || '';
                $("#vendor_phone").val(phone);

                if (phone === "") {
                    $(".error-vendorphone").text("");
                } else if (!/^\d+$/.test(phone)) {
                    $(".error-vendorphone").text("Enter a valid number (digits only)").css("color", "red");
                } else {
                    $(".error-vendorphone").text("");
                }
            });

            // Fullscreen panel toggle
            var isPosFullscreen = false;
            $('#posFullscreenToggle').on('click', function() {
                isPosFullscreen = !isPosFullscreen;
                if (isPosFullscreen) {
                    $('#pos-left-panel').hide();
                    $('#pos-right-panel').removeClass('col-lg-5 col-sm-12').addClass('col-lg-12 col-12');
                    $(this).find('i').removeClass('fa-expand').addClass('fa-compress');
                    $(this).attr('title', 'Collapse panel');
                } else {
                    $('#pos-left-panel').show();
                    $('#pos-right-panel').removeClass('col-lg-12 col-12').addClass('col-lg-5 col-sm-12');
                    $(this).find('i').removeClass('fa-compress').addClass('fa-expand');
                    $(this).attr('title', 'Expand panel');
                }
                if (typeof updatePaymentPanelWidth === 'function') {
                    setTimeout(updatePaymentPanelWidth, 100);
                }
            });

            // Order format preview & badge update
            window.posOrderFormats = {
                with_gst: @json($withGstFormat ?? 'INV-GST-{YYYY}-{0000}'),
                without_gst: @json($withoutGstFormat ?? 'INV-{YYYY}-{0000}')
            };

            function evaluateOrderFormat(format, sample) {
                if (!format) return '';
                sample = sample || 1;
                var d = new Date();
                var yyyy = d.getFullYear();
                var yy = String(yyyy).slice(-2);
                var mm = String(d.getMonth() + 1).padStart(2, '0');
                var dd = String(d.getDate()).padStart(2, '0');

                var result = String(format);
                result = result.replace(/{YYYY}/g, yyyy);
                result = result.replace(/{YY}/g, yy);
                result = result.replace(/{MM}/g, mm);
                result = result.replace(/{DD}/g, dd);

                result = result.replace(/{00000}/g, String(sample).padStart(5, '0'));
                result = result.replace(/{0000}/g, String(sample).padStart(4, '0'));
                result = result.replace(/{000}/g, String(sample).padStart(3, '0'));
                result = result.replace(/{00}/g, String(sample).padStart(2, '0'));
                result = result.replace(/{(?:0|1|NUMBER)}/g, sample);
                return result;
            }

            function updateOrderNumberBadge() {
                var $badgeLabel = $('#posOrderNumberLabel');
                var $badgeValue = $('#posOrderNumberValue');
                var formats = window.posOrderFormats || {};
                var isWithGst = ($('input[name="gst_option"]:checked').val() === 'with');

                $badgeLabel.text('Order No:');
                var salePattern = isWithGst ? (formats.with_gst || 'INV-GST-{YYYY}-{0000}') : (formats.without_gst || 'INV-{YYYY}-{0000}');
                $badgeValue.text(evaluateOrderFormat(salePattern, 1));
            }
            $(document).on('change', 'input[name="gst_option"]', updateOrderNumberBadge);

        });

        // Quick Add / Edit Customer JS
        (function() {
            var authToken = localStorage.getItem('authToken');
            var qacModalEl = document.getElementById('quickAddCustomerModal');
            var qacModal = qacModalEl && typeof bootstrap !== 'undefined' ? new bootstrap.Modal(qacModalEl) : null;

            var stateCodeToName = {
                "01": "Jammu and Kashmir", "02": "Himachal Pradesh", "03": "Punjab", "04": "Chandigarh",
                "05": "Uttarakhand", "06": "Haryana", "07": "Delhi", "08": "Rajasthan", "09": "Uttar Pradesh",
                "10": "Bihar", "11": "Sikkim", "12": "Arunachal Pradesh", "13": "Nagaland", "14": "Manipur",
                "15": "Mizoram", "16": "Tripura", "17": "Meghalaya", "18": "Assam", "19": "West Bengal",
                "20": "Jharkhand", "21": "Odisha", "22": "Chhattisgarh", "23": "Madhya Pradesh", "24": "Gujarat",
                "27": "Maharashtra", "29": "Karnataka", "33": "Tamil Nadu", "36": "Telangana"
            };

            function resetQacForm() {
                $('#quickAddCustomerForm')[0].reset();
                $('#qac_edit_id').val('');
                $('.qac-error').html('');
                $('#qac-gst-msg').html('');
                $('#qac_use_same_address').prop('checked', false);
            }

            function setQacMode(mode) {
                var isEdit = mode === 'edit';
                $('#quickAddCustomerModalLabel').text(isEdit ? 'Edit Customer' : 'Add New Customer');
                $('#qacSaveBtnText').text(isEdit ? 'Update Customer' : 'Save Customer');
            }

            function fillQacForm(c) {
                $('#qac_customer_code').val(c.customer_code || '');
                $('#qac_customer_name').val(c.name || '');
                $('#qac_company_name').val(c.company_name || '');
                $('#qac_phone').val(c.phone || '');
                $('#qac_alternate_phone').val(c.alternate_phone || '');
                $('#qac_email').val(c.email || '');
                $('#qac_gst_number').val(c.gst_num || c.gst_number || '');
                $('#qac_pan_number').val(c.pan_number || '');
                $('#qac_state_code').val(c.state_code || '');
                $('#qac_state_name').val(c.state_name || stateCodeToName[c.state_code] || '');
                $('#qac_country').val(c.country || (c.details && c.details.country) || '');
                $('#qac_city').val(c.city || (c.details && c.details.city) || '');
                $('#qac_address').val(c.address || (c.details && c.details.address) || '');
                $('#qac_delivery_address').val(c.delivery_address || (c.details && c.details.delivery_address) || '');

                var addressVal = $('#qac_address').val();
                var deliveryAddressVal = $('#qac_delivery_address').val();
                $('#qac_use_same_address').prop('checked', addressVal && addressVal === deliveryAddressVal);
            }

            $(document).on('click', '#openQuickAddCustomerBtn', function() {
                resetQacForm();
                setQacMode('add');
                if (qacModal) qacModal.show();
            });

            $(document).on('click', '#openEditCustomerBtn', function() {
                var customerId = $('#customer_name').val();
                if (!customerId || !/^\d+$/.test(String(customerId))) return;

                resetQacForm();
                setQacMode('edit');
                $('#qac_edit_id').val(customerId);

                if (qacModal) qacModal.show();
                $('#qacSaveBtn').prop('disabled', true);
                $('#qacBtnSpinner').removeClass('d-none');

                $.ajax({
                    url: '/api/getCustomer/' + customerId,
                    method: 'GET',
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Authorization': 'Bearer ' + authToken
                    },
                    success: function(res) {
                        if (res.status && res.customer) {
                            var c = res.customer;
                            if (c.details) {
                                c.country = c.country || c.details.country;
                                c.city = c.city || c.details.city;
                                c.address = c.address || c.details.address;
                                c.delivery_address = c.delivery_address || c.details.delivery_address;
                            }
                            fillQacForm(c);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Could not load customer details.'
                            });
                            if (qacModal) qacModal.hide();
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to fetch customer details.'
                        });
                        if (qacModal) qacModal.hide();
                    },
                    complete: function() {
                        $('#qacSaveBtn').prop('disabled', false);
                        $('#qacBtnSpinner').addClass('d-none');
                    }
                });
            });

            $(document).on('input', '#qac_state_code', function() {
                var raw = this.value.replace(/[^0-9]/g, '').substring(0, 3);
                this.value = raw;
                $('#qac_state_name').val(stateCodeToName[raw] || '');
            });

            $(document).on('input', '#qac_gst_number', function() {
                var raw = $(this).val().toUpperCase().replace(/[^0-9A-Z]/g, '').substring(0, 15);
                $(this).val(raw);
                $('#qac-gst-msg').html('');
                if (raw.length < 15) return;

                var $loader = $('#qac-gst-loader');
                var $msg = $('#qac-gst-msg');

                $loader.show();
                $(this).prop('readonly', true);
                $msg.html('<span style="color:#1B2850;"><i class="fas fa-spinner fa-spin"></i> Fetching GST details...</span>');

                $.ajax({
                    url: '/api/fetch-gst-details',
                    method: 'POST',
                    dataType: 'json',
                    data: { gst_number: raw },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Authorization': 'Bearer ' + authToken
                    },
                    success: function(res) {
                        if (res.trade_name) $('#qac_customer_name').val(res.trade_name);
                        if (res.legal_name) $('#qac_company_name').val(res.legal_name);
                        if (res.address) $('#qac_address').val(res.address);
                        if (res.city) $('#qac_city').val(res.city);
                        if (res.state_code) {
                            $('#qac_state_code').val(res.state_code);
                            $('#qac_state_name').val(res.state_name || stateCodeToName[res.state_code] || '');
                        }
                        if (res.country) $('#qac_country').val(res.country);
                        if (raw.length >= 12) $('#qac_pan_number').val(raw.substring(2, 12));
                        $msg.html('<span style="color:#28a745;"><i class="fas fa-check-circle"></i> Details fetched successfully.</span>');
                    },
                    error: function() {
                        $('#qac-gst-msg').html('<span class="text-danger">Failed to fetch GST details.</span>');
                    },
                    complete: function() {
                        $loader.hide();
                        $('#qac_gst_number').prop('readonly', false);
                    }
                });
            });

            $(document).on('input', '#qac_phone, #qac_alternate_phone', function() {
                this.value = this.value.replace(/\D/g, '').substring(0, 10);
            });

            $(document).on('change', '#qac_use_same_address', function() {
                if ($(this).is(':checked')) {
                    $('#qac_delivery_address').val($('#qac_address').val());
                } else {
                    $('#qac_delivery_address').val('');
                }
            });

            $(document).on('submit', '#quickAddCustomerForm', function(e) {
                e.preventDefault();
                $('.qac-error').html('');

                var editId = $('#qac_edit_id').val();
                var isEdit = !!editId;

                var $btn = $('#qacSaveBtn');
                var $spinner = $('#qacBtnSpinner');
                $btn.prop('disabled', true);
                $spinner.removeClass('d-none');

                var formData = new FormData();
                formData.append('customer_code', $('#qac_customer_code').val().trim());
                formData.append('customer_name', $('#qac_customer_name').val().trim());
                formData.append('company_name', $('#qac_company_name').val().trim());
                formData.append('phone', $('#qac_phone').val().trim());
                formData.append('alternate_phone', $('#qac_alternate_phone').val().trim());
                formData.append('email', $('#qac_email').val().trim());
                formData.append('gst_number', $('#qac_gst_number').val().trim());
                formData.append('gst_num', $('#qac_gst_number').val().trim());
                formData.append('pan_number', $('#qac_pan_number').val().trim());
                formData.append('state_code', $('#qac_state_code').val().trim());
                formData.append('state_name', $('#qac_state_name').val().trim());
                formData.append('country', $('#qac_country').val().trim());
                formData.append('city', $('#qac_city').val().trim());
                formData.append('address', $('#qac_address').val().trim());
                formData.append('delivery_address', $('#qac_delivery_address').val().trim());

                var sid = localStorage.getItem('selectedSubAdminId');
                if (sid && sid !== 'null' && sid !== 'undefined') {
                    formData.append('sub_admin_id', sid);
                }

                var apiUrl = isEdit ? '/api/updateCustomer/' + editId : '/api/createCustomer';

                $.ajax({
                    url: apiUrl,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Authorization': 'Bearer ' + authToken
                    },
                    success: function(response) {
                        if (response.status) {
                            var name = $('#qac_customer_name').val().trim();
                            var company = $('#qac_company_name').val().trim();
                            var phone = $('#qac_phone').val().trim();
                            var gst = $('#qac_gst_number').val().trim();
                            var label = (company || name) + (phone ? ' - ' + phone : '');

                            var $select = $('#customer_name');
                            var realId = editId || (response.customer ? response.customer.id : (response.customer_id || response.id));

                            var $opt = $select.find('option[value="' + realId + '"]');
                            if ($opt.length) {
                                $opt.text(label).attr('data-phone', phone).attr('data-gst', gst);
                            } else {
                                $opt = $('<option>', { value: realId, text: label }).attr('data-phone', phone).attr('data-gst', gst);
                                $select.append($opt);
                            }

                            if ($select.data('select2')) {
                                $select.val(realId).trigger('change');
                                $select.trigger('change.select2');
                            } else {
                                $select.val(realId).trigger('change');
                            }

                            $('#customer_phone').val(phone);
                            if (qacModal) qacModal.hide();
                            if (typeof toggleCustomerButtons === 'function') {
                                toggleCustomerButtons();
                            }

                            Swal.fire({
                                icon: 'success',
                                title: isEdit ? 'Customer Updated' : 'Customer Added',
                                text: name + (isEdit ? ' updated' : ' added') + ' successfully.',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message || response.error || 'Operation failed.'
                            });
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            var errors = (xhr.responseJSON || {}).errors || {};
                            $.each(errors, function(key, msgs) {
                                $('#qac_error_' + key).html(msgs[0]);
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: (xhr.responseJSON || {}).message || 'Something went wrong.'
                            });
                        }
                    },
                    complete: function() {
                        $btn.prop('disabled', false);
                        $spinner.addClass('d-none');
                    }
                });
            });
        })();
    </script>

    <script>
        /* =====================================================
           Payment Panel — Drag Handle + Minimize/Restore
           (Same functionality & code as main-erp-product)
           ===================================================== */
        (function() {
            var panel = document.getElementById('paymentPanel');
            var handle = document.getElementById('paymentPanelHandle');
            var body = document.getElementById('paymentPanelBody');
            var minBtn = document.getElementById('paymentPanelMinBtn');
            var minIcon = document.getElementById('panelMinIcon');

            if (!panel || !handle) return;

            var isDragging = false;
            var startY = 0;
            var startTop = 0;
            var isCollapsed = false;

            /* ── Only apply dragging when the panel is position:fixed (and NOT on responsive/mobile where it is a bottom bar) ── */
            function isPanelFixed() {
                if (window.innerWidth <= 991.98) return false;
                return getComputedStyle(panel).position === 'fixed';
            }

            /* ── Responsive tap/click handle to toggle payment options on mobile ── */
            handle.addEventListener('click', function(e) {
                if (window.innerWidth > 991.98) return;
                if (e.target.closest && e.target.closest('#paymentPanelMinBtn')) return;
                panel.classList.toggle('pos-pay-collapsed');
                if (typeof window.updatePaySummary === 'function') {
                    window.updatePaySummary();
                }
            });

            /* ── Initialise top position ── */
            function initPanelTop() {
                if (!isPanelFixed()) return;
                if (!panel.style.top) {
                    var rect = panel.getBoundingClientRect();
                    panel.style.top = rect.top + 'px';
                    panel.style.bottom = 'auto';
                }
            }

            /* ── Mouse drag ── */
            handle.addEventListener('mousedown', function(e) {
                if (!isPanelFixed()) return;
                if (minBtn && (e.target === minBtn || minBtn.contains(e.target))) return;
                initPanelTop();
                isDragging = true;
                startY = e.clientY;
                startTop = parseInt(panel.style.top, 10) || panel.getBoundingClientRect().top;
                document.body.style.userSelect = 'none';
                e.preventDefault();
            });

            document.addEventListener('mousemove', function(e) {
                if (!isDragging) return;
                var delta = e.clientY - startY;
                var newTop = startTop + delta;
                // Clamp: don't drag above 60px or below (viewport - 60px)
                newTop = Math.max(60, Math.min(window.innerHeight - 60, newTop));
                panel.style.top = newTop + 'px';
                panel.style.bottom = 'auto';
            });

            document.addEventListener('mouseup', function() {
                if (isDragging) {
                    isDragging = false;
                    document.body.style.userSelect = '';
                }
            });

            /* ── Touch drag ── */
            handle.addEventListener('touchstart', function(e) {
                if (!isPanelFixed()) return;
                if (minBtn && (e.target === minBtn || minBtn.contains(e.target))) return;
                initPanelTop();
                isDragging = true;
                startY = e.touches[0].clientY;
                startTop = parseInt(panel.style.top, 10) || panel.getBoundingClientRect().top;
            }, {
                passive: true
            });

            document.addEventListener('touchmove', function(e) {
                if (!isDragging) return;
                var delta = e.touches[0].clientY - startY;
                var newTop = startTop + delta;
                newTop = Math.max(60, Math.min(window.innerHeight - 60, newTop));
                panel.style.top = newTop + 'px';
                panel.style.bottom = 'auto';
            }, {
                passive: true
            });

            document.addEventListener('touchend', function() {
                isDragging = false;
            });

            /* ── Minimize / Restore button ── */
            var expandIcon =
                '<path d="M7.646 4.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1-.708.708L8 5.707l-5.646 5.647a.5.5 0 0 1-.708-.708z"/>';
            var collapseIcon =
                '<path d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z"/>';

            if (minBtn) {
                minBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    isCollapsed = !isCollapsed;
                    if (isCollapsed) {
                        body.style.maxHeight = body.scrollHeight + 'px';
                        requestAnimationFrame(function() {
                            body.classList.add('panel-collapsed');
                        });
                        minIcon.innerHTML = collapseIcon;
                    } else {
                        body.classList.remove('panel-collapsed');
                        body.style.maxHeight = body.scrollHeight + 'px';
                        setTimeout(function() {
                            body.style.maxHeight = '';
                        }, 320);
                        minIcon.innerHTML = expandIcon;
                    }
                });
            }
        })();

        // Synchronize Payment Panel Width and Position with Right Column
        function updatePaymentPanelWidth() {
            var $rightPanel = $('#pos-right-panel');
            var $panel = $('#paymentPanel');
            if (!$rightPanel.length || !$panel.length) return;

            if ($(window).width() > 991.98) {
                var rect = $rightPanel[0].getBoundingClientRect();
                $panel.css({
                    'width': rect.width + 'px',
                    'left': rect.left + 'px',
                    'right': 'auto'
                });
            } else {
                $panel.css({
                    'width': '',
                    'left': '',
                    'right': '',
                    'top': '',
                    'bottom': ''
                });
            }
        }
        $(window).on('resize', updatePaymentPanelWidth);
        $(window).on('scroll', updatePaymentPanelWidth);
        $(document).ready(function() {
            setTimeout(updatePaymentPanelWidth, 150);
            if (typeof window.updatePaySummary === 'function') {
                window.updatePaySummary();
            }
        });
    </script>


    @endpush