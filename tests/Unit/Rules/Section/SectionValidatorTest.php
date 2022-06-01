<?php

namespace Tests\Unit\Rules\Section;

use App\Models\Section;
use App\Rules\Section\SectionHeadingTypeValidator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SectionValidatorTest extends TestCase
{
    use RefreshDatabase;

    public function testHasChanges()
    {
        $section = factory(Section::class)->create();
        $section->heading = 2;
        $validator = new SectionHeadingTypeValidator($section);
        $this->assertTrue($validator->hasChanges('heading', 1));
        $this->assertFalse($validator->hasChanges('heading', 2));
    }
}
