<?php

// Pest style
test('the application return a successfull response', function () {
    $response = $this->get('/');
    $response->assertStatus(200);
}) ;