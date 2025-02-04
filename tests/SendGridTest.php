<?php

namespace StephaneCoinon\SendGridActivity\Tests;

use Http\Mock\Client as MockClient;
use StephaneCoinon\SendGridActivity\SendGrid;
use StephaneCoinon\SendGridActivity\Tests\Support\Factories\ApiResponseFactory;
use StephaneCoinon\SendGridActivity\Tests\Support\Stubs\RequestStub;
use StephaneCoinon\SendGridActivity\Tests\Support\Stubs\ResponseStub;

class SendGridTest extends TestCase
{
    /** @test */
    function return_json_response_as_an_array()
    {
        $api = $this->mockApiResponse(
            $item = ['id' => 1, 'email' => 'john@example.com']
        );

        $response = $api->requestRaw('GET', '/some-endpoint');

        $this->assertEquals($item, $response);
    }

    /** @test */
    function making_a_request_from_a_request_instance_returns_response_instances()
    {
        $api = $this->mockApiResponse([
            'items' => [['id' => 1], ['id' => 2], ['id' => 3]]
        ]);

        $responses = $api->request(new RequestStub);

        $this->assertCount(3, $responses);
        $this->assertContainsOnlyInstancesOf(ResponseStub::class, $responses);
        $this->assertEquals([1, 2, 3], array_map(function ($response) {
            return $response->id;
        }, $responses));
    }

    /** @test */
    function fetching_a_fresh_resource()
    {
        $api = $this->mockApiResponses([
            ['items' => [['id' => 1]]],      // GET /items
            ['id' => 1, 'name' => 'item-1'], // GET /items/1
        ]);
        // First fetch, all the resources
        $items = $api->request(new RequestStub);
        // Pre-condition: name is not returned in first request
        $this->assertFalse(isset($items[0]->name));

        $item = $items[0]->fresh();

        $this->assertInstanceOf(ResponseStub::class, $item);
        $this->assertEquals(1, $item->id);
        $this->assertEquals('item-1', $item->name);
    }

    function mockApiResponse(array $response): SendGrid
    {
        $client = new MockClient;
        $client->addResponse((new ApiResponseFactory)->json()->build($response));

        return SendGrid::newWithClient($client);
    }

    function mockApiResponses(array $responses): SendGrid
    {
        $client = new MockClient;
        foreach ($responses as $response) {
            $client->addResponse((new ApiResponseFactory)->json()->build($response));
        }

        return SendGrid::newWithClient($client);
    }
}
