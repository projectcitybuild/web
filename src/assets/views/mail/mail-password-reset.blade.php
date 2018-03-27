<html>
<head>
    <style>
        @import url('https://fonts.googleapis.com/css?family=Fjalla+One|Rubik');

        * {
            box-sizing: border-box;
        }

        body {
            background: #fffcdb;
            padding: 2em;
            text-align: center;
            font-family: 'Rubik', sans-serif;
            font-size: 16px;
        }

        .logo {
            width: 75%;
            max-width: 350px;
        }

        h1 {
            font-family: 'Fjalla One', sans-serif;
            text-transform: uppercase;
            margin-top: 0;
        }

        .box {
            width: 100%;
            background: #fff;
            padding: 2em;
            margin: 2em 0;
            border-radius: 5px;
            border-bottom: 3px solid;
            -webkit-box-shadow: 0 2px 5px 1px rgba(0, 0, 0, 0.05);
                    box-shadow: 0 2px 5px 1px rgba(0, 0, 0, 0.05);
            border-color: #e6e5e5;
        }

        .button {
            display: inline-block;
            padding: 0.75em 2em;
            font-weight: bold;
            background: #1887dc;
            color: #fff;
            border-radius: 5px;
            border-bottom: 4px solid #136bae;
            text-transform: uppercase;
        }
    </style>
</head>
<body>
    <a href="https://projectcitybuild.com">
        <img class="logo" src="https://projectcitybuild.com/assets/images/logo.png" alt="Project City Build" />
    </a>
    <div class="box">
        <h1>Password Recovery</h1>

        <p>You or someone else has requested for your password to be reset. Use the below link if you wish to proceed.</p>
        <a class="button" href="{{ $url }}">Reset Your Password</a>
        <p>If you did not request this, you can safely ignore this email.</p>

    </div>

    <div class="footer">
        <p>Please note that this link will <strong>expire in 20 minutes</strong>.</p>
    </div>
</body>
</html>