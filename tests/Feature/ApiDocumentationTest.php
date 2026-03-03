<?php

namespace Tests\Feature;

use Tests\TestCase;

class ApiDocumentationTest extends TestCase
{
    public function test_docs_ui_is_accessible(): void
    {
        $response = $this->get('/docs/api');

        $response->assertOk();
        $response->assertSee('openapi', false);
    }

    public function test_docs_json_is_accessible_and_contains_paths(): void
    {
        $response = $this->get('/docs/api.json');

        $response->assertOk();

        $data = $response->json();

        $this->assertIsArray($data);
        $this->assertArrayHasKey('openapi', $data);
        $this->assertArrayHasKey('paths', $data);
        $this->assertIsArray($data['paths']);
        $this->assertNotEmpty($data['paths']);
    }
}
