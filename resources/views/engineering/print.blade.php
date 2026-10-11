<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إفادة رسمية</title>

    <style>
        body {
            font-family: Tahoma;
            margin: 40px;
        }
        .header {
            text-align: center;
        }
        .header img {
            width: 120px;
        }
        .title {
            font-size: 22px;
            font-weight: bold;
            margin-top: 10px;
        }
        .content {
            margin-top: 40px;
            font-size: 18px;
            line-height: 2.3;
        }
        .footer {
            margin-top: 80px;
            display: flex;
            justify-content: space-between;
        }
    </style>
</head>

<body onload="window.print()">

<div class="header">
    <img src="{{ asset('assets/bg/logo-light.png') }}">
    <div class="title">بلدية جباليا النزلة</div>
    <div>دائرة الهندسة و التنظيم</div>
</div>

<div class="content">
    تشهد بلدية جباليا النزلة بأن المواطن
    <strong>{{ $engineering->citizen_name }}</strong>
    <br>

    رقم الملف:
    <strong>{{ $engineering->file_number }}</strong>
    <br>

    قد حصل على رخصة
    <strong>{{ $engineering->license_type }}</strong>
    <br>

    وذلك بتاريخ
    <strong>{{ $engineering->license_date }}</strong>
    <br><br>

    وقد أعطيت هذه الإفادة بناءً على طلبه دون أدنى مسؤولية على البلدية.
</div>

<div class="footer">
    <div>الموظف المختص<br>..................</div>
    <div>الختم والتوقيع<br>..................</div>
</div>

</body>
</html>
