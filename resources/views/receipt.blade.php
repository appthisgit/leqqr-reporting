<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Bon</title>

    <style>
        @font-face {
            font-family: 'Roboto-Mono';
            src: url("{{ storage_path('fonts/roboto-mono-normal.ttf') }}") format("truetype");
        }

        @font-face {
            font-family: 'Roboto-Mono';
            src: url("{{ storage_path('fonts/roboto-mono-bold.ttf') }}") format("truetype");
            font-weight: bold;
        }

        * {
            padding: 0;
            margin: 0;
        }

        body {
            background-color: #fff;
            {!! $receipt_styles !!}
        }

        p,
        td {
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
            /* table-layout: fixed; */
            border-spacing: 0;
            border-collapse: collapse;
        }

        td {
            /* border: 1px solid black; */
            display: table-cell;
            white-space: pre;
            {!! $table_cell_styles !!}
        }

        td.price {
            vertical-align: top;
            {!! $table_price_styles !!}
        }
    </style>
</head>

<body>
    {!! $receipt !!}
</body>

</html>
