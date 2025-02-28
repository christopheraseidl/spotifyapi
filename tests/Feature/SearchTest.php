<?php

use App\Models\User;

beforeEach(function () {
    $user = User::factory()->create();
    $this->token = $user->createToken('test-token')->plainTextToken;
});

it('returns a paginted list of search results', function () {
    $searchTerm = 'Wu-Tang Clan';
    $searchData = [
        'q' => $searchTerm,
        'type' => 'artist',
    ];

    $response = $this->withHeaders([
        'Accept' => 'application/json',
        'Authorization' => 'Bearer '.$this->token,
    ])->post('api/v1/search', $searchData);

    $data = json_decode($response->getContent())->data;
    $nextPageUrl = json_decode($response->getContent())->next_page_url;
    $firstResult = $data[0];

    expect($firstResult->name)->toBe($searchTerm)
        ->and($nextPageUrl)->toEndWith('/api/v1/search?page=2');
});

it('correctly advances offset based on current page', function () {
    $searchTerm = 'Enter The Wu-Tang';
    $searchData = [
        'q' => $searchTerm,
        'type' => 'album',
        'page' => 2,
    ];

    $response = $this->withHeaders([
        'Accept' => 'application/json',
        'Authorization' => 'Bearer '.$this->token,
    ])->post('api/v1/search', $searchData);

    $data = json_decode($response->getContent())->data;
    $previousPageUrl = json_decode($response->getContent())->prev_page_url;
    $nextPageUrl = json_decode($response->getContent())->next_page_url;
    $firstResult = $data[0];

    expect($firstResult->name)->not->toBe($searchTerm)
        ->and($previousPageUrl)->toEndWith('/api/v1/search?page=1')
        ->and($nextPageUrl)->toEndWith('/api/v1/search?page=3');
});

it('will not perform a search without authentication and returns an error code', function () {
    $searchTerm = 'Wu-Tang Clan';
    $searchData = [
        'q' => $searchTerm,
        'type' => 'artist',
    ];

    $response = $this->withHeaders([
        'Accept' => 'application/json',
    ])->post('api/v1/search', $searchData);

    $data = json_decode($response->getContent());
    $message = $data->message;

    expect($message)->toBe('Unauthenticated.')
        ->and($response->getStatusCode())->toBe(401);
});

it('will not search without valid data and returns an error code', function () {
    $searchData = [];
    $response = $this->withHeaders([
        'Accept' => 'application/json',
        'Authorization' => 'Bearer '.$this->token,
    ])->post('api/v1/search', $searchData);

    $data = json_decode($response->getContent());
    $errors = $data->errors;

    expect($response->getStatusCode())->toBe(422)
        ->and($errors->q[0])->toBe('The q field is required.')
        ->and($errors->type[0])->toBe('The type field is required.');
});
