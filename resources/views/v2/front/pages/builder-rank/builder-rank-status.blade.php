@extends('v2.front.templates.master')

@section('title', 'Builder Rank Application Status')

@section('body')
    <main class="page login">
        <div class="container">
            <div class="login__dialog login__register-form">
                <h1>Builder Rank Application</h1>

                Status: {{ \Domain\BuilderRankApplications\Entities\ApplicationStatus::tryFrom($application->status)->humanReadable() }}
                <br/><br/>

                <hr />

                <p>
                    <strong>Minecraft username:</strong> {{ $application->minecraft_alias }}
                </p>
                <p>
                    <strong>Current builder rank:</strong> {{ $application->current_builder_rank }}
                </p>
                <p>
                    <strong>Build location:</strong> {{ $application->build_location }}
                </p>
                <p>
                    <strong>Build description:</strong> {{ $application->build_description }}
                </p>
                <p>
                    <strong>Additional notes:</strong> {{ $application->additional_notes ?: 'None' }}
                </p>
                <p>
                    <strong>Created at:</strong> {{ $application->created_at }}
                </p>
            </div>
        </div>
    </main>
@endsection
