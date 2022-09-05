<!DOCTYPE html>
<html>
    <head>
        <title>Internal Server Error</title>

        <link href="https://fonts.googleapis.com/css?family=Lato:100" rel="stylesheet" type="text/css">

        <style>
            html, body {
                height: 100%;
            }
            body {
                margin: 0;
                padding: 0;
                width: 100%;
                color: #B0BEC5;
                display: table;
                font-weight: 100;
                font-family: 'Lato';
            }
            .container {
                text-align: center;
                display: table-cell;
                vertical-align: middle;
            }
            .content {
                text-align: center;
                display: inline-block;
            }
            .title {
                font-size: 72px;
                margin-bottom: 40px;
            }
        </style>
    </head>
    <body>
        <div class="content">
            <div class="container">
                <div class="title">Something went wrong.</div>

                @if(app()->bound('sentry') && !empty(Sentry::getLastEventID()))
                    <div class="subtitle">Error ID: {{ Sentry::getLastEventID() }}</div>

                    <!-- Sentry JS SDK 2.1.+ required -->
                    <script src="https://cdn.ravenjs.com/3.3.0/raven.min.js"></script>

                    <script>
                        Raven.showReportDialog({
                            eventId: '{{ Sentry::getLastEventID() }}',
                            // use the public DSN (dont include your secret!)
                            dsn: 'https://99fe2a2574a948b8b594d05ea19ae972@sentry.io/1239355',
                            user: {
                                'name': '',
                                'email': '',
                            }
                        });
                    </script>
                @endif
            </div>
        </div>
    </body>
</html>