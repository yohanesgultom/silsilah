<?php

use App\Couple;
use App\User;
use App\UserMetadata;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Ramsey\Uuid\Uuid;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Builds a small 3-generation family tree so the app has data to exercise:
     * profiles, father/mother/child relations, siblings, couples/marriage,
     * charts, trees, and cemetery metadata.
     *
     * @return void
     */
    public function run()
    {
        // --- Generation 1: grandparents ---
        $grandfather = User::create([
            'id'         => Uuid::uuid4()->toString(),
            'nickname'   => 'Ayah',
            'name'       => 'Bapak Besar',
            'gender_id'  => 1,
            'email'      => 'ayah@example.com',
            'password'   => Hash::make('password'),
            'manager_id' => null,
            'dob'        => '1950-01-01',
            'yob'        => 1950,
        ]);
        $grandfather->manager_id = $grandfather->id;
        $grandfather->save();

        $grandmother = User::create([
            'id'         => Uuid::uuid4()->toString(),
            'nickname'   => 'Ibu',
            'name'       => 'Ibu Besar',
            'gender_id'  => 2,
            'email'      => 'ibu@example.com',
            'password'   => Hash::make('password'),
            'manager_id' => null,
            'dob'        => '1952-03-15',
            'yob'        => 1952,
        ]);
        $grandmother->manager_id = $grandmother->id;
        $grandmother->save();

        $grandparents = Couple::create([
            'id'            => Uuid::uuid4()->toString(),
            'husband_id'    => $grandfather->id,
            'wife_id'       => $grandmother->id,
            'marriage_date' => '1975-06-01',
            'manager_id'    => $grandfather->id,
        ]);

        // --- Generation 2: parents ---
        $father = User::create([
            'id'         => Uuid::uuid4()->toString(),
            'nickname'   => 'Bapak',
            'name'       => 'Bapak Muda',
            'gender_id'  => 1,
            'email'      => 'bapak@example.com',
            'password'   => Hash::make('password'),
            'father_id'  => $grandfather->id,
            'mother_id'  => $grandmother->id,
            'parent_id'  => $grandparents->id,
            'manager_id' => null,
            'dob'        => '1980-07-20',
            'yob'        => 1980,
        ]);
        $father->manager_id = $father->id;
        $father->save();

        $mother = User::create([
            'id'         => Uuid::uuid4()->toString(),
            'nickname'   => 'Ibu Muda',
            'name'       => 'Ibu Muda',
            'gender_id'  => 2,
            'email'      => 'ibu.muda@example.com',
            'password'   => Hash::make('password'),
            'manager_id' => null,
            'dob'        => '1982-11-10',
            'yob'        => 1982,
        ]);
        $mother->manager_id = $mother->id;
        $mother->save();

        $parents = Couple::create([
            'id'            => Uuid::uuid4()->toString(),
            'husband_id'    => $father->id,
            'wife_id'       => $mother->id,
            'marriage_date' => '2005-08-15',
            'manager_id'    => $father->id,
        ]);

        // --- Generation 3: children (siblings to each other) ---
        $child1 = User::create([
            'id'         => Uuid::uuid4()->toString(),
            'nickname'   => 'Anak Satu',
            'name'       => 'Anak Pertama',
            'gender_id'  => 1,
            'email'      => 'anak1@example.com',
            'password'   => Hash::make('password'),
            'father_id'  => $father->id,
            'mother_id'  => $mother->id,
            'parent_id'  => $parents->id,
            'manager_id' => null,
            'dob'        => '2006-02-14',
            'yob'        => 2006,
            'birth_order'=> 1,
        ]);
        $child1->manager_id = $child1->id;
        $child1->save();

        $child2 = User::create([
            'id'         => Uuid::uuid4()->toString(),
            'nickname'   => 'Anak Dua',
            'name'       => 'Anak Kedua',
            'gender_id'  => 2,
            'email'      => 'anak2@example.com',
            'password'   => Hash::make('password'),
            'father_id'  => $father->id,
            'mother_id'  => $mother->id,
            'parent_id'  => $parents->id,
            'manager_id' => null,
            'dob'        => '2009-09-05',
            'yob'        => 2009,
            'birth_order'=> 2,
        ]);
        $child2->manager_id = $child2->id;
        $child2->save();

        // --- Cemetery metadata (for the Leaflet cemetery map) ---
        UserMetadata::create([
            'id'      => Uuid::uuid4()->toString(),
            'user_id' => $grandfather->id,
            'key'     => 'cemetery_location_name',
            'value'   => 'Taman Makam Pahlawan',
        ]);

        UserMetadata::create([
            'id'      => Uuid::uuid4()->toString(),
            'user_id' => $grandfather->id,
            'key'     => 'cemetery_location_latitude',
            'value'   => '-6.2088',
        ]);

        UserMetadata::create([
            'id'      => Uuid::uuid4()->toString(),
            'user_id' => $grandfather->id,
            'key'     => 'cemetery_location_longitude',
            'value'   => '106.8456',
        ]);
    }
}
