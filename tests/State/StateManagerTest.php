<?php

use Mockery as M;
use SocialNorm\State\StateManager;

class StateManagerTest extends TestCase
{
    /** @test */
    public function it_generates_random_state()
    {
        $session = M::mock('SocialNorm\State\Session')->shouldIgnoreMissing();
        $stateManager = new StateManager($session, ['state' => 'not-important']);

        $firstState = $stateManager->generateState();
        $secondState = $stateManager->generateState();
        $this->assertFalse($firstState == $secondState);
    }

    /** @test */
    public function it_persists_the_state_to_the_session()
    {
        $session = M::spy('SocialNorm\State\Session');
        $stateManager = new StateManager($session, []);

        $stateManager->generateState();
        $session->shouldHaveReceived('put');
    }

    /** @test */
    public function it_can_verify_valid_state()
    {
        $state = 'valid-state';
        $session = M::mock('SocialNorm\State\Session');
        $session->shouldReceive('get')->andReturn($state);

        $stateManager = new StateManager($session, ['state' => $state]);

        $this->assertTrue($stateManager->verifyState());
    }

    /** @test */
    public function it_can_verify_invalid_state()
    {
        $state = 'valid-state';
        $session = M::mock('SocialNorm\State\Session');
        $session->shouldReceive('get')->andReturn($state);

        $stateManager = new StateManager($session, ['state' => 'invalid-state']);

        $this->assertFalse($stateManager->verifyState());
    }

    /** @test */
    public function it_can_verify_missing_state()
    {
        $session = M::mock('SocialNorm\State\Session');

        $stateManager = new StateManager($session, []);

        $this->assertFalse($stateManager->verifyState());
    }
}
