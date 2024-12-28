<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{config('app.name')}}</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">

        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #454a4d;
                font-family: 'Nunito', sans-serif;
                font-weight: 200;
                height: 100vh;
                margin: 0;
            }

            h1, h2, h3 {
                text-transform: uppercase;
                margin-bottom: 5px;
                margin-top: 30px;
            }

            p {
                line-height: 145%;
                margin-bottom: 30px;
            }

            ul {
                list-style: none;
                padding-left: 0;
            }

            ul li {
                padding-bottom: 6px;
            }

            table {
                width: 90%;
                margin: 10px auto;
                border: 1px solid #ebeaea;
                color: black;
                border-radius: 10px;
            }

            th, td {
                padding: 20px 15px;
                text-align: left;
                margin: 0;
                border: 0;
            }

            tr {
                padding-left: 12px;
                padding-right: 12px;
            }

            .row-white {
                background-color: #fff;
            }

            .row-gray {
                background-color: rgb(243 244 246 );
            }

            pre {
                background-color: rgb(49, 72, 95);
                border-radius: 10px;
            }

            code {
                background-color: rgb(49, 72, 95);
                color: rgb(130, 170, 255);
                font-size: medium;
                line-height: 145%;
            }

            a {
                text-decoration: none;
                padding-bottom: 4px;
                font-weight: 400;
            }

            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
            }

            .title {
                font-size: 84px;
            }

            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 13px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
                display: inline-block;
            }

            .m-b-md {
                margin-bottom: 30px;
            }

            .text-black {
                color: rgba(0, 0, 0, 1);
            }

            .text-white {
                color: rgba(255, 255, 255, 1);
            }

            .text-gray {
                color: rgba(107, 114, 128, 1);
            }

            .text-red {
                color: rgba(239, 68, 68, 1);
            }

            .text-yellow {
                color: rgba(245, 158, 11, 1);
            }

            .text-green {
                color: rgba(16, 185, 129, 1);
            }

            .text-blue {
                color: rgba(59, 130, 246, 1);
            }

            .text-indigo {
                color: rgba(99, 102, 241, 1);
            }

            .text-purple {
                color: rgba(139, 92, 246, 1);
            }

            .text-pink {
                color: rgba(236, 72, 153, 1);
            }

            .container {
                margin-left: 9%;
                margin-right: 9%;
            }

            .pb{
                padding-bottom: 2rem;
            }

            .text-container {
                margin-left: 9%;
                margin-right: 18%;
            }

            @media only screen and (max-width: 400px) {
                .links > a {
                    display: block;
                    margin-top: 3%;
                }
            }
        </style>
    </head>
    <body>
        @yield('content')
    </body>
</html>
