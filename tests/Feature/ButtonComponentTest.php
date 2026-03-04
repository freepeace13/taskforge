<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

class ButtonComponentTest extends TestCase
{
    public function test_primary_md_button_renders_expected_classes(): void
    {
        $html = Blade::render('<x-button variant="primary" size="md">Primary</x-button>');

        $this->assertStringContainsString('bg-brand-600', $html);
        $this->assertStringContainsString('text-white', $html);
        $this->assertStringContainsString('rounded-2xl', $html);
        $this->assertStringContainsString('px-4', $html);
        $this->assertStringContainsString('py-2', $html);
        $this->assertStringContainsString('text-sm', $html);
    }

    public function test_secondary_md_button_renders_expected_classes(): void
    {
        $html = Blade::render('<x-button variant="secondary" size="md">Secondary</x-button>');

        $this->assertStringContainsString('border-gray-200', $html);
        $this->assertStringContainsString('bg-white', $html);
        $this->assertStringContainsString('text-gray-900', $html);
    }

    public function test_sm_and_lg_sizes_apply_correct_padding_and_font_sizes(): void
    {
        $small = Blade::render('<x-button variant="primary" size="sm">Small</x-button>');
        $large = Blade::render('<x-button variant="primary" size="lg">Large</x-button>');

        $this->assertStringContainsString('px-3', $small);
        $this->assertStringContainsString('py-1.5', $small);
        $this->assertStringContainsString('text-xs', $small);

        $this->assertStringContainsString('px-5', $large);
        $this->assertStringContainsString('py-3', $large);
        $this->assertStringContainsString('text-base', $large);
    }
}
