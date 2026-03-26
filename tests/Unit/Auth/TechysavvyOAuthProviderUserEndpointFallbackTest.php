<?php

namespace Tests\Unit\Auth;

use App\Services\Auth\TechysavvyOAuthProvider;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Request;
use Tests\TestCase;

class TechysavvyOAuthProviderUserEndpointFallbackTest extends TestCase
{
    public function test_it_falls_back_to_api_me_when_api_user_returns_404(): void
    {
        $history = [];

        $mock = new MockHandler([
            new Response(404, ['Content-Type' => 'application/json'], json_encode(['message' => 'Not Found'])),
            new Response(200, ['Content-Type' => 'application/json'], json_encode([
                'id' => '123',
                'name' => 'Alice',
                'email' => 'alice@example.com',
                'email_verified_at' => null,
            ])),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push(Middleware::history($history));

        $client = new Client(['handler' => $handlerStack]);

        $this->app['config']->set('services.techysavvy.server_url', 'http://auth.techysavvy.test');

        $request = Request::create('http://taskforge.test');
        $provider = new TechysavvyOAuthProvider($request, 'test-client-id', 'test-client-secret', 'http://taskforge.test/auth/callback');
        $provider->setHttpClient($client);

        $user = $provider->userFromToken('test-token');

        $this->assertSame('123', (string) $user->getId());
        $this->assertSame('alice@example.com', $user->getEmail());

        $this->assertCount(2, $history);
        $this->assertSame('/api/user', $history[0]['request']->getUri()->getPath());
        $this->assertSame('/api/me', $history[1]['request']->getUri()->getPath());
    }
}
