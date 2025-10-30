<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserShowTest extends TestCase
{
    use RefreshDatabase;

    public function testShowUserName()
    {
        $user = $this->createAdminUser();
        $token = $user->createToken('test')->plainTextToken;

        $user->login_string = 'test string';
        $user->save();
        $data = $this->withHeader('Authorization', "Bearer $token")
            ->get("/api/v1/users/$user->id_bigint")
            ->assertStatus(200)
            ->json();

        $this->assertIsString($data['username']);
        $this->assertEquals($user->login_string, $data['username']);
    }

    public function testShowUserType()
    {
        $user = $this->createAdminUser();
        $token = $user->createToken('test')->plainTextToken;

        $data = $this->withHeader('Authorization', "Bearer $token")
            ->get("/api/v1/users/$user->id_bigint")
            ->assertStatus(200)
            ->json();

        $this->assertIsString($data['type']);
        $this->assertEquals(User::USER_LEVEL_TO_USER_TYPE_MAP[$user->user_level_tinyint], $data['type']);
    }

    public function testShowUserDisplayName()
    {
        $user = $this->createAdminUser();
        $token = $user->createToken('test')->plainTextToken;

        $user->name_string = 'test string';
        $user->save();
        $data = $this->withHeader('Authorization', "Bearer $token")
            ->get("/api/v1/users/$user->id_bigint")
            ->assertStatus(200)
            ->json();

        $this->assertIsString($data['displayName']);
        $this->assertEquals($user->name_string, $data['displayName']);
    }

    public function testShowUserDescription()
    {
        $user = $this->createAdminUser();
        $token = $user->createToken('test')->plainTextToken;

        $user->description_string = 'test string';
        $user->save();
        $data = $this->withHeader('Authorization', "Bearer $token")
            ->get("/api/v1/users/$user->id_bigint")
            ->assertStatus(200)
            ->json();

        $this->assertIsString($data['description']);
        $this->assertEquals($user->description_string, $data['description']);
    }

    public function testShowUserEmail()
    {
        $user = $this->createAdminUser();
        $token = $user->createToken('test')->plainTextToken;

        $user->email_address_string = 'test string';
        $user->save();
        $data = $this->withHeader('Authorization', "Bearer $token")
            ->get("/api/v1/users/$user->id_bigint")
            ->assertStatus(200)
            ->json();

        $this->assertIsString($data['email']);
        $this->assertEquals($user->email_address_string, $data['email']);
    }

    public function testShowUserOwnerIdNegativeOne()
    {
        $user = $this->createAdminUser();
        $token = $user->createToken('test')->plainTextToken;

        $user->owner_id_bigint = -1;
        $user->save();
        $data = $this->withHeader('Authorization', "Bearer $token")
            ->get("/api/v1/users/$user->id_bigint")
            ->assertStatus(200)
            ->json();

        $this->assertNull($data['ownerId']);
    }

    public function testShowUserOwnerIdPositiveInt()
    {
        $user = $this->createAdminUser();
        $token = $user->createToken('test')->plainTextToken;

        $user->owner_id_bigint = 123;
        $user->save();
        $data = $this->withHeader('Authorization', "Bearer $token")
            ->get("/api/v1/users/$user->id_bigint")
            ->assertStatus(200)
            ->json();

        $this->assertIsInt($data['ownerId']);
        $this->assertEquals($user->owner_id_bigint, $data['ownerId']);
    }

    public function testShowUserLastAccessAtNull()
    {
        $authUser = $this->createAdminUser();
        $token = $authUser->createToken('test')->plainTextToken;
        $userWithoutAccess = $this->createServiceBodyAdminUser();

        $data = $this->withHeader('Authorization', "Bearer $token")
            ->get("/api/v1/users/$userWithoutAccess->id_bigint")
            ->assertStatus(200)
            ->json();

        $this->assertNull($data['lastLoginAt']);
    }

    public function testShowUserLastAccessAtWithToken()
    {
        $authUser = $this->createAdminUser();
        $token = $authUser->createToken('test')->plainTextToken;

        $user = $this->createServiceBodyAdminUser();
        $lastLoginAt = \Carbon\Carbon::parse('2025-06-15 10:00:00', 'UTC');
        $user->last_access_datetime = $lastLoginAt;
        $user->save();

        $data = $this->withHeader('Authorization', "Bearer $token")
            ->get("/api/v1/users/$user->id_bigint")
            ->assertStatus(200)
            ->json();

        $this->assertIsString($data['lastLoginAt']);
        $user->refresh();
        $this->assertEquals($user->last_access_datetime->toJSON(), $data['lastLoginAt']);
    }

    public function testShowUserLastAccessAtWithMultipleTokens()
    {
        $authUser = $this->createAdminUser();
        $token = $authUser->createToken('test')->plainTextToken;

        $user = $this->createServiceBodyAdminUser();
        $mostRecentAccess = \Carbon\Carbon::parse('2025-06-15 14:30:00', 'UTC');
        $user->last_access_datetime = $mostRecentAccess;
        $user->save();

        $data = $this->withHeader('Authorization', "Bearer $token")
            ->get("/api/v1/users/$user->id_bigint")
            ->assertStatus(200)
            ->json();

        $this->assertIsString($data['lastLoginAt']);
        $user->refresh();
        $this->assertEquals($user->last_access_datetime->toJSON(), $data['lastLoginAt']);
    }
}
