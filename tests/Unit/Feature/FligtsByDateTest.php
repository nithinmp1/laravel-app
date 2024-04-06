<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FligtsByDateTest extends TestCase
{
    use RefreshDatabase;

    public function testGetApi()
    {
        $response = $this->get('http://0.0.0.0:1234/fligts?date=11 Jan 2022'); 

        $response->assertStatus(200);
        $response->assertJson(['status' => true]);
        $response->assertJsonStructure(['data']);
        $responseData = $response->json();
    }

    public function testFetchFlight()
    {
        $postData = [
            'airportCode' => 'CPH'
        ];
        $response = $this->postJson('http://0.0.0.0:1234/fligts', $postData);
        $response->assertStatus(200);
        $response->assertJson(['status' => true]);
        $response->assertJsonStructure(['data']);
        $responseData = $response->json();
    }

    public function testSBYEvents()
    {
        $response = $this->get('http://0.0.0.0:1234/SBYEvents?date=11 Jan 2022'); 

        $response->assertStatus(200);
        $response->assertJson(['status' => true]);
        $response->assertJsonStructure(['data']);
        $responseData = $response->json();
    }

    public function testRoasterFilter()
    {
        $postData = [
            'fromDate' => 12,
            'toDate' => 14
        ];
        $response = $this->postJson('http://0.0.0.0:1234/roasterFilter', $postData);

        $response->assertStatus(200);
        $response->assertJson(['status' => true]);
        $response->assertJsonStructure(['data']);
        $responseData = $response->json();
    }

    public function testRosters()
    {
        $response = $this->get('http://0.0.0.0:1234/rosters'); 

        $response->assertStatus(200);
        $response->assertJson(['status' => true]);
        $response->assertJsonStructure(['data']);
        $responseData = $response->json();
    }
}
