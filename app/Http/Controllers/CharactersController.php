<?php

namespace App\Http\Controllers;

use App\Character;
use App\CharacterGear;
use App\Realm;
use App\Roster;

use App\Http\Controllers\Lookups;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\RequestException;

class CharactersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Import single character page
     * @return \Illuminate\Http\Response
     */
    public function import(Roster $roster) {
        $realms = \App\Realm::where('region', 'us')->get();;

        return view('characters.import', compact(['realms', 'roster']));
    }

    /**
     * Import character from Blizzard API
     */
    public function importCharacter(Roster $roster)
    {
        $this->validate(request(), [
            'name'  => 'required',
            'realm' => 'required'
        ]);

        $realm = Realm::find(request('realm'));
        $character = Lookups::apiCharacter(request('name'), $realm->slug);
        if(isset($character->name)) {
            $importedCharacter = $this::handleCharacterImport($character, request('realm'));
            $roster->characters()->attach($importedCharacter, ['main_spec' => 'unassigned', 'off_spec' => 'unassigned']);

            return redirect("/rosters/$roster->id")->with('success', 'Character has been imported');
        } else {
            return back()->with('error', 'Character does not exist');
        }
    }

    /**
     * Creates character and character item records
    */
    public static function handleCharacterImport($character, $realmId) {
        $characterExists = Character::where([['name', '=', $character->name], ['realm', '=', $realmId]])->first();
        if($characterExists === null) {
            $newCharacter = self::createCharacter($character, $realmId);
            return $newCharacter;
        } else {
            $existingCharacter = Character::find($characterExists->id);
            $updatedCharacter = self::updateCharacter($character, $existingCharacter);
            return $updatedCharacter;
        }
    }

    private static function createCharacter($character, $realmId)
    {
        $newCharacter = new Character();

        $newCharacter->name = htmlspecialchars($character->name);
        $newCharacter->realm = $realmId;
        $newCharacter->class = $character->class;
        $newCharacter->race = $character->race;
        $newCharacter->faction = $character->faction;
        $newCharacter->item_level = $character->items->averageItemLevel;

        // Save Character
        $newCharacter->save();

        foreach($character->items as $key => $item) {
            if($key == "averageItemLevelEquipped" || $key == "averageItemLevel") {
                continue;
            }
            $newItem = new CharacterGear();

            $newItem->blizz_id = $item->id;
            $newItem->character_id = $newCharacter->id;
            $newItem->item_slot = $key;
            $newItem->name = $item->name;
            $newItem->item_level = $item->itemLevel;

            if($key == "neck"){
                $characterInfo = Character::find($newCharacter->id);
                $characterInfo->azerite_level = $item->azeriteItem->azeriteLevel;
                $characterInfo->save();
            }

            $newItem->save();
        }

        return $newCharacter->id;
    }

    public static function updateCharacter($character, $existingCharacter)
    {
        foreach($character->items as $key => $item) {
            if($key == "averageItemLevelEquipped" || $key == "averageItemLevel") {
                continue;
            }
            $existingItem = CharacterGear::where([
                ['item_slot', '=', $key],
                ['character_id', '=', $existingCharacter->id]
            ])->first();

            $existingItem->blizz_id = $item->id;
            $existingItem->item_slot = $key;
            $existingItem->name = $item->name;
            $existingItem->item_level = $item->itemLevel;

            if($key == "neck"){
                $existingCharacter->azerite_level = $item->azeriteItem->azeriteLevel;
            }

            $existingItem->save();
        }

        $existingCharacter->item_level = $character->items->averageItemLevel;
        $existingCharacter->save();

        return $existingCharacter->id;
    }
}
