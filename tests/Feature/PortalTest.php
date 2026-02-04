<?php

use App\Models\Alumnus;
use App\Models\Tenure;
use App\Models\Department;

test('portal page can be rendered', function () {
    $response = $this->get('/portal');
    $response->assertStatus(200);
    $response->assertInertia(
        fn($page) => $page
            ->component('public/Portal')
            ->has('tenures')
            ->has('departments')
    );
});
