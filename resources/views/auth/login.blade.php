@extends('layouts.auth')


@section('content')
    <section class="container mx-auto px-2 md:px-0">
        <div class="flex w-full justify-center">
            <article class="w-full md:w-5/12 pt-20">
                <form action="{{ route('login') }}" method="POST" class="my-shadow bg-white w-full p-3 rounded space-y-4">
                    <h1 class="flex w-full justify-center text-xl font-semibold text-gray-700">Login</h1>
                    <div>
                        <label for="" class="text-gray-800 @error('asbs') text-red-500 @enderror">ASBS Number</label>
                        <input type="text" name="asbs" placeholder="Enter asb number"
                            class="w-full outline-none p-2 border  border-gray-400  hover:border-gray-500 @error('asbs') border-red-500 hover:border-600 @enderror rounded" />
                        @error('asbs')
                            <small class="text-red-600">{{ $message }}</small>
                        @enderror
                    </div>

                    <div>
                        <label for="" class="text-gray-800 @error('email') text-red-500 @enderror">Email</label>
                        <input type="email" name="email" placeholder="Enter your email"
                            class="w-full outline-none p-2 border border-gray-400 hover:border-gray-500 @error('email') border-red-500 hover:border-600 @enderror rounded" />
                        @error('email')
                            <small class="text-red-600">{{ $message }}</small>
                        @enderror
                    </div>


                    <div>
                        <label for="" class="text-gray-800 @error('password') text-red-500 @enderror">ASBS Number</label>
                        <input type="password" name="password" placeholder="**********"
                            class="w-full outline-none p-2 border border-gray-400  hover:border-gray-500  @error('password') border-red-500 hover:border-600 @enderror rounded" />
                        @error('password')
                            <small class="text-red-600">{{ $message }}</small>
                        @enderror
                    </div>


                    <div class="py-4 flex justify-end">
                        <button
                            class="bg-green-600 hover:bg-green-700 rounded px-3 text-white font-semibold py-1">Login</button>
                    </div>

                    @csrf
                </form>
            </article>
        </div>
    </section>
@endsection
