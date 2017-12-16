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
                background: #fff;
            }

            h1 {
                margin-top: 1em;
            }

            .card,
            .progressbar,
            .button,
            .alert {
                margin-bottom: 1em
            }

        </style>
    </head>
    <body>

        <div class="container">
            <h1>Overview of PCB's style</h1>
            <p>
                This time we're trying for a brighter, more toony style, where components look almost like they're blocks made of matte plastic.
                This page is merely a showcase of the components created so far that, when combined, make up PCB's design.
            </p>

            <h1>Header 1</h1>
            <h2>Header 2</h2>
            <h3>Header 3</h3>
            <h4>Header 4</h4>
            <h5>Header 5</h5>
            <h6>Header 6</h6>

            <h1>Typography</h1>
            <p>
                Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec posuere tincidunt felis, at rutrum tortor. Donec at cursus nulla. Curabitur tellus magna, consequat ut feugiat a, viverra ac tortor. Quisque sed pharetra orci. Nam a lorem quis magna mattis pretium. Curabitur consectetur nibh sed eros pulvinar, non mollis nulla feugiat. Sed fringilla dapibus mollis.
            </p>
            <p>
                Sed a urna tempus, pharetra ante sed, suscipit felis. Nulla condimentum consequat ante finibus cursus. Vestibulum sodales a felis vel eleifend. Nam dignissim, magna in lacinia venenatis, turpis sapien faucibus nulla, id euismod urna urna id turpis.
                <br />
                <br />
                Text with <strong>emphasis</strong>.<br />
                Text with <i>italics</i>.<br />
                Text with a <a href="#">link</a> to somewhere.<br />
                <small>Fine print text</small><br />
            </p>

            <h1>Blockquotes</h1>
            <blockquote>
                Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec posuere tincidunt felis, at rutrum tortor. Donec at cursus nulla.
            </blockquote>


            <h1>Badges</h1>
            <span class="badge">Basic</span>
            <span class="badge primary">Primary</span>
            <span class="badge secondary">Secondary</span>
            <span class="badge accent">Accent</span>

            <h1>Cards</h1>
            <div class="card">
                <div class="card-body">
                    Basic (card)
                </div>
            </div>

            <div class="card primary">
                <div class="card-body">
                    Primary (card.primary)
                </div>
            </div>

            <div class="card secondary">
                 <div class="card-body">
                    Secondary (card.secondary)
                </div>
            </div>

            <div class="card accent">
                 <div class="card-body">
                    Accent (card.accent)
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    Header
                </div>
                <div class="card-body">
                    Basic (card)
                </div>
            </div>

            <div class="card primary">
                <div class="card-header">
                    Header
                </div>
                <div class="card-body">
                    Primary (card.primary)
                </div>
            </div>

            <div class="card secondary">
                <div class="card-header">
                    Header
                </div>
                <div class="card-body">
                    Secondary (card.secondary)
                </div>
            </div>

            <div class="card accent">
                <div class="card-header">
                    Header
                </div>
                <div class="card-body">
                    Accent (card.accent)
                </div>
            </div>

            <h1>Alerts</h1>
            <div class="alert warning">
                <h3><i class="fas fa-exclamation-circle"></i> Warning</h3>
                <p>You have <a href="#">5 unchecked</a> player reports.</p>
            </div>
            <div class="alert error">
                <h3><i class="fas fa-exclamation-circle"></i> Error</h3>
                <p>Image size exceeded the limit. Please upload an image less than <strong>1mb</strong>.</p>
            </div>
            <div class="alert success">
                <h3><i class="fas fa-check"></i> Success</h3>
                <p>Ban appeal submitted succesfully!</p>
            </div>

            <h1>Progress Bars</h1>
            <div class="card primary">
                <div class="card-body">

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
            </div>
            

            <h1>Buttons</h1>
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
        
        </div>

        <script src="{{ mix('assets/js/app.js') }}"></script>
        
    </body>
</html>