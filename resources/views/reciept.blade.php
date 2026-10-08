<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expense Receipt Analyzer</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f4f6f9;
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            width: 100%;
            max-width: 500px;
            background: #ffffff;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        }

        .title {
            text-align: center;
            font-size: 22px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 24px;
        }

        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .alert-success {
            background-color: #d1fae5;
            color: #065f46;
            border-left: 4px solid #10b981;
        }

        .alert-danger {
            background-color: #fee2e2;
            color: #991b1b;
            border-left: 4px solid #ef4444;
            list-style: none;
        }

        .upload-card {
            border: 2px dashed #cbd5e1;
            border-radius: 12px;
            padding: 24px;
            text-align: center;
            background-color: #f8fafc;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
        }

        .upload-card:hover {
            border-color: #4f46e5;
            background-color: #f0f3ff;
        }

        .upload-card i.upload-icon {
            font-size: 40px;
            color: #6366f1;
            margin-bottom: 10px;
        }

        .upload-card label {
            display: block;
            font-weight: 600;
            color: #475569;
            cursor: pointer;
        }

        .upload-card input[type="file"] {
            display: none;
        }

        .preview-box {
            margin-top: 15px;
            display: none;
            position: relative;
        }

        .preview-box img {
            width: 100%;
            max-height: 250px;
            object-fit: contain;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
        }

        .btn-submit {
            width: 100%;
            background-color: #4f46e5;
            color: #ffffff;
            padding: 12px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 20px;
            transition: background 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-submit:hover {
            background-color: #4338ca;
        }

        .btn-submit:disabled {
            background-color: #a5b4fc;
            cursor: not-allowed;
        }

        /* Receipt Info & Loading Box */
        .receipt-card {
            margin-top: 28px;
            padding: 20px;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
        }

        .receipt-card h3 {
            font-size: 16px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 8px;
        }

        .receipt-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            font-size: 14px;
            color: #334155;
        }

        .receipt-item strong {
            color: #0f172a;
        }

        /* Loading Spinner CSS */
        .loading-box {
            display: none;
            text-align: center;
            padding: 25px 15px;
            background-color: #f0f3ff;
            border: 1px dashed #6366f1;
            border-radius: 12px;
            margin-top: 28px;
            color: #4f46e5;
        }

        .loading-box i {
            font-size: 32px;
            margin-bottom: 10px;
        }

        .loading-box p {
            font-size: 15px;
            font-weight: 600;
        }
    </style>
</head>
<body>

    <div class="container">
        <h2 class="title"><i class="fa-solid fa-receipt"></i> Expense Receipt Analyzer</h2>

        @if(session('success'))
            <div class="alert alert-success">
                <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <ul class="alert alert-danger">
                @foreach($errors->all() as $error)
                    <li><i class="fa-solid fa-circle-exclamation"></i> {{ $error }}</li>
                @endforeach
            </ul>
        @endif

        <!-- Form -->
        <form action="" method="post" enctype="multipart/form-data" id="expense-form">
            @csrf

            <div class="upload-card" onclick="document.getElementById('receipt-image').click()">
                <i class="fa-solid fa-cloud-arrow-up upload-icon"></i>
                <label id="upload-label">Click to upload Receipt Image</label>
                <input type="file" name="image" id="receipt-image" accept="image/*" onchange="previewImage(event)">

                <div class="preview-box" id="preview-container">
                    <img id="image-preview" src="#" alt="Receipt Preview">
                </div>
            </div>

            <button type="submit" class="btn-submit" id="submit-btn">
                <i class="fa-solid fa-file-invoice-dollar"></i> Process Expense
            </button>
        </form>

        <!-- Processing State (Form submit hotey hi show hoga) -->
        <div class="loading-box" id="loading-box">
            <i class="fa-solid fa-circle-notch fa-spin"></i>
            <p>Processing Receipt with AI... Please wait</p>
        </div>

        <!-- Show Current Receipt Info (Server Response aane par show hoga) -->
        @if(isset($curexp) && $curexp)
            <div class="receipt-card" id="receipt-details">
                <h3><i class="fa-solid fa-circle-info"></i> Processed Receipt Info</h3>
                <div class="receipt-item">
                    <span><i class="fa-solid fa-store"></i> <strong>Mart Name:</strong></span>
                    <span>{{ $curexp->Mart_name ?? 'N/A' }}</span>
                </div>
                <div class="receipt-item">
                    <span><i class="fa-solid fa-calendar-day"></i> <strong>Date:</strong></span>
                    <span>{{ $curexp->Date_Expense ?? 'N/A' }}</span>
                </div>
                <div class="receipt-item">
                    <span><i class="fa-solid fa-money-bill-wave"></i> <strong>Total Amount:</strong></span>
                    <span><strong>{{ $curexp->total_amount ?? 'N/A' }}</strong></span>
                </div>
            </div>
        @endif
    </div>

    <script>
        // Image preview functionality
        function previewImage(event) {
            const input = event.target;
            const previewContainer = document.getElementById('preview-container');
            const previewImage = document.getElementById('image-preview');
            const uploadLabel = document.getElementById('upload-label');

            if (input.files && input.files[0]) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    previewContainer.style.display = 'block';
                    uploadLabel.innerText = input.files[0].name;
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        // Handle Form Submit Event for Loading State
        document.getElementById('expense-form').addEventListener('submit', function() {
            // Hide previous receipt details if present
            const receiptDetails = document.getElementById('receipt-details');
            if (receiptDetails) {
                receiptDetails.style.display = 'none';
            }

            // Show Processing/Loading State
            document.getElementById('loading-box').style.display = 'block';

            // Button ko disable karein taake double submit na ho
            const submitBtn = document.getElementById('submit-btn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Processing...';
        });
    </script>
</body>
</html>