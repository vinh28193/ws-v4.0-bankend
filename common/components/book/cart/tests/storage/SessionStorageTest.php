<?php

namespace common\components\book\cart\tests\storage;

use common\components\book\cart\storage\SessionStorage;
use common\components\book\cart\tests\TestCase;

class SessionStorageTest extends TestCase
{
    /**
     * @var SessionStorage
     */
    private $storage;

    public function setUp()
    {
        parent::setUp();
        $this->storage = new SessionStorage(['key' => 'test']);
    }

    public function testEmpty()
    {
        $this->assertEquals([], $this->storage->load());
    }

    public function testStore()
    {
        $this->storage->save($items = [1 => 5, 6 => 12]);

        $this->assertEquals($items, $this->storage->load());
    }
}
