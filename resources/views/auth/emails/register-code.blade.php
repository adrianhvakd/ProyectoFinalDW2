<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Código de Confirmación</title>

    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f4f6f8;
            font-family: Arial, Helvetica, sans-serif;
            color: #333333;
        }

        .email-wrapper {
            width: 100%;
            padding: 30px 0;
        }

        .email-container {
            max-width: 520px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        .email-header {
            background-color: #605DFF;
            padding: 20px;
            text-align: center;
        }

        .email-header img {
            max-width: 120px;
        }

        .email-body {
            padding: 30px;
            text-align: center;
        }

        .email-body h2 {
            margin-top: 0;
            color: #111827;
        }

        .email-body p {
            font-size: 14px;
            line-height: 1.6;
            margin: 12px 0;
        }

        .code-box {
            margin: 25px 0;
            padding: 15px 0;
            background-color: #f0f4ff;
            border: 2px dashed #605DFF;
            border-radius: 8px;
        }

        .code {
            font-size: 32px;
            font-weight: bold;
            letter-spacing: 6px;
            color: #605DFF;
        }

        .email-footer {
            padding: 15px;
            font-size: 12px;
            text-align: center;
            color: #6b7280;
            background-color: #f9fafb;
        }

        .title {
            color: #f9fafb;
        }
    </style>
</head>

<body>
    <div class="email-wrapper">
        <div class="email-container">

            <div class="email-header">
                <h1 class="title">NormFlow</h1>
            </div>

            <div class="email-body">
                <h2>Confirmación de Registro</h2>

                <p>
                    Gracias por registrarte en nuestra plataforma.
                </p>

                <p>
                    Usa el siguiente código para completar tu registro:
                </p>

                <div class="code-box">
                    <div class="code">{{ $code }}</div>
                </div>

                <p>
                    Este código expirará en unos minutos.<br>
                    Si no solicitaste este registro, puedes ignorar este correo.
                </p>
            </div>

            <div class="email-footer">
                © {{ date('Y') }} NormFlow · No respondas este correo
            </div>

        </div>
    </div>
</body>

</html>
