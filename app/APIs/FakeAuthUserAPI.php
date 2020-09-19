<?php

namespace App\APIs;

use Faker\Factory;
use App\Contracts\AuthUserAPI;

class FakeAuthUserAPI implements AuthUserAPI
{
    public function getUser($login)
    {
        $faker = Factory::create();
        
        $orgId = rand(10000000, 10099999);

        $name = (strlen($login) % 2) === 0 ?
                    $faker->firstNameFemale . ' ' . $faker->lastName :
                    $faker->firstNameMale . ' ' . $faker->lastName;

        return [
            'ok' => true,
            'active' => true,
            'login' => $login,
            'org_id' => $orgId,
            'full_name' => $name,
            'document_id' => $faker->ean13,
            'position_id' => '',
            'position_name' => '',
            'division_id' => '',
            'division_name' => '',
            'password_expires_in_days' => 90,
            'remark' => '',
        ];
    }

    public function authenticate($login, $password)
    {
        if ($password !== $login) {
            return ['ok' => true, 'found' => false];
        }
        $data = $this->getUser($login);
        unset($data['active']);
        $data['full_name_eng'] = $data['full_name'];
        $data['department_name'] = '';
        $data['office_name'] = '';
        $data['email'] = $data['login'] . '@waja.net';
        return $data;
    }
}
