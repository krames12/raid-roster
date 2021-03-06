@extends('layouts.app')

@section('content')
    <h1>{{ $roster->name }}
        @can('update-roster', $roster)
            <a href="{{ route('editRoster', $roster->id) }}" class="px-2 py-1 text-base align-middle">
                <i class="edit-icon far fa-edit"></i>
            </a>
        @endcan
        <form
            action="/rosters/{{ $roster->id }}/update"
            method="POST"
            class="inline-block"
            onSubmit="awaitResponse('Updating')"
        >
            {{ csrf_field() }}
            {{ method_field("PATCH") }}
            <button
                    type="submit"
                    class="text-base align-middle btn border bg-blue hover:bg-blue-dark text-white rounded"
                    id="await-request-button"
            >
                <i class="fas fa-sync-alt text-white"></i>
                Update Characters
            </button>
        </form>
        @can('update-roster', $roster)
            <div class="float-right text-right">
                <p class="text-base text-grey-darkest cursor-pointer select-none p-2" id="import-dropdown">Import <i class="icon text-grey-darkest fas fa-caret-down"></i></p>
                <div class="import-dropdown-menu bg-white border rounded hidden" id="import-dropdown-menu">
                    <a href="{{ route('importGuild', $roster->id) }}" class="dropdown-item block text-base text-right px-3 py-2">Guild</a>
                    <a href="{{ route('importCharacter', $roster->id) }}" class="dropdown-item block text-base text-right px-3 py-2">Character</a>
                </div>
            </div>
        @endcan
    </h1>
    <p>Guild: {{ $roster->guild_name }}</p>
    <p>Realm: {{ $roster->realm->name }}</p>
    <form action="/rosters/{{ $roster->id }}" method="POST" class="inline">
        {{ csrf_field() }}
        {{ method_field("DELETE") }}
        <button type='submit' name="" class="px-2 py-1 border rounded btn bg-red-dark text-white hover:bg-red-darker">
            <i class="text-white far fa-trash-alt"></i> Delete
        </button>
    </form>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-2 col-sm-hidden"></div>
            <div class="col-md-5 col-sm-12 mb-3">
                <h3>Tanks</h3>
                <div class="main-spec-box">
                    @if(count($tanks))
                        @foreach($tanks as $tank)
                            @php
                                $className = App\Http\Controllers\Lookups::classLookup($tank->class);
                            @endphp
                            <p class="pl-4 py-1">
                                <img src="{{ asset('images').'/'.$className.'.png' }}"
                                     alt="{{ $className }}"
                                     class="class-icon-small px-1"
                                >
                                <span class="inline-block w-1/6">{{ $tank->name }}</span>
                                <span class="character-enchants pl-4 py-1 inline-block w-4/6">
                                    @foreach($tank->enchantedGear as $item)
                                        @php
                                            $bonusText = "";
                                            $bonuses = json_decode($item->bonus_ids);
                                            foreach($bonuses as $bonus) {
                                                $bonusText .= $bonus.":";
                                            }
                                            $bonusText = substr($bonusText, 0, -1);
                                        @endphp
                                        <a href="#"
                                           class="enchant
                                                 {{ \App\Http\Controllers\Helpers::isItemEnchanted($item, $tank->class, $tank->talent_spec) ?
                                                 "all-clear" : "issue" }}
                                               "
                                           data-wowhead="item={{ $item->blizz_id }}
                                                {{ $item->socket->spell_id != 0 ? "&gems=".$item->socket->spell_id : "" }}
                                                {{ $item->enchant->spell_id != 0 ? "&ench=".$item->enchant->spell_id : "" }}
                                                &bonus={{ $bonusText }}"
                                        ></a>
                                    @endforeach
                                </span>
                            </p>
                        @endforeach
                    @else
                        <p class="px-4">No Tanks</p>
                    @endif
                </div>
            </div>
            <div class="col-md-5 col-sm-12 mb-3">
                <h3>Healers</h3>
                <div class="main-spec-box">
                    @if(count($healers))
                        @foreach($healers as $healer)
                            @php
                                $className = App\Http\Controllers\Lookups::classLookup($healer->class);
                            @endphp
                            <p class="pl-4 py-1">
                                <img src="{{ asset('images').'/'.$className.'.png' }}"
                                     alt="{{ $className }}"
                                     class="class-icon-small px-1"
                                >
                                <span class="inline-block w-1/6">{{ $healer->name }}</span>
                                <span class="character-enchants pl-4 py-1 inline-block w-4/6">
                                    @foreach($healer->enchantedGear as $item)
                                        @php
                                            $bonusText = "";
                                            $bonuses = json_decode($item->bonus_ids);
                                            foreach($bonuses as $bonus) {
                                                $bonusText .= $bonus.":";
                                            }
                                            $bonusText = substr($bonusText, 0, -1);
                                        @endphp
                                        <a href="#"
                                           class="enchant
                                                 {{ \App\Http\Controllers\Helpers::isItemEnchanted($item, $tank->class, $tank->talent_spec) ?
                                                 "all-clear" : "issue" }}
                                               "
                                           data-wowhead="item={{ $item->blizz_id }}
                                           {{ $item->socket->spell_id != 0 ? "&gems=".$item->socket->spell_id : "" }}
                                           {{ $item->enchant->spell_id != 0 ? "&ench=".$item->enchant->spell_id : "" }}
                                               &bonus={{ $bonusText }}"
                                        ></a>
                                    @endforeach
                                </span>
                            </p>
                        @endforeach
                    @else
                        <p class="px-4">No Healers</p>
                    @endif
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-2 col-sm-hidden"></div>
            <div class="col-md-5 col-sm-12 mb-3">
                <h3>Melee DPS</h3>
                <div class="main-spec-box">
                    @if(count($melee))
                        @foreach($melee as $melee)
                            @php
                                $className = App\Http\Controllers\Lookups::classLookup($melee->class);
                            @endphp
                            <p class="pl-4 py-1">
                                <img src="{{ asset('images').'/'.$className.'.png' }}"
                                     alt="{{ $className }}"
                                     class="class-icon-small px-1"
                                >
                                <span class="inline-block w-1/6">{{ $melee->name }}</span>
                                <span class="character-enchants pl-4 py-1 inline-block w-4/6">
                                    @foreach($melee->enchantedGear as $item)
                                        @php
                                            $bonusText = "";
                                            $bonuses = json_decode($item->bonus_ids);
                                            foreach($bonuses as $bonus) {
                                                $bonusText .= $bonus.":";
                                            }
                                            $bonusText = substr($bonusText, 0, -1);
                                        @endphp
                                        <a href="#"
                                           class="enchant
                                                 {{ \App\Http\Controllers\Helpers::isItemEnchanted($item, $tank->class, $tank->talent_spec) ?
                                                 "all-clear" : "issue" }}
                                               "
                                           data-wowhead="item={{ $item->blizz_id }}
                                           {{ $item->socket->spell_id != 0 ? "&gems=".$item->socket->spell_id : "" }}
                                           {{ $item->enchant->spell_id != 0 ? "&ench=".$item->enchant->spell_id : "" }}
                                               &bonus={{ $bonusText }}"
                                        ></a>
                                    @endforeach
                                </span>
                            </p>
                        @endforeach
                    @else
                        <p class="px-4">No Melee</p>
                    @endif
                </div>
            </div>
            <div class="col-md-5 col-sm-12 mb-3">
                <h3>Ranged DPS</h3>
                <div class="main-spec-box">
                    @if(count($ranged))
                        @foreach($ranged as $ranged)
                            @php
                                $className = App\Http\Controllers\Lookups::classLookup($ranged->class);
                            @endphp
                            <p class="pl-4 py-1">
                                <img src="{{ asset('images').'/'.$className.'.png' }}"
                                     alt="{{ $className }}"
                                     class="class-icon-small px-1"
                                >
                                <span class="inline-block w-1/6">{{ $ranged->name }}</span>
                                <span class="character-enchants pl-4 py-1 inline-block w-4/6">
                                    @foreach($ranged->enchantedGear as $item)
                                        @php
                                            $bonusText = "";
                                            $bonuses = json_decode($item->bonus_ids);
                                            foreach($bonuses as $bonus) {
                                                $bonusText .= $bonus.":";
                                            }
                                            $bonusText = substr($bonusText, 0, -1);
                                        @endphp
                                        <a href="#"
                                           class="enchant
                                                 {{ \App\Http\Controllers\Helpers::isItemEnchanted($item, $tank->class, $tank->talent_spec) ?
                                                 "all-clear" : "issue" }}
                                               "
                                           data-wowhead="item={{ $item->blizz_id }}
                                           {{ $item->socket->spell_id != 0 ? "&gems=".$item->socket->spell_id : "" }}
                                           {{ $item->enchant->spell_id != 0 ? "&ench=".$item->enchant->spell_id : "" }}
                                               &bonus={{ $bonusText }}"
                                        ></a>
                                    @endforeach
                                </span>
                            </p>
                        @endforeach
                    @else
                        <p class="px-4">No Ranged</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <hr>
    <div class="container">
        @if(count($roster->characters))
            @can('update-roster', $roster)
            <form method="POST" action="/rosters/{{ $roster->id }}/roles">
                {{ csrf_field() }}
                {{ method_field("PATCH") }}

                <input type="hidden" name="rosterId" value="{{ $roster->ids }}">
            @endcan
                <div class="import-guild-members-list mb-4">
                    <table class="roster-members-table mx-auto">
                        <thead>
                        <tr>
                            <th>Character</th>
                            <th>Main Spec</th>
                            @can('update-roster', $roster)
                                <th>Remove</th>
                            @endcan
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($roster->characters as $character)
                            <tr>
                                @php
                                    $className = App\Http\Controllers\Lookups::classLookup($character->class);
                                @endphp
                                <td class="leading-normal border-b py-1 px-2">
                                    <input type="hidden" name="characters[{{ $character->id }}][id]" value="{{ $character->id }}">
                                    <img src="{{ asset('images').'/'.$className.'.png' }}"
                                         alt="{{ $className}}"
                                         class="class-icon-small px-1"
                                    >
                                    <span class="">{{ $character->name }}</span>
                                </td>
                                <td class="leading-normal border-b py-1 px-2">
                                    <select name="characters[{{ $character->id }}][main_spec]"
                                            id="main-spec-select"
                                            class="bg-white border rounded px-1 py-1"
                                            {{ Auth::check() && Auth::user()->can('update-roster', $roster) ? "" : 'disabled="disabled"' }}
                                    >
                                        <option value="unassigned" {{ $character->pivot->main_spec == "unassigned" ? "selected" : "" }}>None</option>
                                        <option value="tank" {{ $character->pivot->main_spec == "tank" ? 'selected="selected"' : "" }}>Tank</option>
                                        <option value="healer" {{ $character->pivot->main_spec == "healer" ? 'selected="selected"' : "" }}>Healer</option>
                                        <option value="rdps" {{ $character->pivot->main_spec == "rdps" ? 'selected="selected"' : "" }}>Ranged DPS</option>
                                        <option value="mdps" {{ $character->pivot->main_spec == "mdps" ? 'selected="selected"' : "" }}>Melee DPS</option>
                                    </select>
                                </td>
                                @if( Auth::check() && Auth::user()->can('update-roster', $roster))
                                    <td class="border-b py-1 px-2 text-center">
                                        <input type="checkbox" name="characters[{{ $character->id }}][remove]" value="remove" />
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @if(Auth::check() && Auth::user()->can('update-roster', $roster))
                    <button type="submit" name="updateRoles" class="block mx-auto btn bg-blue hover:bg-blue-darker text-white rounded px-2 py-2">Update Roles</button>
            </form>
            @endif
        @else
            <div class="text-center">
                <h4>There are no characters assigned to this team.</h4>
            </div>
        @endif
    </div>

    <script src="/js/buttonDisableOnApiRequest.js"></script>
    <script>
        document.getElementById('import-dropdown').addEventListener('click', event => {
            document.getElementById('import-dropdown-menu').classList.toggle('hidden');
        });
    </script>

@endsection
