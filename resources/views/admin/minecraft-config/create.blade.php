@extends('admin.layouts.admin')

@section('title', 'Minecraft Config')

@section('body')
    <div class="row">
        <div class="col-md-8">
            @include('admin._errors')

            <form action="{{ route('front.panel.minecraft.config.update') }}" method="post">
                @csrf
                @method('patch')

                <div class="row mb-3">
                    <label for="game_type" class="col-sm-3 col-form-label horizontal-label">Current version</label>
                    <div class="col-sm-9">
                        {{ $config->version }}
                    </div>
                </div>
                <div class="row mb-3">
                    <label for="game_type" class="col-sm-3 col-form-label horizontal-label">Config (JSON)</label>
                    <div class="col-sm-9">
                        <textarea name="config" style="width: 100%; height: 500px">{{ trim(old('config') ?? json_encode($config->config, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)) }}</textarea>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-9 ms-auto">
                        Pressing Update will save the JSON as a new version. The new config will be immediately sent to the Minecraft server
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-9 ms-auto">
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
