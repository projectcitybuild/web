<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="google-site-verification" content="Sp9E55tVkNph_ttvggLD52MY-ACeGfeivQbmWp7CWfo" />

        <title>Stylesheet</title>

        <link rel="stylesheet" type="text/css" href="{{ mix('assets/css/app.css') }}" />

        <script src="https://use.fontawesome.com/releases/v5.0.1/js/brands.js"></script>
        <script src="https://use.fontawesome.com/releases/v5.0.1/js/solid.js"></script>
        <!-- <script src="https://use.fontawesome.com/releases/v5.0.1/js/regular.js"></script> -->
        <script src="https://use.fontawesome.com/releases/v5.0.1/js/fontawesome.js"></script>

        <style>
            body {
                padding: 1em;
            }

            .card,
            .progressbar,
            .button {
                margin-bottom: 0.5em
            }

        </style>
    </head>
    <body>
        
        Headers
        <h1>Header 1</h1>
        <h2>Header 2</h2>
        <h3>Header 3</h3>
        <h4>Header 4</h4>
        <h5>Header 5</h5>
        <h6>Header 6</h6>

        Cards
        <div class="card">
            Basic (card)
        </div>

        <div class="card primary">
            Primary (card.primary)
        </div>

        <div class="card secondary">
            Secondary (card.secondary)
        </div>

        <div class="card accent">
            Accent (card.accent)
        </div>

        <div class="card centered">
            Centered (card.centered)
        </div>

        Progress Bars
        <div class="card primary">

            <div class="progressbar primary">
                <div class="outer">
                    <div class="inner" style="width:25%"></div>
                </div>
            </div>
            
            <div class="progressbar secondary">
                <div class="outer">
                    <div class="inner" style="width:25%"></div>
                </div>
            </div>

            <div class="progressbar accent small">
                <div class="outer">
                    <div class="inner" style="width:25%"></div>
                </div>
            </div>

            <div class="progressbar secondary bordered">
                <div class="outer">
                    <div class="inner" style="width:25%"></div>
                </div>
            </div>

            <div class="progressbar accent">
                <div class="outer">
                    <div class="inner" style="width:66%">66%</div>
                    <div class="label">33%</div>
                </div>
            </div>
            
            <div class="progressbar accent">
                <div class="outer">
                    <div class="inner" style="width:25%"></div>
                </div>
                <div class="markers">
                    <span>0</span>
                    <span>250</span>
                    <span>500</span>
                    <span>750</span>
                    <span>1000</span>
                </div>
            </div>

        </div>
        

        Buttons
        <a class="button primary" href="#">Link Button</a>
        <a class="button secondary" href="#">Link Button</a>
        <a class="button accent" href="#">Link Button</a>

        <a class="button accent" href="#">
            <i class="fas fa-lock"></i>
            Link with Icon
        </a>

        <a class="button primary disabled" href="#">Disabled Button</a>
        <a class="button secondary disabled" href="#">Disabled Button</a>
        <a class="button accent disabled" href="#">Disabled Button</a>
        
        <a class="button accent large" href="#">Large Button</a>

        <a class="button secondary fill" href="#">Fill Button</a>
        

        <script src="{{ mix('assets/js/app.js') }}"></script>
        
    </body>
</html>