<?php

use App\Services\Users\UserService;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    $this->userService = new UserService();
});

it('can fetch and process user data from API', function () {
    $redisMock = Mockery::mock('alias:Illuminate\Support\Facades\Redis');
    $redisMock->shouldReceive('connection->hSet')->times(2)->andReturn(1);

    Http::fake([
        config('api.random-user.url') => Http::response([
            'results' => [
                generateUserData('male'),
                generateUserData('female')
            ]
        ]),
    ]);

    $result = $this->userService->fetchAndProcessUserDataFromApi();

    expect($result)->toBeTrue();
});

it('can handle failure in fetching and processing user data from API', function () {
    $redisMock = Mockery::mock('alias:Illuminate\Support\Facades\Redis');
    $redisMock->shouldReceive('connection->hSet')->times(0)->andReturn(1);
    Http::fake([
        config('api.random-user.url') => Http::response(['error' => 'Internal Server Error'], 500)
    ]);

    $result = $this->userService->fetchAndProcessUserDataFromApi();

    expect($result)->toBeFalse();
});
