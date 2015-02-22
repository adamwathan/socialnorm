<?php

use Mockery as M;
use SocialNorm\State\StateManager;

class StateManagerTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        M::close();
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
