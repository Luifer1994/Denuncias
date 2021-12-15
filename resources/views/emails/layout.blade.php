<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width">
    <title></title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
        }

    </style>
</head>

<body>
    <table style="width: 600px;">
        <thead>
            <tr>
                <th>
                    <table>
                        <tr>
                            <th
                                style="border-right-width: 0.5px;border-right-style: solid;border-right-color: orange;width: 300px;">
                                <img src="{{ $message->embed(public_path() . '/img/image001.png') }}" width="250" />

                            </th>

                            <th style="width: 300px;">
                                <img src="{{ $message->embed(public_path() . '/img/image002.png') }}" width="200" />
                            </th>
                        </tr>
                    </table>
                </th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <th>
                    <table>
                        @yield('content')
                    </table>
                </th>

            </tr>
        </tbody>
        <tfoot>
            <tr>
                <th>
                    <table>
                        <tr>
                            <th
                                style="width: 300px;font-family: Arial, Helvetica, sans-serif;font-size: 10px;color: gray;text-align: justify;padding-left: 10px;">
                                <p>Conmutador: (57 8) 513 1130 - Línea Verde: (57 8) 512 8272</p>
                                <p>Providencia: Sector Mountain. Teléfono: (57 8) 514 8552</p>
                                <p>Email: serviciocliente@coralina.gov.co </p>
                                <p>Twitter: @coralina_sai - Facebook: Coralina </p>
                            </th>

                            <th style="width: 300px;">
                                <img src="{{ $message->embed(public_path() . '/img/image004.png') }}" width="40" />
                                <img src="{{ $message->embed(public_path() . '/img/image003.png') }}" width="100" />
                            </th>
                        </tr>
                    </table>
                </th>
            </tr>

        </tfoot>
    </table>
    </div>
</body>
