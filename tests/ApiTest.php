<?php

namespace tests\Clearcode\EHLibrarySandbox;

use Symfony\Component\HttpFoundation\Response;

class ApiTest extends WebTestCase
{
    /** @test */
    public function it_renders_homepage()
    {
        $this->visitRoute('GET', 'homepage');

        $this->assertThatResponseHasStatus(Response::HTTP_OK);
    }
}
