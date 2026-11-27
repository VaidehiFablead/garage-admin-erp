<style>
    .mm-head,
    .mm-card-list {
        display: none;
    }

    @media (max-width: 1024.98px) {
        .mm-page.content {
            padding: 6px 6px 90px !important;
        }
        .mm-page > .page-header,
        .mm-page .table-container,
        .mm-page .table-responsive {
            display: none !important;
        }

        .mm-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            margin-bottom: 8px;
            padding: 12px 10px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(16, 24, 40, .08);
        }
        .mm-title {
            margin-left: 2px;
            font-size: 15px;
            font-weight: 600;
            color: #1b2850;
            white-space: nowrap;
        }
        .mm-count {
            margin: 1px 0 0 2px;
            font-size: 11.5px;
            color: #8a92a6;
        }
        .mm-count:empty {
            display: none;
        }
        .mm-add {
            display: inline-flex !important;
            align-items: center;
            height: 30px;
            margin: 0 !important;
            padding: 0 10px !important;
            border: 1px solid #1b2850 !important;
            border-radius: 8px !important;
            background: #1b2850 !important;
            color: #fff !important;
            font-size: 12px !important;
            font-weight: 600;
            white-space: nowrap;
        }

        .mm-page > .card {
            background: transparent !important;
            border: 0 !important;
            box-shadow: none !important;
            margin: 0 !important;
        }
        .mm-page > .card > .card-body {
            padding: 0 !important;
        }
        .mm-page .mm-search {
            margin: 0 0 8px !important;
            padding: 8px 10px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(16, 24, 40, .08);
        }
        .mm-page .mm-search.row {
            --bs-gutter-x: 0;
            row-gap: 8px;
        }
        .mm-page .mm-search .mb-2 {
            margin-bottom: 0 !important;
        }
        .mm-page .mm-search .search-set {
            width: 100% !important;
            margin: 0 !important;
        }
        .mm-page .mm-search .search-input {
            position: relative;
            display: flex;
            align-items: center;
            width: 100% !important;
            margin: 0 !important;
        }
        .mm-page .mm-search .search-input input {
            width: 100% !important;
            height: 40px !important;
            padding-left: 36px !important;
            border-radius: 10px !important;
            font-size: 13px;
        }
        .mm-page .mm-search .btn-searchset {
            position: absolute;
            left: 10px;
            top: 50% !important;
            z-index: 2;
            padding: 0;
            transform: translateY(-50%);
        }
        .mm-page .mm-search.row > [class*="col-"] {
            margin-bottom: 0 !important;
        }
        .mm-page .mm-search .form-select {
            height: 40px;
            border-radius: 10px;
            font-size: 13px;
        }

        .mm-card-list {
            display: flex !important;
            flex-direction: column;
            gap: 8px;
        }
        .mm-card {
            display: grid;
            grid-template-columns: 42px minmax(0, 1fr) auto;
            align-items: center;
            column-gap: 10px;
            padding: 12px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(16, 24, 40, .08);
        }
        .mm-avatar {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 42px;
            height: 42px;
            border-radius: 10px;
            overflow: hidden;
            background: #fff3e6;
            color: #e07b14;
            font-size: 15px;
            font-weight: 700;
        }
        .mm-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .mm-main {
            min-width: 0;
        }
        .mm-name {
            overflow: hidden;
            color: #1b2850;
            font-size: 14px;
            font-weight: 700;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .mm-sub {
            margin-top: 2px;
            overflow: hidden;
            color: #8a92a6;
            font-size: 12px;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .mm-acts {
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .mm-act {
            display: inline-flex !important;
            align-items: center;
            justify-content: center;
            width: 34px;
            height: 34px;
            margin: 0 !important;
            border-radius: 50%;
            font-size: 13px;
            text-decoration: none;
        }
        .mm-act.is-edit {
            background: #eef4ff;
            color: #3b6fd8 !important;
        }
        .mm-act.is-delete {
            background: #fdecee;
            color: #e5484d !important;
        }
        .mm-foot {
            grid-column: 1 / -1;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(0, 1fr));
            gap: 8px;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px dashed #e5e7eb;
        }
        .mm-stat {
            min-width: 0;
        }
        .mm-stat > span {
            display: block;
            color: #8a92a6;
            font-size: 11px;
        }
        .mm-stat strong {
            display: block;
            overflow: hidden;
            color: #1b2850;
            font-size: 12.5px;
            font-weight: 700;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .mm-stat strong:has(.mm-chips) {
            white-space: normal;
        }
        .mm-chips {
            display: flex;
            flex-wrap: wrap;
            gap: 4px;
            margin-top: 2px;
        }
        .mm-chip {
            padding: 2px 8px;
            border-radius: 999px;
            background: #f1f2f6;
            color: #475467;
            font-size: 11px;
            font-weight: 600;
        }
        .mm-empty {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            padding: 32px 12px;
            background: #fff;
            border-radius: 12px;
            color: #8a92a6;
            font-size: 13px;
        }
        .mm-empty i {
            font-size: 28px;
            color: #d0d5dd;
        }

        .mm-page .pagination-controls {
            margin-top: 10px !important;
            padding: 10px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(16, 24, 40, .08);
            gap: 8px;
        }
        .mm-page .pagination-controls > .d-flex {
            flex-wrap: wrap;
            justify-content: center;
            margin-bottom: 0 !important;
            font-size: 12px;
        }

        :root[data-theme="dark"] .mm-head,
        :root[data-theme="dark"] .mm-page .mm-search,
        :root[data-theme="dark"] .mm-card,
        :root[data-theme="dark"] .mm-empty,
        :root[data-theme="dark"] .mm-page .pagination-controls {
            background: #1e293b;
        }
        :root[data-theme="dark"] .mm-title,
        :root[data-theme="dark"] .mm-name,
        :root[data-theme="dark"] .mm-stat strong {
            color: #f8fafc !important;
        }
    }
</style>
<script>
    (function() {
        function esc(value) {
            return String(value == null ? '' : value).replace(/[&<>"']/g, function(ch) {
                return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[ch];
            });
        }

        function initials(name) {
            var parts = String(name || '').trim().split(/\s+/).filter(Boolean);
            if (!parts.length) return '?';
            return ((parts[0][0] || '') + (parts.length > 1 ? parts[parts.length - 1][0] : '')).toUpperCase();
        }

        function card(opts) {
            var ini = esc(initials(opts.title));
            var avatar = opts.image
                ? '<img src="' + esc(opts.image) + '" alt="" loading="lazy" onerror="this.parentNode.innerHTML=\'' + ini + '\'">'
                : ini;
            var stats = (opts.stats || []).map(function(s) {
                var value = s.html != null ? s.html : (s.value != null && String(s.value).trim() !== '' ? esc(s.value) : '—');
                return '<div class="mm-stat"><span>' + esc(s.label) + '</span><strong>' + value + '</strong></div>';
            }).join('');

            return '<div class="mm-card">' +
                '<div class="mm-avatar">' + avatar + '</div>' +
                '<div class="mm-main">' +
                    '<div class="mm-name">' + esc(opts.title) + '</div>' +
                    (opts.sub ? '<div class="mm-sub">' + esc(opts.sub) + '</div>' : '') +
                '</div>' +
                '<div class="mm-acts">' + (opts.actions || '') + '</div>' +
                (stats ? '<div class="mm-foot">' + stats + '</div>' : '') +
            '</div>';
        }

        function render(cards, emptyText, list) {
            var html = Array.isArray(cards) ? cards.join('') : (cards || '');
            $(list || '#mmCardList').html(html || '<div class="mm-empty"><i class="fas fa-inbox"></i><span>' + esc(emptyText || 'No records found') + '</span></div>');
            var total = parseInt($('#pagination-total').text(), 10);
            $('#mmCount').text(html && !isNaN(total) ? total + (total === 1 ? ' item' : ' items') : '');
        }

        window.MasterMobileUI = { esc: esc, card: card, render: render };

        $(document).on('click', '[data-mm-proxy]', function(e) {
            e.preventDefault();
            $($(this).data('mm-proxy')).trigger('click');
        });
    })();
</script>
