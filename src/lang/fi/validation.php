<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Syötteiden tarkistamisen kielitekstit
    |--------------------------------------------------------------------------
    |
    | Nämä ovat syötteiden tarkistajan oletusvirheilmoitukset. Joillakin
    | säännöillä, kuten kokoa koskevilla säännöillä, on useita versioita.
    | Tekstejä voi muuttaa sovelluksen tarpeiden mukaan.
    |
    */

    'accepted' => 'Kenttä :attribute on hyväksyttävä.',
    'accepted_if' => 'Kenttä :attribute on hyväksyttävä, kun kentän :other arvo on :value.',
    'active_url' => 'Kentän :attribute URL-osoite on virheellinen.',
    'after' => 'Kentän :attribute päivämäärän on oltava myöhempi kuin :date.',
    'after_or_equal' => 'Kentän :attribute päivämäärän on oltava :date tai myöhempi.',
    'alpha' => 'Kenttä :attribute saa sisältää vain kirjaimia.',
    'alpha_dash' => 'Kenttä :attribute saa sisältää vain kirjaimia, numeroita, yhdysmerkkejä ja alaviivoja.',
    'alpha_num' => 'Kenttä :attribute saa sisältää vain kirjaimia ja numeroita.',
    'array' => 'Kentän :attribute on oltava taulukko.',
    'before' => 'Kentän :attribute päivämäärän on oltava aikaisempi kuin :date.',
    'before_or_equal' => 'Kentän :attribute päivämäärän on oltava :date tai aikaisempi.',
    'between' => [
        'array' => 'Kentässä :attribute on oltava :min–:max alkiota.',
        'file' => 'Kentän :attribute tiedoston koon on oltava :min–:max kilotavua.',
        'numeric' => 'Kentän :attribute arvon on oltava välillä :min–:max.',
        'string' => 'Kentän :attribute pituuden on oltava :min–:max merkkiä.',
    ],
    'boolean' => 'Kentän :attribute arvon on oltava tosi tai epätosi.',
    'confirmed' => 'Kentän :attribute vahvistus ei täsmää.',
    'current_password' => 'Salasana on virheellinen.',
    'date' => 'Kentän :attribute päivämäärä on virheellinen.',
    'date_equals' => 'Kentän :attribute päivämäärän on oltava :date.',
    'date_format' => 'Kentän :attribute arvo ei vastaa muotoa :format.',
    'declined' => 'Kenttä :attribute on hylättävä.',
    'declined_if' => 'Kenttä :attribute on hylättävä, kun kentän :other arvo on :value.',
    'different' => 'Kenttien :attribute ja :other arvojen on oltava erilaiset.',
    'digits' => 'Kentän :attribute on sisällettävä :digits numeroa.',
    'digits_between' => 'Kentän :attribute on sisällettävä :min–:max numeroa.',
    'dimensions' => 'Kentän :attribute kuvan mitat ovat virheelliset.',
    'distinct' => 'Kentässä :attribute on toistuva arvo.',
    'doesnt_end_with' => 'Kenttä :attribute ei saa päättyä mihinkään seuraavista: :values.',
    'doesnt_start_with' => 'Kenttä :attribute ei saa alkaa millään seuraavista: :values.',
    'email' => 'Kentän :attribute on oltava kelvollinen sähköpostiosoite.',
    'ends_with' => 'Kentän :attribute on päätyttävä johonkin seuraavista: :values.',
    'enum' => 'Kentän :attribute valittu arvo on virheellinen.',
    'exists' => 'Kentän :attribute valittu arvo on virheellinen.',
    'file' => 'Kentän :attribute on oltava tiedosto.',
    'filled' => 'Kenttä :attribute ei saa olla tyhjä.',
    'gt' => [
        'array' => 'Kentässä :attribute on oltava enemmän kuin :value alkiota.',
        'file' => 'Kentän :attribute tiedoston koon on oltava suurempi kuin :value kilotavua.',
        'numeric' => 'Kentän :attribute arvon on oltava suurempi kuin :value.',
        'string' => 'Kentän :attribute pituuden on oltava enemmän kuin :value merkkiä.',
    ],
    'gte' => [
        'array' => 'Kentässä :attribute on oltava vähintään :value alkiota.',
        'file' => 'Kentän :attribute tiedoston koon on oltava vähintään :value kilotavua.',
        'numeric' => 'Kentän :attribute arvon on oltava vähintään :value.',
        'string' => 'Kentän :attribute pituuden on oltava vähintään :value merkkiä.',
    ],
    'image' => 'Kentän :attribute on oltava kuva.',
    'in' => 'Kentän :attribute valittu arvo on virheellinen.',
    'in_array' => 'Kentän :attribute arvoa ei löydy kentästä :other.',
    'integer' => 'Kentän :attribute on oltava kokonaisluku.',
    'ip' => 'Kentän :attribute on oltava kelvollinen IP-osoite.',
    'ipv4' => 'Kentän :attribute on oltava kelvollinen IPv4-osoite.',
    'ipv6' => 'Kentän :attribute on oltava kelvollinen IPv6-osoite.',
    'json' => 'Kentän :attribute on oltava kelvollinen JSON-merkkijono.',
    'lt' => [
        'array' => 'Kentässä :attribute on oltava vähemmän kuin :value alkiota.',
        'file' => 'Kentän :attribute tiedoston koon on oltava pienempi kuin :value kilotavua.',
        'numeric' => 'Kentän :attribute arvon on oltava pienempi kuin :value.',
        'string' => 'Kentän :attribute pituuden on oltava vähemmän kuin :value merkkiä.',
    ],
    'lte' => [
        'array' => 'Kentässä :attribute saa olla enintään :value alkiota.',
        'file' => 'Kentän :attribute tiedoston koko saa olla enintään :value kilotavua.',
        'numeric' => 'Kentän :attribute arvo saa olla enintään :value.',
        'string' => 'Kentän :attribute pituus saa olla enintään :value merkkiä.',
    ],
    'mac_address' => 'Kentän :attribute on oltava kelvollinen MAC-osoite.',
    'max' => [
        'array' => 'Kentässä :attribute saa olla enintään :max alkiota.',
        'file' => 'Kentän :attribute tiedoston koko saa olla enintään :max kilotavua.',
        'numeric' => 'Kentän :attribute arvo saa olla enintään :max.',
        'string' => 'Kentän :attribute pituus saa olla enintään :max merkkiä.',
    ],
    'max_digits' => 'Kenttä :attribute saa sisältää enintään :max numeroa.',
    'mimes' => 'Kentän :attribute tiedoston tyypin on oltava jokin seuraavista: :values.',
    'mimetypes' => 'Kentän :attribute tiedoston tyypin on oltava jokin seuraavista: :values.',
    'min' => [
        'array' => 'Kentässä :attribute on oltava vähintään :min alkiota.',
        'file' => 'Kentän :attribute tiedoston koon on oltava vähintään :min kilotavua.',
        'numeric' => 'Kentän :attribute arvon on oltava vähintään :min.',
        'string' => 'Kentän :attribute pituuden on oltava vähintään :min merkkiä.',
    ],
    'min_digits' => 'Kentän :attribute on sisällettävä vähintään :min numeroa.',
    'multiple_of' => 'Kentän :attribute arvon on oltava luvun :value monikerta.',
    'not_in' => 'Kentän :attribute valittu arvo on virheellinen.',
    'not_regex' => 'Kentän :attribute muoto on virheellinen.',
    'numeric' => 'Kentän :attribute on oltava luku.',
    'password' => [
        'letters' => 'Kentän :attribute on sisällettävä vähintään yksi kirjain.',
        'mixed' => 'Kentän :attribute on sisällettävä vähintään yksi iso ja yksi pieni kirjain.',
        'numbers' => 'Kentän :attribute on sisällettävä vähintään yksi numero.',
        'symbols' => 'Kentän :attribute on sisällettävä vähintään yksi erikoismerkki.',
        'uncompromised' => 'Annettu :attribute on esiintynyt tietovuodossa. Valitse toinen :attribute.',
    ],
    'present' => 'Kentän :attribute on oltava mukana.',
    'prohibited' => 'Kenttä :attribute ei ole sallittu.',
    'prohibited_if' => 'Kenttä :attribute ei ole sallittu, kun kentän :other arvo on :value.',
    'prohibited_unless' => 'Kenttä :attribute ei ole sallittu, ellei kentän :other arvo ole jokin seuraavista: :values.',
    'prohibits' => 'Kentän :attribute ollessa mukana kenttä :other ei ole sallittu.',
    'regex' => 'Kentän :attribute muoto on virheellinen.',
    'required' => 'Kenttä :attribute on pakollinen.',
    'required_array_keys' => 'Kentän :attribute on sisällettävä seuraavat avaimet: :values.',
    'required_if' => 'Kenttä :attribute on pakollinen, kun kentän :other arvo on :value.',
    'required_unless' => 'Kenttä :attribute on pakollinen, ellei kentän :other arvo ole jokin seuraavista: :values.',
    'required_with' => 'Kenttä :attribute on pakollinen, kun :values on mukana.',
    'required_with_all' => 'Kenttä :attribute on pakollinen, kun kaikki seuraavat ovat mukana: :values.',
    'required_without' => 'Kenttä :attribute on pakollinen, kun :values puuttuu.',
    'required_without_all' => 'Kenttä :attribute on pakollinen, kun kaikki seuraavat puuttuvat: :values.',
    'same' => 'Kenttien :attribute ja :other arvojen on oltava samat.',
    'size' => [
        'array' => 'Kentän :attribute on sisällettävä :size alkiota.',
        'file' => 'Kentän :attribute tiedoston koon on oltava :size kilotavua.',
        'numeric' => 'Kentän :attribute arvon on oltava :size.',
        'string' => 'Kentän :attribute pituuden on oltava :size merkkiä.',
    ],
    'starts_with' => 'Kentän :attribute on alettava jollakin seuraavista: :values.',
    'string' => 'Kentän :attribute on oltava merkkijono.',
    'timezone' => 'Kentän :attribute on oltava kelvollinen aikavyöhyke.',
    'unique' => 'Kentän :attribute arvo on jo käytössä.',
    'uploaded' => 'Kentän :attribute tiedoston lähettäminen epäonnistui.',
    'url' => 'Kentän :attribute on oltava kelvollinen URL-osoite.',
    'uuid' => 'Kentän :attribute on oltava kelvollinen UUID-tunniste.',

    /*
    |--------------------------------------------------------------------------
    | Omat tarkistusilmoitukset
    |--------------------------------------------------------------------------
    |
    | Omia virheilmoituksia voi määrittää kentille muodossa "kenttä.sääntö".
    | Näin yksittäiselle kentälle ja tarkistussäännölle voidaan antaa oma teksti.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'oma virheilmoitus',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Kenttien nimet
    |--------------------------------------------------------------------------
    |
    | Näillä nimillä korvataan kentän paikkamerkki käyttäjälle tutummalla
    | nimellä, esimerkiksi "sähköpostiosoite" teknisen "email"-nimen sijaan.
    |
    */

    'attributes' => [],

];
