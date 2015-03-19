<?php

use SocialNorm\StateGenerator;

class StateGeneratorTest extends TestCase
{
    /** @test */
    public function it_generates_random_state()
    {
        $stateGenerator = new StateGenerator;

        $firstState = $stateGenerator->generate();
        $secondState = $stateGenerator->generate();
        $this->assertFalse($firstState == $secondState);
    }

    /** @test */
    public function it_generates_32_character_long_states_by_default()
    {
        $stateGenerator = new StateGenerator;

        $state = $stateGenerator->generate();
        $this->assertEquals(32, strlen($state));
    }

    /** @test */
    public function it_can_generate_state_of_an_arbitrary_length()
    {
        $stateGenerator = new StateGenerator;

        $state = $stateGenerator->generate(16);
        $this->assertEquals(16, strlen($state));
    }
}
