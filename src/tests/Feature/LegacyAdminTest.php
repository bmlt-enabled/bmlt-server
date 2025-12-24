<?php

namespace Tests\Feature;

use App\Models\ServiceBody;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LegacyAdminTest extends TestCase
{
    use RefreshDatabase;

    private string $goodPassword = 'goodpassword';
    private string $badPassword = 'badpassword';

    public function createServerAdmin()
    {
        return User::create([
            'user_level_tinyint' => User::USER_LEVEL_ADMIN,
            'name_string' => 'test',
            'description_string' => '',
            'email_address_string' => '',
            'login_string' => 'test',
            'password_string' => password_hash($this->goodPassword, PASSWORD_BCRYPT),
        ]);
    }

    public function createServiceBodyAdmin()
    {
        return User::create([
            'user_level_tinyint' => User::USER_LEVEL_SERVICE_BODY_ADMIN,
            'name_string' => 'test',
            'description_string' => '',
            'email_address_string' => '',
            'login_string' => 'test',
            'password_string' => password_hash($this->goodPassword, PASSWORD_BCRYPT),
        ]);
    }

    public function createArea(string $name, ?int $principalUserId = null, ?array $assignedUserIds = null)
    {
        return ServiceBody::create([
            'sb_owner' => 0,
            'name_string' => $name,
            'description_string' => 'my area',
            'sb_type' => 'AS',
            'uri_string' => null,
            'kml_file_uri_string' => null,
            'worldid_mixed' => null,
            'sb_meeting_email' => '',
            'principal_user_bigint' => $principalUserId,
            'editors_string' => !is_null($assignedUserIds) ? implode(',', $assignedUserIds) : null,
        ]);
    }

    public function testSuccessfulLoginServiceBodyAdminWeb()
    {
        $user = $this->createServiceBodyAdmin();
        $urls = ['', '/', '/index.php'];
        foreach ($urls as $url) {
            $data = [
                'admin_action' => 'login',
                'c_comdef_admin_login' => $user->login_string,
                'c_comdef_admin_password' => $this->goodPassword
            ];
            $this->post($url, $data)
                ->assertStatus(302)
                ->assertSessionHas('login_web_59ba36addc2b2f9401580f014c7f58ea4e30989d', $user->id_bigint);
        }
    }

    public function testSuccessfulLoginServerAdminWeb()
    {
        $user = $this->createServerAdmin();
        $urls = ['', '/', '/index.php'];
        foreach ($urls as $url) {
            $data = [
                'admin_action' => 'login',
                'c_comdef_admin_login' => $user->login_string,
                'c_comdef_admin_password' => $this->goodPassword
            ];
            $this->post($url, $data)
                ->assertStatus(302)
                ->assertSessionHas('login_web_59ba36addc2b2f9401580f014c7f58ea4e30989d', $user->id_bigint);
        }
    }

    public function testFailedLoginWeb()
    {
        $user = $this->createServiceBodyAdmin();
        $urls = ['', '/', '/index.php'];
        foreach ($urls as $url) {
            $data = [
                'admin_action' => 'login',
                'c_comdef_admin_login' => $user->login_string,
                'c_comdef_admin_password' => $this->badPassword
            ];
            $this->post($url, $data)
                ->assertStatus(302)
                ->assertSessionMissing('login_web_59ba36addc2b2f9401580f014c7f58ea4e30989d');
        }
    }

    public function testLogoutWeb()
    {
        $user = $this->createServiceBodyAdmin();
        $urls = ['', '/', '/index.php'];
        foreach ($urls as $url) {
            $this->actingAs($user)
                ->withSession([
                    'login_web_59ba36addc2b2f9401580f014c7f58ea4e30989d' => $user->id_bigint,
                ])
                ->post($url, ['admin_action' => 'logout'])
                ->assertStatus(302)
                ->assertSessionMissing('login_web_59ba36addc2b2f9401580f014c7f58ea4e30989d');
        }
    }

    public function testSuccessfulLoginAdminXml()
    {
        $user = $this->createServiceBodyAdmin();
        $data = [
            'admin_action' => 'login',
            'c_comdef_admin_login' => $user->login_string,
            'c_comdef_admin_password' => $this->goodPassword
        ];
        $this->assertEquals(
            'OK',
            $this->post('/local_server/server_admin/xml.php', $data)
                ->assertStatus(200)
                ->assertHeader('Content-Type', 'text/html; charset=UTF-8')
                ->assertSessionHas('login_web_59ba36addc2b2f9401580f014c7f58ea4e30989d', $user->id_bigint)
                ->content()
        );
        $this->assertEquals(
            'OK',
            $this->post('////local_server/server_admin/xml.php', $data)
                ->assertStatus(200)
                ->assertHeader('Content-Type', 'text/html; charset=UTF-8')
                ->assertSessionHas('login_web_59ba36addc2b2f9401580f014c7f58ea4e30989d', $user->id_bigint)
                ->content()
        );
        $this->assertEquals(
            'OK',
            $this->get("/local_server/server_admin/xml.php?admin_action=login&c_comdef_admin_login=$user->login_string&c_comdef_admin_password=$this->goodPassword")
                ->assertStatus(200)
                ->assertHeader('Content-Type', 'text/html; charset=UTF-8')
                ->assertSessionHas('login_web_59ba36addc2b2f9401580f014c7f58ea4e30989d', $user->id_bigint)
                ->content()
        );
    }

    public function testFailedLoginServiceBodyAdminAdminXml()
    {
        $user = $this->createServiceBodyAdmin();
        $data = [
            'admin_action' => 'login',
            'c_comdef_admin_login' => $user->login_string,
            'c_comdef_admin_password' => $this->badPassword
        ];
        $this->assertEquals(
            '<h1>NOT AUTHORIZED</h1>',
            $this->post('/local_server/server_admin/xml.php', $data)
                ->assertStatus(200)
                ->assertHeader('Content-Type', 'text/html; charset=UTF-8')
                ->assertSessionMissing('login_web_59ba36addc2b2f9401580f014c7f58ea4e30989d')
                ->content()
        );
        $this->assertEquals(
            '<h1>NOT AUTHORIZED</h1>',
            $this->get("/local_server/server_admin/xml.php?admin_action=login&c_comdef_admin_login=$user->login_string&c_comdef_admin_password=$this->badPassword")
                ->assertStatus(200)
                ->assertHeader('Content-Type', 'text/html; charset=UTF-8')
                ->assertSessionMissing('login_web_59ba36addc2b2f9401580f014c7f58ea4e30989d')
                ->content()
        );
    }

    public function testServerAdminAdminXml()
    {
        $user = $this->createServerAdmin();
        $data = [
            'admin_action' => 'login',
            'c_comdef_admin_login' => $user->login_string,
            'c_comdef_admin_password' => $this->goodPassword
        ];
        $this->assertEquals(
            '<h1>NOT AUTHORIZED</h1>',
            $this->post('/local_server/server_admin/xml.php', $data)
                ->assertStatus(200)
                ->assertHeader('Content-Type', 'text/html; charset=UTF-8')
                ->assertSessionMissing('login_web_59ba36addc2b2f9401580f014c7f58ea4e30989d')
                ->content()
        );
        $this->assertEquals(
            '<h1>NOT AUTHORIZED</h1>',
            $this->get("/local_server/server_admin/xml.php?admin_action=login&c_comdef_admin_login=$user->login_string&c_comdef_admin_password=$this->badPassword")
                ->assertStatus(200)
                ->assertHeader('Content-Type', 'text/html; charset=UTF-8')
                ->assertSessionMissing('login_web_59ba36addc2b2f9401580f014c7f58ea4e30989d')
                ->content()
        );
    }

    public function testLogoutAdminXml()
    {
        $user = $this->createServiceBodyAdmin();
        $this->assertEquals(
            'BYE',
            $this->actingAs($user)
                ->withSession([
                    'login_web_59ba36addc2b2f9401580f014c7f58ea4e30989d' => $user->id_bigint,
                ])
                ->post('/local_server/server_admin/xml.php', ['admin_action' => 'logout'])
                ->assertStatus(200)
                ->assertHeader('Content-Type', 'text/html; charset=UTF-8')
                ->assertSessionMissing('login_web_59ba36addc2b2f9401580f014c7f58ea4e30989d')
                ->content()
        );
        $this->assertEquals(
            'BYE',
            $this->actingAs($user)
                ->withSession([
                    'login_web_59ba36addc2b2f9401580f014c7f58ea4e30989d' => $user->id_bigint,
                ])
                ->get('/local_server/server_admin/xml.php?admin_action=logout')
                ->assertStatus(200)
                ->assertHeader('Content-Type', 'text/html; charset=UTF-8')
                ->assertSessionMissing('login_web_59ba36addc2b2f9401580f014c7f58ea4e30989d')
                ->content()
        );
    }

    public function testSuccessfulLoginAdminJson()
    {
        $user = $this->createServiceBodyAdmin();
        $data = [
            'admin_action' => 'login',
            'c_comdef_admin_login' => $user->login_string,
            'c_comdef_admin_password' => $this->goodPassword
        ];
        $this->assertEquals(
            'OK',
            $this->post('/local_server/server_admin/json.php', $data)
                ->assertStatus(200)
                ->assertHeader('Content-Type', 'text/html; charset=UTF-8')
                ->assertSessionHas('login_web_59ba36addc2b2f9401580f014c7f58ea4e30989d', $user->id_bigint)
                ->content()
        );
        $this->assertEquals(
            'OK',
            $this->post('///local_server/server_admin/json.php', $data)
                ->assertStatus(200)
                ->assertHeader('Content-Type', 'text/html; charset=UTF-8')
                ->assertSessionHas('login_web_59ba36addc2b2f9401580f014c7f58ea4e30989d', $user->id_bigint)
                ->content()
        );
        $this->assertEquals(
            'OK',
            $this->get("/local_server/server_admin/json.php?admin_action=login&c_comdef_admin_login=$user->login_string&c_comdef_admin_password=$this->goodPassword")
                ->assertStatus(200)
                ->assertHeader('Content-Type', 'text/html; charset=UTF-8')
                ->assertSessionHas('login_web_59ba36addc2b2f9401580f014c7f58ea4e30989d', $user->id_bigint)
                ->content()
        );
    }

    public function testFailedLoginAdminJson()
    {
        $user = $this->createServiceBodyAdmin();
        $data = [
            'admin_action' => 'login',
            'c_comdef_admin_login' => $user->login_string,
            'c_comdef_admin_password' => $this->badPassword
        ];
        $this->assertEquals(
            'NOT AUTHORIZED',
            $this->post('/local_server/server_admin/json.php', $data)
                ->assertStatus(200)
                ->assertHeader('Content-Type', 'text/html; charset=UTF-8')
                ->assertSessionMissing('login_web_59ba36addc2b2f9401580f014c7f58ea4e30989d')
                ->content()
        );
        $this->assertEquals(
            'NOT AUTHORIZED',
            $this->get("/local_server/server_admin/json.php?admin_action=login&c_comdef_admin_login=$user->login_string&c_comdef_admin_password=$this->badPassword")
                ->assertStatus(200)
                ->assertHeader('Content-Type', 'text/html; charset=UTF-8')
                ->assertSessionMissing('login_web_59ba36addc2b2f9401580f014c7f58ea4e30989d')
                ->content()
        );
    }

    public function testServerAdminUserAdminJson()
    {
        $user = $this->createServerAdmin();
        $data = [
            'admin_action' => 'login',
            'c_comdef_admin_login' => $user->login_string,
            'c_comdef_admin_password' => $this->goodPassword
        ];
        $this->assertEquals(
            'NOT AUTHORIZED',
            $this->post('/local_server/server_admin/json.php', $data)
                ->assertStatus(200)
                ->assertHeader('Content-Type', 'text/html; charset=UTF-8')
                ->assertSessionMissing('login_web_59ba36addc2b2f9401580f014c7f58ea4e30989d')
                ->content()
        );
        $this->assertEquals(
            'NOT AUTHORIZED',
            $this->get("/local_server/server_admin/json.php?admin_action=login&c_comdef_admin_login=$user->login_string&c_comdef_admin_password=$this->badPassword")
                ->assertStatus(200)
                ->assertHeader('Content-Type', 'text/html; charset=UTF-8')
                ->assertSessionMissing('login_web_59ba36addc2b2f9401580f014c7f58ea4e30989d')
                ->content()
        );
    }

    public function testLogoutAdminJson()
    {
        $user = $this->createServiceBodyAdmin();
        $this->assertEquals(
            'BYE',
            $this->actingAs($user)
                ->withSession([
                    'login_web_59ba36addc2b2f9401580f014c7f58ea4e30989d' => $user->id_bigint,
                ])
                ->post('/local_server/server_admin/json.php', ['admin_action' => 'logout'])
                ->assertStatus(200)
                ->assertHeader('Content-Type', 'text/html; charset=UTF-8')
                ->assertSessionMissing('login_web_59ba36addc2b2f9401580f014c7f58ea4e30989d')
                ->content()
        );
        $this->assertEquals(
            'BYE',
            $this->actingAs($user)
                ->withSession([
                    'login_web_59ba36addc2b2f9401580f014c7f58ea4e30989d' => $user->id_bigint,
                ])
                ->get('/local_server/server_admin/json.php?admin_action=logout')
                ->assertStatus(200)
                ->assertHeader('Content-Type', 'text/html; charset=UTF-8')
                ->assertSessionMissing('login_web_59ba36addc2b2f9401580f014c7f58ea4e30989d')
                ->content()
        );
    }

    public function testGetPermissionsUnauthenticated()
    {
        $this->assertEquals(
            'NOT AUTHORIZED',
            $this->post('/local_server/server_admin/json.php', ['admin_action' => 'get_permissions'])
            ->assertStatus(200)
            ->assertHeader('Content-Type', 'text/html; charset=UTF-8')
            ->content()
        );
    }

    public function testGetPermissionsAuthenticated()
    {
        $user = $this->createServiceBodyAdmin();
        $area1 = $this->createArea('area1', $user->id_bigint);
        $area2 = $this->createArea('area2', $user->id_bigint + 1, [$user->id_bigint]);
        $response = $this->actingAs($user)
            ->withSession([
                'login_web_59ba36addc2b2f9401580f014c7f58ea4e30989d' => $user->id_bigint,
            ])
            ->post('/local_server/server_admin/json.php', ['admin_action' => 'get_permissions'])
            ->assertStatus(200)
            ->assertHeader('Content-Type', 'application/json')
            ->json();
        $this->assertEquals(2, count($response['service_body']));
        $this->assertEquals(['id' => $area1->id_bigint, 'name' => $area1->name_string, 'permissions' => 3], $response['service_body'][0]);
        $this->assertEquals(['id' => $area2->id_bigint, 'name' => $area2->name_string, 'permissions' => 3], $response['service_body'][1]);
    }

    public function testGetUserInfoUnauthenticated()
    {
        $this->assertEquals(
            'NOT AUTHORIZED',
            $this->post('/local_server/server_admin/json.php', ['admin_action' => 'get_user_info'])
                ->assertStatus(200)
                ->assertHeader('Content-Type', 'text/html; charset=UTF-8')
                ->content()
        );
    }

    public function testGetUserInfoAuthenticated()
    {
        $user = $this->createServiceBodyAdmin();
        $response = $this->actingAs($user)
            ->withSession([
                'login_web_59ba36addc2b2f9401580f014c7f58ea4e30989d' => $user->id_bigint,
            ])
            ->post('/local_server/server_admin/json.php', ['admin_action' => 'get_user_info'])
            ->assertStatus(200)
            ->assertHeader('Content-Type', 'application/json')
            ->json();
        $expected = ['current_user' => ['id' => $user->id_bigint, 'name' => $user->name_string, 'type' => $user->user_level_tinyint]];
        $this->assertEquals($expected, $response);
    }

    public function testMigratePasswordHash()
    {
        $user = $this->createServiceBodyAdmin();
        $user->password_string = crypt($this->goodPassword, 'ab');
        $user->save();
        $data = [
            'admin_action' => 'login',
            'c_comdef_admin_login' => $user->login_string,
            'c_comdef_admin_password' => $this->goodPassword
        ];
        $this->post('/', $data)
            ->assertStatus(302)
            ->assertSessionHas('login_web_59ba36addc2b2f9401580f014c7f58ea4e30989d', $user->id_bigint);
        $oldPasswordhash = $user->password_string;
        $user->refresh();
        $this->assertNotEmpty($user->password_string);
        $this->assertNotEquals($oldPasswordhash, $user->password_string);
    }
}
