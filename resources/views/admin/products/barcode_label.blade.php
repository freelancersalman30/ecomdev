<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barcode Label - {{ $product->sku }}</title>
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
    <style>
        @page {
            size: 50mm 30mm;
            margin: 0;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }
        body {
            background-color: #f1f5f9;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }
        .controls {
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
        }
        .btn {
            background: #0f172a;
            color: #ffffff;
            border: none;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn-success {
            background: #10b981;
        }
        .label-container {
            width: 50mm;
            height: 30mm;
            background: #ffffff;
            border: 1px dashed #cbd5e1;
            padding: 2mm;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
            text-align: center;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
        }
        .brand-header {
            font-size: 7pt;
            font-weight: 800;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            color: #0f172a;
            line-height: 1;
        }
        .product-name {
            font-size: 7.5pt;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.1;
            max-height: 2.2em;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            margin: 1px 0;
        }
        .barcode-svg {
            max-width: 95%;
            height: 11mm !important;
            margin: 0 auto;
        }
        .footer-info {
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            font-size: 7pt;
            font-weight: 700;
            line-height: 1;
            padding-top: 1px;
            border-top: 0.5px solid #e2e8f0;
        }
        .sku {
            font-family: monospace;
            color: #475569;
        }
        .price {
            font-size: 8.5pt;
            font-weight: 900;
            color: #059669;
        }

        @media print {
            body {
                background: none;
                padding: 0;
                margin: 0;
                display: block;
            }
            .controls {
                display: none !important;
            }
            .label-container {
                border: none;
                box-shadow: none;
                width: 50mm;
                height: 30mm;
                page-break-after: always;
            }
        }
    </style>
</head>
<body>

    <div class="controls">
        <button onclick="window.print()" class="btn btn-success">
            🖨️ Print Label Sticker
        </button>
        <button onclick="window.close()" class="btn">
            ✕ Close
        </button>
    </div>

    <div class="label-container">
        <div class="brand-header">DREAMERS PCB &bull; HARDWARE</div>
        <div class="product-name">{{ $product->name }}</div>
        
        <svg id="barcode" class="barcode-svg"></svg>

        <div class="footer-info">
            <span class="sku">{{ $product->sku }}</span>
            <span class="price">৳{{ number_format($product->selling_price, 0) }}</span>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const barcodeValue = "{{ $product->barcode ?: $product->sku }}";
            try {
                JsBarcode("#barcode", barcodeValue, {
                    format: "CODE128",
                    lineColor: "#000000",
                    width: 1.4,
                    height: 35,
                    displayValue: false,
                    margin: 0
                });
            } catch (e) {
                console.error("Barcode generation failed", e);
            }
        });
    </script>
</body>
</html>
