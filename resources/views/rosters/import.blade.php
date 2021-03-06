@extends('layouts.app')

@section('content')
    <h1 class="mb-3">Import Guild</h1>
    @if(count($members))
    <form
        method="POST"
        {{ action("RosterController@importGuild", ['id' => $roster->id]) }}
        onSubmit="awaitResponse('Importing')"
    >
        {{ csrf_field() }}
        <ul class="px-1 import-guild-members-list mb-4">
            @foreach($members as $member)
                @php
                    $className = App\Http\Controllers\Lookups::classLookup($member->character->class);
                @endphp
                <li class="list-reset leading-normal border-b py-1 px-2">
                    <input type="checkbox" value="{{ $member->character->name }}" name="characters[]" />
                    <img src="{{ asset('images').'/'.$className.'.png' }}"
                         alt="{{ $className }}"
                         class="class-icon-small px-1"
                    >
                    {{ $member->character->name }}
                </li>
            @endforeach
        </ul>
        <button
            type="submit"
            class="btn bg-blue hover:bg-blue-darker text-white rounded px-4 py-2"
            id="await-request-button"
        >
            Import Characters
        </button>
    </form>
    @else
        <p>There are no characters in this guild. Please double check your team settings and try again.</p>
    @endif

    <script src="/js/buttonDisableOnApiRequest.js"></script>
@endsection
