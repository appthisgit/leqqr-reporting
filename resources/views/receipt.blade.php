<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Bon</title>


    <style>
        * {
            padding: 0;
            margin: 0;
        }

        html {
            background-color: rgb(123, 132, 135);
        }
        
        body {
            background-color: #fff;
            margin: 20px auto;
            {!! $receipt_styles !!}
        }

        p, td {
            display: block;
            text-align: left;
            {!! $line_styles !!}
        }

        .centered {
            text-align: center !important;
        }

        .bolded {
            font-weight: bold;
        }

        .inverted {
            background-color: #000;
            color: #FFF;
        }

        .underlined {
            text-decoration: underline;
        }

        table {
            width: 100%;
            table-layout: fixed;
            border-spacing: 0;
            border-collapse: collapse;
        }

        td {
            width: auto; 
            display: table-cell;       
            white-space: pre;
            overflow: hidden;
        }
        td.price {
            {!! $price_styles !!}
        }
    </style>
</head>

<body>
    {!! $receipt !!}
</body>

</html>
