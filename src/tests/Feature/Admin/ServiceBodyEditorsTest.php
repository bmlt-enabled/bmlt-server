<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ServiceBodyEditorsTest extends TestCase
{
    use RefreshDatabase;

    private function createOwnedEditor(string $login, string $name, ?int $ownerId = null): User
    {
        $values = [
            'user_level_tinyint' => User::USER_LEVEL_SERVICE_BODY_ADMIN,
            'name_string' => $name,
            'description_string' => '',
            'email_address_string' => '',
            'login_string' => $login,
            'password_string' => password_hash($this->userPassword, PASSWORD_BCRYPT),
        ];
        $user = User::create($values);
        if (!is_null($ownerId)) {
            $user->owner_id_bigint = $ownerId;
            $user->save();
        }
        return $user;
    }

    public function testEditorsAsUnauthenticated()
    {
        $area = $this->createArea('area', 'desc', 0);
        $this->get("/api/v1/servicebodies/$area->id_bigint/editors")
            ->assertStatus(401);
    }

    public function testEditorsAsDeactivated()
    {
        $user = $this->createDeactivatedUser();
        $token = $user->createToken('test')->plainTextToken;
        $area = $this->createArea('area', 'desc', 0);
        $this->withHeader('Authorization', "Bearer $token")
            ->get("/api/v1/servicebodies/$area->id_bigint/editors")
            ->assertStatus(403);
    }

    public function testEditorsAsObserverNotAssigned()
    {
        $user = $this->createServiceBodyObserverUser();
        $token = $user->createToken('test')->plainTextToken;
        $area = $this->createArea('area', 'desc', 0);
        $this->withHeader('Authorization', "Bearer $token")
            ->get("/api/v1/servicebodies/$area->id_bigint/editors")
            ->assertStatus(403);
    }

    public function testEditorsServiceBodyNotFound()
    {
        $user = $this->createAdminUser();
        $token = $user->createToken('test')->plainTextToken;
        $this->withHeader('Authorization', "Bearer $token")
            ->get('/api/v1/servicebodies/999999/editors')
            ->assertStatus(404);
    }

    public function testEditorsEmpty()
    {
        $admin = $this->createAdminUser();
        $token = $admin->createToken('test')->plainTextToken;
        $area = $this->createArea('area', 'desc', 0);
        $this->withHeader('Authorization', "Bearer $token")
            ->get("/api/v1/servicebodies/$area->id_bigint/editors")
            ->assertStatus(200)
            ->assertExactJson([]);
    }

    public function testEditorsAsAdminAllReadOnlyFalse()
    {
        $admin = $this->createAdminUser();
        $sbAdmin = $this->createServiceBodyAdminUser();
        $editor = $this->createOwnedEditor('editor1', 'Editor One', $sbAdmin->id_bigint);
        $area = $this->createArea(
            'area',
            'desc',
            0,
            adminUserId: $sbAdmin->id_bigint,
            assignedUserIds: [$sbAdmin->id_bigint, $editor->id_bigint],
        );
        $token = $admin->createToken('test')->plainTextToken;
        $data = $this->withHeader('Authorization', "Bearer $token")
            ->get("/api/v1/servicebodies/$area->id_bigint/editors")
            ->assertStatus(200)
            ->json();

        $this->assertCount(2, $data);
        foreach ($data as $entry) {
            $this->assertArrayHasKey('userId', $entry);
            $this->assertArrayHasKey('displayName', $entry);
            $this->assertArrayHasKey('readOnly', $entry);
            $this->assertFalse($entry['readOnly']);
            $this->assertArrayNotHasKey('email', $entry);
            $this->assertArrayNotHasKey('username', $entry);
        }
    }

    public function testEditorsAsServiceBodyAdminSeesOwnedAsEditableAndOthersAsReadOnly()
    {
        $sbAdmin = $this->createServiceBodyAdminUser();
        $ownedEditor = $this->createOwnedEditor('owned', 'Owned Editor', $sbAdmin->id_bigint);
        $foreignEditor = $this->createOwnedEditor('foreign', 'Foreign Editor');
        $area = $this->createArea(
            'area',
            'desc',
            0,
            adminUserId: $sbAdmin->id_bigint,
            assignedUserIds: [$ownedEditor->id_bigint, $foreignEditor->id_bigint, $sbAdmin->id_bigint],
        );
        $token = $sbAdmin->createToken('test')->plainTextToken;
        $data = $this->withHeader('Authorization', "Bearer $token")
            ->get("/api/v1/servicebodies/$area->id_bigint/editors")
            ->assertStatus(200)
            ->json();

        $byId = collect($data)->keyBy('userId');
        $this->assertCount(3, $byId);
        $this->assertFalse($byId[$ownedEditor->id_bigint]['readOnly']);
        $this->assertEquals('Owned Editor', $byId[$ownedEditor->id_bigint]['displayName']);
        $this->assertTrue($byId[$foreignEditor->id_bigint]['readOnly']);
        $this->assertEquals('Foreign Editor', $byId[$foreignEditor->id_bigint]['displayName']);
        $this->assertFalse($byId[$sbAdmin->id_bigint]['readOnly']);
    }

    public function testEditorsAsAssignedEditorSelfNotReadOnly()
    {
        $sbAdmin = $this->createServiceBodyAdminUser();
        $editorUser = $this->createOwnedEditor('editorself', 'Self Editor', $sbAdmin->id_bigint);
        $foreignEditor = $this->createOwnedEditor('other', 'Other', $sbAdmin->id_bigint);
        $area = $this->createArea(
            'area',
            'desc',
            0,
            adminUserId: $sbAdmin->id_bigint,
            assignedUserIds: [$editorUser->id_bigint, $foreignEditor->id_bigint],
        );

        $editorUser->user_level_tinyint = User::USER_LEVEL_OBSERVER;
        $editorUser->save();

        $token = $editorUser->createToken('test')->plainTextToken;
        $data = $this->withHeader('Authorization', "Bearer $token")
            ->get("/api/v1/servicebodies/$area->id_bigint/editors")
            ->assertStatus(200)
            ->json();

        $byId = collect($data)->keyBy('userId');
        $this->assertFalse($byId[$editorUser->id_bigint]['readOnly']);
        $this->assertTrue($byId[$foreignEditor->id_bigint]['readOnly']);
    }

    public function testEditorsFiltersOutNonexistentUserIds()
    {
        $admin = $this->createAdminUser();
        $sbAdmin = $this->createServiceBodyAdminUser();
        $area = $this->createArea(
            'area',
            'desc',
            0,
            adminUserId: $sbAdmin->id_bigint,
            assignedUserIds: [$sbAdmin->id_bigint, 999999],
        );
        $token = $admin->createToken('test')->plainTextToken;
        $data = $this->withHeader('Authorization', "Bearer $token")
            ->get("/api/v1/servicebodies/$area->id_bigint/editors")
            ->assertStatus(200)
            ->json();

        $this->assertCount(1, $data);
        $this->assertEquals($sbAdmin->id_bigint, $data[0]['userId']);
    }

    public function testEditorsPreservesOrderFromEditorsString()
    {
        $admin = $this->createAdminUser();
        $sbAdmin = $this->createServiceBodyAdminUser();
        $a = $this->createOwnedEditor('a', 'A', $sbAdmin->id_bigint);
        $b = $this->createOwnedEditor('b', 'B', $sbAdmin->id_bigint);
        $c = $this->createOwnedEditor('c', 'C', $sbAdmin->id_bigint);
        $area = $this->createArea(
            'area',
            'desc',
            0,
            adminUserId: $sbAdmin->id_bigint,
            assignedUserIds: [$c->id_bigint, $a->id_bigint, $b->id_bigint],
        );
        $token = $admin->createToken('test')->plainTextToken;
        $data = $this->withHeader('Authorization', "Bearer $token")
            ->get("/api/v1/servicebodies/$area->id_bigint/editors")
            ->assertStatus(200)
            ->json();

        $this->assertEquals([$c->id_bigint, $a->id_bigint, $b->id_bigint], array_column($data, 'userId'));
    }
}
