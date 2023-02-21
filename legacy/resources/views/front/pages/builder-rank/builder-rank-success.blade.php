@extends('front.templates.master')

@section('title', 'Builder Rank Application')
@section('body')
    <main class="page login">
        <section class="login__dialog login__dialog--is-narrow">
            <h1>Application Submitted</h1>
            <p>
                Your builder application has been submitted and will be reviewed shortly.
            </p>
            <p>
                You can track the progress of your application at any time <a href="{{ route('front.rank-up.status', $application->getKey()) }}">here</a>.
            </p>
        </section>
    </main>
@endsection
