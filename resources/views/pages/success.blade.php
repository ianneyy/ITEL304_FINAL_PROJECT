<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Document</title>
    <link rel="stylesheet" href="style.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/5.3.45/css/materialdesignicons.css"
        integrity="sha256-NAxhqDvtY0l4xn+YVa6WjAcmd94NNfttjNsDmNatFVc=" crossorigin="anonymous" />
</head>
<style>
    body {
        display: flex;
        justify-content: center;
    }

    .container {
        position: absolute;
        height: auto;
        background-color: #d9d9d9;
        border-radius: 10px;
        margin-bottom: 100px;
        top: 100px;
        filter: drop-shadow(0px 4px 4px rgba(0, 0, 0, 0.2));
        transition: width 0.3s ease, left 0.3s ease;
        /* Smooth transition */

        font-family: "Signika", sans-serif;
    }

    .head {
        display: flex;
        justify-content: space-between;
    }

    .order-card {
        display: flex;
        justify-content: space-between;
    }

    .total {
        display: flex;
        justify-content: flex-end;
    }

    .order-title {
        display: flex;
        justify-content: space-between;
    }

    .qrcode {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        justify-content: center;
    }

    .qrcode a {
        text-decoration: none;
    }

    @media (max-width: 576px) {
        .qr-code-container svg {
            width: 300px;
        }
    }
</style>

<body>
    <div class="container col-xl-9 p-sm-5 p-3 mb-5 ">
        <div class=" student-info mb-5">
            <h5>{{ $data['getOrderId'][0]['full_name'] }}</h5>
            <h5>{{ $data['getOrderId'][0]['email'] }}</h5>
        </div>
        <div class="head">
            <p>Reservation ID: {{ $data['getOrderId'][0]['order_id'] }}</p>
            <p>Reservation Date: {{ $data['getOrderId'][0]['reservation_date'] }}</p>
        </div>
        <hr>
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="background-color: #f0f0f0;">
                    <th style="padding: 8px;">Type</th>
                    <th style="padding: 8px;">Variation</th>
                    <th style="padding: 8px;">Size</th>
                    <th style="padding: 8px;">Quantity</th>
                    <th style="padding: 8px;">Price</th>
                </tr>
            </thead>
            @foreach ($data['getOrderId'] as $order)
                <tbody>



                    <tr>
                        <td style="padding: 8px;">{{ $order['name'] }}</td>
                        <td style="padding: 8px;">{{ $order['department'] }}</td>
                        <td style="padding: 8px;">{{ $order['size'] }}</td>
                        <td style="padding: 8px;">{{ $order['qty'] }}</td>
                        <td style="padding: 8px;">&#8369;{{ $order['subTotal'] }}</td>
                    </tr>

                </tbody>
            @endforeach
        </table>
        <hr>
        <div class="ttl">
            <p class="total">Total: &#8369;{{ $data['getOrderId'][0]['total_price'] }}</p>
        </div>
        <div class="qrcode mb-5">
            <h3>Present this QR code to BAO for verification.</h3>
            <div class="qr-code-container">
                <img src="{{ asset($data['qrCodePath'][0]['qrcode']) }}" class="img-fluid" alt="QR Code">
            </div>
            <div class="back mt-5">
                <a href="{{ url('student/uniforms') }}" class="btn btn-link text-muted">
                    <i class="mdi mdi-arrow-left me-1"></i> Back to Uniforms List</a>
            </div>
        </div>
    </div>
</body>

</html>
