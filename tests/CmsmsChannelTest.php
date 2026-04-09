<?php

namespace NotificationChannels\Cmsms\Test;

use GuzzleHttp\Client;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;
use Mockery;
use NotificationChannels\Cmsms\CmsmsChannel;
use NotificationChannels\Cmsms\CmsmsClient;
use NotificationChannels\Cmsms\CmsmsMessage;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class CmsmsChannelTest extends TestCase
{
    protected static $latestResponse;

    private TestNotification $notification;
    private TestNotifiable $notifiable;
    private Client $guzzle;
    private CmsmsClient $client;
    private CmsmsChannel $channel;

    public function setUp(): void
    {
        parent::setUp();

        $this->notification = new TestNotification();
        $this->notifiable = new TestNotifiable();
        $this->guzzle = Mockery::mock(new Client());
        $this->client = Mockery::mock(new CmsmsClient($this->guzzle, '00000FFF-0000-F0F0-F0F0-FFFFFFFFFFFF'));
        $this->channel = new CmsmsChannel($this->client);
    }

    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function it_can_be_instantiated()
    {
        $this->assertInstanceOf(CmsmsClient::class, $this->client);
        $this->assertInstanceOf(CmsmsChannel::class, $this->channel);
    }

    #[Test]
    #[DoesNotPerformAssertions]
    public function it_shares_message()
    {
        $this->client->shouldReceive('send')->once();
        $this->channel->send($this->notifiable, $this->notification);
    }
}

class TestNotifiable
{
    use Notifiable;

    public function routeNotificationForCmsms()
    {
        return '0031612345678';
    }
}

class TestNotification extends Notification
{
    public function toCmsms($notifiable)
    {
        return CmsmsMessage::create('Message content')->originator('APPNAME');
    }
}
