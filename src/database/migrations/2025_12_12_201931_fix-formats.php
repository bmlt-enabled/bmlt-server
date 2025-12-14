<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * There were some duplicate key errors in the migration that added initial format translations. This is in 1902_01_01_000000_create_initial_schema.php.
     * The errors in the migration have now been fixed, so that new databases won't have these errors; but existing databases may. This can be an issue
     * since version 4.0.0 onward has error checking for duplicate format keys when editing a format (earlier versions did not). Due to the check,
     * the server admin wouldn't be able to edit one of the affected formats without fixing the error.  This migration fixes those mistakes for
     * existing databases. Here are the problematic keys that are being repaired. The id and shared_id_bigint are what you would get by running the
     * create_initial_schema migration; we don't assume they would be the same in another server's database.
     +-----+------------------+-----------+------------+---------------------+----------------------------------------------------------------------------------------------+
     | id  | shared_id_bigint | lang_enum | key_string | name_string         | description_string                                                                           |
     +-----+------------------+-----------+------------+---------------------+----------------------------------------------------------------------------------------------+
     | 269 |               35 | it        | M          | Maratona            | Durata non prestabilita. La riunione prosegue finché tutti i presenti hanno da condividere.  |
     | 272 |               42 | it        | M          | Meditazione         | In questa riunione sono poste restrizioni alle modalità di partecipazione.                   |
     | 348 |               16 | pt        | PC         | Proibido crianças   | Por gentileza não trazer crianças a essa reunião.                                            |
     | 375 |               44 | pt        | PC         | Permitido Crianças  | Crianças são bem-vindas a essa reunião.                                                      |
     | 338 |                6 | pt        | VL         | Luz de velas        | Esta reunião acontece à luz de velas.                                                        |
     | 381 |               51 | pt        | VL         | Vivendo Limpo       | Esta é uma reunião de discussão do livro Vivendo Limpo-A Jornada Continua.                   |
     | 442 |               47 | sv        | ENG        | Engelska            | Engelsktalande möte                                                                          |
     | 446 |               47 | sv        | ENG        | Engelska            | Engelsktalande möte                                                                          |
     +-----+------------------+-----------+------------+---------------------+----------------------------------------------------------------------------------------------+

     */
    public function up(): void
    {
        // For each key_string update, first make sure someone didn't start using the new key for another translation (unlikely, but check anyway).
        // Also insist on an exact match for key, name and description. If the key is no longer available, or if there isn't an exact match for
        // each of key, name, and description, just skip that update -- the server admin can sort out the format later if necessary.

        // update the key and name for Restricted Access format in Italian (it was mixed up with Meditation format)
        $available = DB::table('comdef_formats')
            ->where('lang_enum', 'it')
            ->where('key_string', 'AR')
            ->doesntExist();
        if ($available) {
            DB::table('comdef_formats')
                ->where('lang_enum', 'it')
                ->where('key_string', 'M')
                ->where('name_string', 'Meditazione')
                ->where('description_string', 'In questa riunione sono poste restrizioni alle modalità di partecipazione.')
                ->update(['key_string' => 'AR', 'name_string' => 'Accesso ristretto']);
        }

        // update the key and name for Children Welcome format in Portuguese
        $available = DB::table('comdef_formats')
            ->where('lang_enum', 'pt')
            ->where('key_string', 'CBM')
            ->doesntExist();
        if ($available) {
            DB::table('comdef_formats')
                ->where('lang_enum', 'pt')
                ->where('key_string', 'PC')
                ->where('name_string', 'Permitido Crianças')
                ->where('description_string', 'Crianças são bem-vindas a essa reunião.')
                ->update(['key_string' => 'CBM', 'name_string' => 'Crianças são bem-vindas']);
        }

        // update the key for Candlelight format in Portuguese
        $available = DB::table('comdef_formats')
            ->where('lang_enum', 'pt')
            ->where('key_string', 'LV')
            ->doesntExist();
        if ($available) {
            DB::table('comdef_formats')
                ->where('lang_enum', 'pt')
                ->where('key_string', 'VL')
                ->where('name_string', 'Luz de velas')
                ->where('description_string', 'Esta reunião acontece à luz de velas.')
                ->update(['key_string' => 'LV']);
        }

        // delete the duplicate ENG format for Swedish
        $formats = DB::table('comdef_formats')
            ->where('lang_enum', 'sv')
            ->where('key_string', 'ENG')
            ->where('name_string', 'Engelska')
            ->where('description_string', 'Engelsktalande möte');
        $n = $formats->count();
        if ($n == 2) {
            $dupId =$formats->max('id');
            DB::table('comdef_formats')
                ->where('id', $dupId)
                ->delete();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
