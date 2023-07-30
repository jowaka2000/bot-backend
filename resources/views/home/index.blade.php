@extends('layouts.app')


@section('content')
    <div class="container mx-auto py-4">

        <div class="flex justify-center">
            <article class="w-5/12 border p-2">
                <h1 class="text-lg font-bold py-4">System Details</h1>
                @if (session()->has('message'))
                    <div class="flex justify-center">{{ session('message') }}</div>
                @endif
                <form action="{{ route('settings.facebook-profile') }}" method="POST">
                    <div>
                        <label>Admin Profile Link</label>
                        <input type="text" name="profile_link" placeholder="link"
                            class="w-full p-2 outline-none border border-gray-300 rounded" />

                        @error('profile_link')
                            <small class="text-red-400">{{ $message }}</small>
                        @enderror
                    </div>

                    <div>
                        <label>Admin Profile Name</label>
                        <input type="text" name="profile_name" placeholder="name"
                            class="w-full p-2 outline-none border border-gray-300 rounded" />
                        @error('profile_name')
                            <small class="text-red-400">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="py-4">
                        <button class="bg-green-600 px-2 py-1 text-white rounded">Submit</button>
                    </div>

                    @csrf
                </form>
            </article>

        </div>

        <article class="flex justify-center">
            <div>
                @if ($setting)
                <div>Profile Link: <b>{{$setting->facebook_profile_link}}</b></div>
                <div>Profile Name: <b>{{$setting->facebook_profile_names}}</b></div>
                @else
                <div>No Profile menu</div>
                @endif
            </div>
        </article>

    </div>
@endsection
