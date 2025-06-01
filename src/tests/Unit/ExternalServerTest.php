<?php

namespace Tests\Unit;

use App\Models\Server;
use App\Repositories\External\ExternalServer;
use App\Repositories\External\InvalidServerException;
use Illuminate\Database\Eloquent\Model;
use PHPUnit\Framework\TestCase;

class ExternalServerTest extends TestCase
{
    private function validValues(): array
    {
        return [
            'id' => 1,
            'name' => 'test',
            'url' => 'https://blah.com/blah',
        ];
    }

    private function getModel(array $validValues): Server
    {
        return new Server(['source_id' => $validValues['id'], 'name' => $validValues['name'], 'url' => $validValues['url']]);
    }

    public function testValid()
    {
        $values = $this->validValues();
        $server = new ExternalServer($values);
        $this->assertEquals($values['id'], $server->id);
        $this->assertEquals($values['name'], $server->name);
        $this->assertEquals($values['url'], $server->url);
    }

    public function testMissingId()
    {
        $this->expectException(InvalidServerException::class);
        $values = $this->validValues();
        unset($values['id']);
        new ExternalServer($values);
    }

    public function testInvalidId()
    {
        $this->expectException(InvalidServerException::class);
        $values = $this->validValues();
        $values['id'] = 'string';
        new ExternalServer($values);
    }

    public function testMissingName()
    {
        $this->expectException(InvalidServerException::class);
        $values = $this->validValues();
        unset($values['name']);
        new ExternalServer($values);
    }

    public function testInvalidName()
    {
        $this->expectException(InvalidServerException::class);
        $values = $this->validValues();
        $values['name'] = 123;
        new ExternalServer($values);
    }

    public function testMissingUrl()
    {
        $this->expectException(InvalidServerException::class);
        $values = $this->validValues();
        unset($values['url']);
        new ExternalServer($values);
    }

    public function testInvalidUrl()
    {
        $this->expectException(InvalidServerException::class);
        $values = $this->validValues();
        $values['url'] = 'string';
        new ExternalServer($values);
    }

    // isEqual
    //
    //
    public function testNoDifferences()
    {
        $values = $this->validValues();
        $external = new ExternalServer($values);
        $db = $this->getModel($values);
        $this->assertTrue($external->isEqual($db));
    }

    public function testId()
    {
        $values = $this->validValues();
        $external = new ExternalServer($values);
        $db = $this->getModel($values);
        $db->source_id = 999;
        $this->assertFalse($external->isEqual($db));
    }

    public function testName()
    {
        $values = $this->validValues();
        $external = new ExternalServer($values);
        $db = $this->getModel($values);
        $db->name = 'some name';
        $this->assertFalse($external->isEqual($db));
    }

    public function testUrl()
    {
        $values = $this->validValues();
        $external = new ExternalServer($values);
        $db = $this->getModel($values);
        $db->url = 'https://adifferenturl';
        $this->assertFalse($external->isEqual($db));
    }
}
