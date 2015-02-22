<?php

use SocialNorm\User;

class UserTest extends TestCase
{
    /** @test */
    public function it_can_set_all_properties_and_retrieve()
    {
        $user = new User([
            'access_token' => 'abc123',
            'id' => 'foobar',
            'nickname' => 'john.doe',
            'full_name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'avatar' => 'http://example.com/john-doe.jpg',
        ]);
        $this->assertEquals('abc123', $user->access_token);
        $this->assertEquals('foobar', $user->id);
        $this->assertEquals('john.doe', $user->nickname);
        $this->assertEquals('John Doe', $user->full_name);
        $this->assertEquals('john.doe@example.com', $user->email);
        $this->assertEquals('http://example.com/john-doe.jpg', $user->avatar);
    }

    /** @test */
    public function properties_not_set_return_null()
    {
        $details = new User([
            'accessToken' => 'abc123',
        ]);
        $this->assertNull($details->id);
    }

    /** @test */
    public function test_can_retrieve_raw_details()
    {
        $normalized = [
            'accessToken' => 'abc123',
        ];

        $raw = [
            'otherField' => 'foobar',
        ];

        $details = new User($normalized, $raw);
        $this->assertEquals($raw, $details->raw());
    }
}
