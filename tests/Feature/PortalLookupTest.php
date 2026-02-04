<?php

use App\Models\Alumnus;
use App\Models\Tenure;

test('portal lookup returns match prop', function () {
    // Arrange
    $tenure = Tenure::factory()->create();
    $alumnus = Alumnus::factory()->create([
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'tenure_id' => $tenure->id,
    ]);

    // Act
    $response = $this->from('/portal')
        ->post('/portal/lookup', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

    // Assert
    $response->assertRedirect('/portal');

    // Follow redirect to check props
    $this->get('/portal')
        ->assertInertia(
            fn($page) => $page
                ->component('public/Portal')
                ->has('flash.match')
                ->where('flash.match.id', $alumnus->id)
        );
});

test('portal lookup returns no_match prop', function () {
    // Act
    $response = $this->from('/portal')
        ->post('/portal/lookup', [
            'name' => 'Non Existent',
            'email' => 'nobody@example.com',
        ]);

    // Assert
    $response->assertRedirect('/portal');

    // Follow redirect
    $this->get('/portal')
        ->assertInertia(
            fn($page) => $page
                ->component('public/Portal')
                ->has('flash.no_match')
                ->where('flash.no_match', true)
        );
});
