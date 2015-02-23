<?php

use Mockery as M;
use SocialNorm\State\StateManager;
use SocialNorm\Request;
use SocialNorm\State\StateGenerator;

class StateManagerTest extends TestCase
{
    /** @test */
    public function it_persists_the_state_to_the_session()
    {
        $session = M::spy('SocialNorm\State\Session');
        $stateManager = new StateManager($session, new Request([]), new StateGenerator);

        $state = $stateManager->generateState();
        $session->shouldHaveReceived('put');
    }

    /** @test */
    public function it_can_verify_valid_state()
    {
        $state = 'valid-state';
        $session = M::mock('SocialNorm\State\Session');
        $session->shouldReceive('get')->andReturn($state);

        $stateManager = new StateManager($session, new Request(['state' => $state]), new StateGenerator);

        $this->assertTrue($stateManager->verifyState());
    }

    /** @test */
    public function it_can_verify_invalid_state()
    {
        $state = 'valid-state';
        $session = M::mock('SocialNorm\State\Session');
        $session->shouldReceive('get')->andReturn($state);

        $stateManager = new StateManager($session, new Request(['state' => 'invalid-state']), new StateGenerator);

        $this->assertFalse($stateManager->verifyState());
    }

    /** @test */
    public function it_can_verify_missing_state()
    {
        $session = M::mock('SocialNorm\State\Session')->shouldIgnoreMissing();

        $stateManager = new StateManager($session, new Request([]), new StateGenerator);

        $this->assertFalse($stateManager->verifyState());
    }
}
