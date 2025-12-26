<?php

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->user = User::factory()->create();
});

describe('Profile Settings', function () {
    it('shows profile settings page', function () {
        $this->actingAs($this->user)
            ->get(route('profile.edit'))
            ->assertOk()
            ->assertInertia(
                fn($page) => $page
                    ->component('settings/Profile')
            );
    });

    it('allows user to update profile', function () {
        $this->actingAs($this->user)
            ->patch(route('profile.update'), [
                'name' => 'Updated Name',
                'email' => 'updated@example.com',
            ])
            ->assertRedirect();

        $this->user->refresh();
        expect($this->user->name)->toBe('Updated Name');
    });

    it('validates email format', function () {
        $this->actingAs($this->user)
            ->patch(route('profile.update'), [
                'name' => 'Test',
                'email' => 'invalid-email',
            ])
            ->assertSessionHasErrors(['email']);
    });
});

describe('Avatar Upload', function () {
    it('allows user to upload avatar', function () {
        Storage::fake('public');

        $this->actingAs($this->user)
            ->post(route('profile.avatar.update'), [
                'avatar' => UploadedFile::fake()->image('avatar.jpg'),
            ])
            ->assertRedirect();

        $this->user->refresh();
        expect($this->user->avatar)->not->toBeNull();
    });

    it('allows user to delete avatar', function () {
        $this->user->update(['avatar' => 'avatars/test.jpg']);

        $this->actingAs($this->user)
            ->delete(route('profile.avatar.destroy'))
            ->assertRedirect();

        $this->user->refresh();
        expect($this->user->avatar)->toBeNull();
    });

    it('validates avatar is an image', function () {
        Storage::fake('public');

        $this->actingAs($this->user)
            ->post(route('profile.avatar.update'), [
                'avatar' => UploadedFile::fake()->create('document.pdf'),
            ])
            ->assertSessionHasErrors(['avatar']);
    });
});

describe('Password Settings', function () {
    it('shows password settings page', function () {
        $this->actingAs($this->user)
            ->get(route('user-password.edit'))
            ->assertOk()
            ->assertInertia(
                fn($page) => $page
                    ->component('settings/Password')
            );
    });
});

describe('Appearance Settings', function () {
    it('shows appearance settings page', function () {
        $this->actingAs($this->user)
            ->get(route('appearance.edit'))
            ->assertOk()
            ->assertInertia(
                fn($page) => $page
                    ->component('settings/Appearance')
            );
    });
});
