<?php

namespace Feature;

use LaravelDomainOriented\Tests\TestCase;

class ControllerTest extends TestCase
{
    /** @test **/
    public function shouldGetAOkStatusResponseAndEmptyBody()
    {
        $response = $this->get('test');
        $response->assertJson([]);
        $response->assertOk();
    }
}
