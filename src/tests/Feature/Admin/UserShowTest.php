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

    public function testShowUserLastAccessNull()
    {
        $authUser = $this->createAdminUser();
        $token = $authUser->createToken('test')->plainTextToken;

        $userWithoutTokens = $this->createServiceBodyAdminUser();
        $userWithoutTokens->tokens()->delete();

        $data = $this->withHeader('Authorization', "Bearer $token")
            ->get("/api/v1/users/$userWithoutTokens->id_bigint")
            ->assertStatus(200)
            ->json();

        $this->assertNull($data['lastAccess']);
    }

    public function testShowUserLastAccessWithToken()
    {
        $user = $this->createAdminUser();
        $token = $user->createToken('test')->plainTextToken;

        $personalAccessToken = $user->tokens()->first();
        $lastUsedAt = now()->subHours(2);
        $personalAccessToken->forceFill(['last_used_at' => $lastUsedAt])->save();

        $data = $this->withHeader('Authorization', "Bearer $token")
            ->get("/api/v1/users/$user->id_bigint")
            ->assertStatus(200)
            ->json();

        $this->assertIsString($data['lastAccess']);
        $personalAccessToken->refresh();
        $this->assertEquals($personalAccessToken->last_used_at->toJSON(), $data['lastAccess']);
    }

    public function testShowUserLastAccessWithMultipleTokens()
    {
        $user = $this->createAdminUser();
        $token = $user->createToken('test')->plainTextToken;

        $olderToken = $user->createToken('older');
        $olderToken->accessToken->forceFill(['last_used_at' => now()->subDays(5)])->save();

        $newerToken = $user->createToken('newer');
        $newerToken->accessToken->forceFill(['last_used_at' => now()->subHours(1)])->save();

        $data = $this->withHeader('Authorization', "Bearer $token")
            ->get("/api/v1/users/$user->id_bigint")
            ->assertStatus(200)
            ->json();

        $this->assertIsString($data['lastAccess']);
        $expectedMostRecent = $user->tokens()->orderByDesc('last_used_at')->first()->last_used_at;
        $this->assertEquals($expectedMostRecent->toJSON(), $data['lastAccess']);
    }
}
