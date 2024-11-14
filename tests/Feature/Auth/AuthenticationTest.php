<?php

it('returns a successful response', function () {
    $response = $this->get('/api/auth/login');

    $response->assertStatus(200);
});
