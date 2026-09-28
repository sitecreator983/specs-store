<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PcBuilderTest extends TestCase
{
    use RefreshDatabase;

    public function test_pc_builder_page_returns_successful_response(): void
    {
        $this->get('/pc-builder')->assertOk();
    }

    public function test_pc_builder_endpoint_returns_grouped_component_products(): void
    {
        $this->seed();

        $response = $this->getJson('/api/pc-builder/products');

        $response
            ->assertOk()
            ->assertJsonStructure([
                'products' => [
                    'cpu',
                    'motherboard',
                    'ram',
                    'gpu',
                    'storage',
                    'case',
                    'psu',
                    'fans',
                ],
            ])
            ->assertJsonPath('products.cpu.0.specs.socket', 'AM5')
            ->assertJsonPath('products.motherboard.0.specs.memory_type', 'DDR5');

        $this->assertNotEmpty($response->json('products.case.0.specs.motherboard_support'));
        $this->assertNotEmpty($response->json('products.psu.0.specs.wattage'));
    }
}
