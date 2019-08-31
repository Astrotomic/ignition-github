<?php

namespace Astrotomic\IgnitionGithubTab\Tests;

use Astrotomic\IgnitionGithubTab\Tab;

final class TabTest extends TestCase
{
    /** @test */
    public function it_has_the_correct_name(): void
    {
        $tab = new Tab();

        $this->assertEquals('Stack Overflow', $tab->name());
    }

    /** @test */
    public function it_has_the_correct_component(): void
    {
        $tab = new Tab();

        $this->assertEquals('ignition-github', $tab->component());
    }

    /** @test */
    public function it_has_the_correct_scripts(): void
    {
        $tab = new Tab();

        $this->assertArrayHasKey('ignition-github', $tab->scripts);
        $this->assertStringEndsWith('dist/js/tab.js', $tab->scripts['ignition-github']);
    }

    /** @test */
    public function it_has_no_styles(): void
    {
        $tab = new Tab();

        $this->assertEmpty($tab->styles);
    }
}
