<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Bon</title>

    <style>
        @font-face {
            font-family: 'Roboto-Mono';
            src: url("{{ $font_path }}/fonts/roboto-mono-normal.ttf") format("truetype");
        }

        @font-face {
            font-family: 'Roboto-Mono';
            src: url("{{ $font_path }}/fonts/roboto-mono-bold.ttf") format("truetype");
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

        hr {
            margin: 10px 0;
            border-style: dashed;
            border-color: #bcbbbe;
        }

        .align-center {
            text-align: center !important;
        }

        .align-right {
            text-align: right !important;
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
        }

        td.price {
            vertical-align: top;
        }
    </style>
</head>

<body>
    {!! $receipt !!}
</body>

</html>
