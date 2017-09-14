<?php

namespace ElfSundae\BearyChat\Laravel\Jobs;

use Illuminate\Bus\Queueable;
use ElfSundae\BearyChat\Client;
use ElfSundae\BearyChat\Message;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

/**
 * Queueable BearyChat Job for Laravel 5.4 or later.
 *
 * @see https://laravel.com/docs/queues
 */
class SendBearyChat implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The BearyChat client.
     *
     * @var \ElfSundae\BearyChat\Client
     */
    protected $client;

    /**
     * The Message instance to be sent.
     *
     * @var \ElfSundae\BearyChat\Message
     */
    protected $message;

    /**
     * Create a new job instance.
     *
     * @param  mixed  $message
     */
    public function __construct($message = null)
    {
        if ($message instanceof Message) {
            $this->message = $message;
        } elseif (! is_null($message)) {
            call_user_func_array([$this, 'content'], func_get_args());
        }
    }

    /**
     * Set the BearyChat client.
     *
     * @param  \ElfSundae\BearyChat\Client|string  $client
     * @return $this
     */
    public function client($client)
    {
        $this->client = $client instanceof Client ? $client : bearychat($client);

        if ($this->message) {
            $this->message->setClient($this->client);
        }

        return $this;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (! $this->message->getClient()) {
            $this->message->setClient($this->client ?: bearychat());
        }

        $this->message->send();
    }

    /**
     * Any unhandled methods will be sent to the Message instance.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return $this
     */
    public function __call($method, $parameters)
    {
        if (! $this->message) {
            $this->message = new Message($this->client ?: bearychat());
        }

        call_user_func_array([$this->message, $method], $parameters);

        return $this;
    }
}
